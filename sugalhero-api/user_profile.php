<?php
// sugalhero-api/user_profile.php

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

// Connect to database
$conn = getDbConnection();

try {
    // Fetch user profile data
    $stmt = $conn->prepare("SELECT username, email, currency_balance FROM users WHERE id = ?");
    if ($stmt === false) {
        throw new Exception($conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userProfile = $result->fetch_assoc();
    $stmt->close();

    if (!$userProfile) {
        // This case should ideally not be hit if authentication worked correctly,
        // but it's a good safeguard for a user ID that somehow doesn't exist.
        sendJsonResponse(['success' => false, 'message' => 'User profile not found.'], 404);
    }

    // Format balance for consistent output
    $userProfile['currency_balance'] = (float)$userProfile['currency_balance'];

    // Send successful response with profile data
    sendJsonResponse([
        'success' => true,
        'message' => 'User profile fetched successfully.',
        'profile' => $userProfile
    ], 200);

} catch (Exception $e) {
    error_log("Failed to fetch user profile for user " . $userId . ": " . $e->getMessage());
    sendJsonResponse(['success' => false, 'message' => 'Failed to fetch user profile: ' . $e->getMessage()], 500);
} finally {
    $conn->close();
}
?>