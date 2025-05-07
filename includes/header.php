<?php 
require_once 'config.php';
$current_page = basename($_SERVER['PHP_SELF']);

// Debug session information
error_log("Current page: " . $current_page);
error_log("Session status: " . session_status());
error_log("Session ID: " . session_id());
if (isset($_SESSION['user_id'])) {
    error_log("User ID in session: " . $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Here - Food Donation Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        :root {
            --primary-color: #3B82F6;
            --primary-dark: #2563EB;
            --primary-light: #DBEAFE;
            --accent-color: #1E40AF;
            --text-dark: #1F2937;
            --text-light: #F9FAFB;
            --gray-light: #F3F4F6;
            --gray-medium: #E5E7EB;
            --gray-dark: #4B5563;
        }
        .primary-color {
            color: var(--primary-color);
        }
        .primary-bg {
            background-color: var(--primary-color);
        }
        .secondary-color {
            color: var(--accent-color);
        }
        .secondary-bg {
            background-color: var(--accent-color);
        }
        .nav-link {
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-light);
            padding: 0.5rem 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nav-link:hover {
            color: var(--primary-light);
        }
        .active-nav {
            border-bottom: 2px solid var(--primary-light);
            font-weight: bold;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: 1px solid var(--primary-light);
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        .card {
            transition: all 0.3s ease;
            border: 1px solid var(--gray-medium);
            background-color: white;
        }
        .card:hover {
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        .slider-container {
            overflow: hidden;
            position: relative;
        }
        .slider {
            display: flex;
            transition: transform 0.5s ease;
        }
        .slide {
            min-width: 100%;
            position: relative;
        }
        .site-header {
            background-color: var(--primary-color);
            border-bottom: 3px solid var(--primary-light);
        }
        .site-footer {
            background-color: var(--primary-color);
            border-top: 3px solid var(--primary-light);
        }
        .logo-img {
            height: 50px;
            margin-right: 15px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <header>
        <!-- Site Header -->
        <div class="site-header py-4">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <a href="index.php">
                            <img src="https://cdn-icons-png.flaticon.com/512/3448/3448609.png" class="logo-img" alt="Donate Here Logo">
                        </a>
                        <div class="text-center md:text-left">
                            <a href="index.php" class="text-3xl font-bold text-white no-underline">DONATE HERE</a>
                            <p class="text-sm text-gray-100">Food Donation Platform</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="relative mr-4">
                            <input type="text" placeholder="Search..." class="bg-white text-gray-800 px-4 py-2 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            <button class="absolute right-2 top-2">
                                <i class="fas fa-search text-gray-500"></i>
                            </button>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="relative" id="account-dropdown">
                                <button id="account-dropdown-button" class="flex items-center space-x-1 focus:outline-none text-white px-3 py-1 border border-blue-400 rounded-md bg-blue-600 hover:bg-blue-700">
                                    <span>My Account</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="account-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                                    <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Dashboard</a>
                                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Profile</a>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Logout</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="btn-primary px-4 py-2 rounded-md text-white">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <nav class="bg-blue-800 shadow-md">
            <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
    <div class="hidden md:flex items-center space-x-4">
        <a href="index.php" class="nav-link px-9 py-3 block <?php echo ($current_page == 'index.php') ? 'active-nav' : ''; ?>">Home</a>
        <a href="donations.php" class="nav-link px-4 py-3 block <?php echo ($current_page == 'donations.php') ? 'active-nav' : ''; ?>">Donations</a>
        <a href="donate.php" class="nav-link px-4 py-3 block <?php echo ($current_page == 'donate.php') ? 'active-nav' : ''; ?>">Donate Food</a>
        <a href="aboutus.php" class="nav-link px-4 py-3 block <?php echo ($current_page == 'aboutus.php') ? 'active-nav' : ''; ?>">About Us</a>
    </div>
</div>

                    <div class="md:hidden flex items-center py-3">
                        <button id="mobile-menu-button" class="focus:outline-none text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="mobile-menu" class="hidden md:hidden bg-blue-700">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="index.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'index.php') ? 'bg-blue-900' : ''; ?>">Home</a>
                    <a href="donations.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'donations.php') ? 'bg-blue-900' : ''; ?>">Donations</a>
                    <a href="donate.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'donate.php') ? 'bg-blue-900' : ''; ?>">Donate Food</a>
                    <a href="aboutus.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'aboutus.php') ? 'bg-blue-900' : ''; ?>">About Us</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="block px-3 py-2 rounded-md text-white">Dashboard</a>
                        <a href="profile.php" class="block px-3 py-2 rounded-md text-white">Profile</a>
                        <a href="logout.php" class="block px-3 py-2 rounded-md text-white">Logout</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <script>
                // Mobile menu toggle
                document.getElementById('mobile-menu-button').addEventListener('click', function() {
                    const mobileMenu = document.getElementById('mobile-menu');
                    mobileMenu.classList.toggle('hidden');
                });
                
                // Account dropdown toggle
                const accountDropdownButton = document.getElementById('account-dropdown-button');
                const accountDropdownMenu = document.getElementById('account-dropdown-menu');
                
                if (accountDropdownButton) {
                    accountDropdownButton.addEventListener('click', function(e) {
                        e.stopPropagation();
                        accountDropdownMenu.classList.toggle('hidden');
                    });
                    
                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!document.getElementById('account-dropdown').contains(e.target)) {
                            accountDropdownMenu.classList.add('hidden');
                        }
                    });
                }
            </script>
        </nav>
        
        <?php if ($current_page != 'login.php' && $current_page != 'signup.php'): ?>
        <div class="primary-bg text-white py-3 border-t border-b border-blue-400">
            <div class="container mx-auto px-4">
                <p class="text-center text-lg font-bold">Help us save food and feed the hungry!</p>
            </div>
        </div>
        <?php endif; ?>
    </header>
    <main class="container mx-auto px-4 py-6">