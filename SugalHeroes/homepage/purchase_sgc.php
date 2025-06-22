<?php
// homepage/purchase_sgc.php

// This page allows users to purchase SugalCoin (SGC).
// Actual payment processing will be handled by a backend API.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Purchase SGC - SUGALHEROES</title>
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Link ONLY to the comprehensive purchase-sgc.css -->
    <link rel="stylesheet" href="purchase-sgc.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar - Consistent with homepage -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../images/logo.png" alt="Your Brand Logo" id="sidebarLogo">
            </div>
            <nav class="main-nav">
                <ul>
                    <!-- Nav items, updated to link correctly from this page -->
                    <li><a href="index.php#home" class="nav-item"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="index.php#games-section" class="nav-item"><i class="fas fa-gamepad"></i> Games</a></li>
                    <li><a href="../dashboard/user_dashboard.php" class="nav-item"><i class="fas fa-clipboard-list"></i> Dashboard</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <!-- Header - Consistent with homepage -->
            <header class="header">
                <div class="left">
                    <!-- Home Button - Similar to dashboard, leads back to homepage -->
                    <a href="index.php" class="home-button">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </div>
                
                <div class="right-header-actions">
                    <!-- Crypto Wallet Display - display only, not clickable on this page -->
                    <div class="crypto-wallet-display">
                        <span id="sugalCoinBalance">0 SGC</span> 
                    </div>

                    <!-- User Account Dropdown - Consistent with homepage -->
                    <div class="user-account-dropdown">
                        <button class="dropdown-toggle" id="userDropdownToggle">
                            <i class="fas fa-user-circle"></i> <span id="headerUsername">Username</span>
                        </button>
                        <div class="dropdown-menu" id="myDropdown">
                            <div class="account-balance" id="userBalanceDisplay">Balance: $0.00</div>
                            <button class="top-up-button" id="topUpBtn">Top Up</button>
                            <button class="logout-button" id="logoutBtn">Log Out</button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content for Purchase SGC -->
            <section class="dashboard-widget" id="purchase-sgc-page-content">
                <div class="hero-content-wrapper">
                    <h1 class="page-title">SUGALHEROES</h1> <!-- Custom class for giant page title -->
                    <p class="purchase-info">Select the amount of SugalCoin (SGC) you wish to purchase.</p>

                    <div class="purchase-options-grid">
                        <!-- Purchase Option 1 -->
                        <div class="purchase-card">
                            <span class="sgc-amount">100 SGC</span>
                            <span class="price">($10,000.00)</span>
                            <button class="buy-sgc-btn" data-sgc-amount="100">Buy Now</button>
                        </div>
                        <!-- Purchase Option 2 -->
                        <div class="purchase-card">
                            <span class="sgc-amount">250 SGC</span>
                            <span class="price">($25,000.00)</span>
                            <button class="buy-sgc-btn" data-sgc-amount="250">Buy Now</button>
                        </div>
                        <!-- Purchase Option 3 -->
                        <div class="purchase-card">
                            <span class="sgc-amount">500 SGC</span>
                            <span class="price">($50,000.00)</span>
                            <button class="buy-sgc-btn" data-sgc-amount="500">Buy Now</button>
                        </div>
                        <!-- Purchase Option 4 -->
                        <div class="purchase-card">
                            <span class="sgc-amount">1,000 SGC</span>
                            <span class="price">($100,000.00)</span>
                            <button class="buy-sgc-btn" data-sgc-amount="1000">Buy Now</button>
                        </div>
                        <!-- Purchase Option 5 -->
                        <div class="purchase-card">
                            <span class="sgc-amount">2,500 SGC</span>
                            <span class="price">($250,000.00)</span>
                            <button class="buy-sgc-btn" data-sgc-amount="2500">Buy Now</button>
                        </div>
                        <!-- Purchase Option 6 -->
                        <div class="purchase-card">
                            <span class="sgc-amount">5,000 SGC</span>
                            <span class="price">($500,000.00)</span>
                            <button class="buy-sgc-btn" data-sgc-amount="5000">Buy Now</button>
                        </div>
                    </div>
                    <p id="purchaseSGC_message" class="modal-message" style="margin-top: 25px;"></p> 
                    <p style="font-size: 0.85em; color: var(--text-color); margin-top: 15px; text-align: center;">
                        *Note: Purchases are simulated for frontend. Actual token delivery depends on backend payment confirmation.*
                    </p>
                </div>
            </section>

        </main>
    </div>

    <!-- Existing Top Up Modal (Fiat) - Include if you use it on this page -->
    <div id="topUpModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeTopUpModal">&times;</span>
            <h2>Deposit Funds ($)</h2>
            <div class="form-group">
                <label for="modalDepositAmount">Amount to Deposit ($)</label>
                <input type="number" id="modalDepositAmount" value="50.00" min="0.01" step="0.01">
            </div>
            <button id="confirmDepositBtn">Confirm Deposit</button>
            <p id="modalMessage" class="modal-message"></p>
        </div>
    </div>


    <!-- Web3.js library -->
    <script src="https://cdn.jsdelivr.net/npm/web3@1.7.0/dist/web3.min.js"></script>
    <!-- FIX: Your crypto-wallet.js script must be loaded BEFORE page-specific script -->
    <script src="crypto-wallet.js"></script>
    
    <!-- Page-specific JavaScript for purchase_sgc.php -->
    <script>
        // API base URL - Kept for handlePurchaseSGC if it makes API calls
        const API_BASE_URL = 'http://localhost/sugalhero-api'; // Adjust if your API folder name is different

        // This function should be present on this page to handle purchase button clicks
        async function handlePurchaseSGC(event) {
            const amount = event.target.dataset.sgcAmount;
            const purchaseSGCMessage = document.getElementById('purchaseSGC_message');

            if (!amount) {
                purchaseSGCMessage.textContent = "Error: Amount not specified.";
                purchaseSGCMessage.className = 'modal-message error';
                return;
            }

            purchaseSGCMessage.textContent = `Processing purchase of ${amount} SGC... (Simulated)`;
            purchaseSGCMessage.className = 'modal-message info';
            console.log(`Simulating purchase of ${amount} SGC.`);

            setTimeout(() => {
                purchaseSGCMessage.textContent = `Purchase of ${amount} SGC simulated successfully! Check your MetaMask wallet for tokens.`;
                purchaseSGCMessage.className = 'modal-message success';
                // Trigger a refresh of the SGC balance on the page (from crypto-wallet.js)
                if (typeof fetchTokenBalance === 'function') { // fetchTokenBalance is now in crypto-wallet.js
                    fetchTokenBalance(); 
                }
            }, 2000); 
        }

        // --- Logic for Fiat Top Up Modal on this page (if you keep it here) ---
        async function handleFiatDeposit() {
            const modalMessage = document.getElementById('modalMessage');
            modalMessage.textContent = "Fiat deposit not implemented on this page yet.";
            modalMessage.className = 'modal-message info';
            console.warn("Fiat deposit clicked on SGC purchase page - logic missing.");
        }


        document.addEventListener('DOMContentLoaded', async () => {
            // User dropdown and logout logic - moved outside login check
            const userDropdownToggle = document.getElementById('userDropdownToggle');
            const myDropdown = document.getElementById('myDropdown');
            const logoutBtn = document.getElementById('logoutBtn');
            
            if (userDropdownToggle && myDropdown) {
                userDropdownToggle.addEventListener('click', () => {
                    myDropdown.classList.toggle('show');
                });
                window.onclick = function(event) {
                    if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-toggle')) {
                        if (myDropdown.classList.contains('show')) {
                            myDropdown.classList.remove('show');
                        }
                    }
                };
            }

            if (logoutBtn) {
                logoutBtn.addEventListener('click', () => {
                    localStorage.removeItem('jwt_token');
                    localStorage.removeItem('user_id');
                    localStorage.removeItem('username');
                    window.location.href = '../authentication/login.php';
                });
            }

            // Fiat Top Up Modal setup
            const topUpBtn = document.getElementById('topUpBtn'); 
            const topUpModal = document.getElementById('topUpModal'); 
            const closeTopUpModalBtn = document.getElementById('closeTopUpModal');
            const confirmDepositBtn = document.getElementById('confirmDepositBtn');

            if (topUpBtn && topUpModal) {
                topUpBtn.addEventListener('click', () => {
                    topUpModal.style.display = 'flex';
                    document.getElementById('modalDepositAmount').value = '50.00';
                    document.getElementById('modalMessage').textContent = ''; 
                });
            }
            if(closeTopUpModalBtn) {
                closeTopUpModalBtn.addEventListener('click', () => {
                    topUpModal.style.display = 'none';
                });
            }
            if(topUpModal) {
                window.addEventListener('click', (event) => {
                    if (event.target == topUpModal) {
                        topUpModal.style.display = 'none';
                    }
                });
            }
            if(confirmDepositBtn) { confirmDepositBtn.addEventListener('click', handleFiatDeposit); }


            // --- Add event listeners for the new purchase buttons ---
            const buySGCButtons = document.querySelectorAll('.buy-sgc-btn');
            buySGCButtons.forEach(button => {
                button.addEventListener('click', handlePurchaseSGC);
            });

            // Basic Session Check for this page - now simplified
            const jwtToken = localStorage.getItem('jwt_token');
            const username = localStorage.getItem('username'); // Get username for display
            const userId = localStorage.getItem('user_id'); // Get userId if needed for API calls

            if (!jwtToken || !username || !userId) { // Check all necessary login credentials
                window.location.href = '../authentication/login.php'; 
                return; // Stop further script execution if not authenticated
            } else {
                // If logged in, update username and fiat balance
                currentUserToken = jwtToken; // Set global token for API calls (now in crypto-wallet.js)
                document.getElementById('headerUsername').textContent = username; // Update username display
                await updateBalanceDisplay(); // Call update for fiat (now in crypto-wallet.js)
            }
        });
    </script>
</body>
</html>