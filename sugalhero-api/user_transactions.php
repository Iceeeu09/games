<?php
// Include necessary files
require_once __DIR__ . '/vendor/autoload.php'; // Composer autoloader
require_once __DIR__ . '/config.php';          // Configuration
require_once __DIR__ . '/database.php';        // Database connection
require_once __DIR__ . '/auth_helper.php';     // JWT functions

// Set content type to JSON and handle CORS

// Ensure it's a GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request method. Expected GET.'], 405);
}

// Validate JWT token and get user ID
$userId = validateJwtToken(); // This function will exit if token is invalid/missing

// Get pagination parameters (optional, but good for real apps)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default 10 transactions per page
$offset = ($page - 1) * $limit;

// Connect to database
$conn = getDbConnection();

try {
    // Get total number of transactions for pagination info
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM transactions WHERE user_id = ?");
    if ($stmt === false) {
        throw new Exception($conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $totalResult = $stmt->get_result();
    $totalTransactions = $totalResult->fetch_assoc()['total'];
    $stmt->close();

    // Fetch transactions for the user, ordered by most recent
    // --- MODIFIED QUERY TO JOIN WITH 'games' TABLE ---
    $stmt = $conn->prepare(
        "SELECT t.id, t.type, t.amount, t.description, t.status, t.created_at, t.reference_id,
                g.game_name  -- Fetch game_name from the games table
         FROM transactions t
         LEFT JOIN games g ON t.game_id = g.id  -- Join on game_id
         WHERE t.user_id = ?
         ORDER BY t.created_at DESC
         LIMIT ? OFFSET ?"
    );
    if ($stmt === false) {
        throw new Exception($conn->error);
    }
    $stmt->bind_param("iii", $userId, $limit, $offset); // user_id, limit, offset are integers
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    $stmt->close();

    sendJsonResponse([
        'success' => true,
        'message' => 'Transactions fetched successfully.',
        'currentPage' => $page,
        'limit' => $limit,
        'totalTransactions' => $totalTransactions,
        'totalPages' => ceil($totalTransactions / $limit),
        'transactions' => $transactions
    ], 200);

} catch (Exception $e) {
    error_log("Failed to fetch transactions for user " . $userId . ": " . $e->getMessage());
    sendJsonResponse(['success' => false, 'message' => 'Failed to fetch transactions: ' . $e->getMessage()], 500);
} finally {
    $conn->close();
}
?>