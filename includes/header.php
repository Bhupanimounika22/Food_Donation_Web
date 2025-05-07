<?php 
require_once 'config.php';
$current_page = basename($_SERVER['PHP_SELF']);
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
            font-family: 'Times New Roman', Times, serif;
            background-color: #f0f0f5;
        }
        .primary-color {
            color: #2D1152;
        }
        .primary-bg {
            background-color: #2D1152;
        }
        .secondary-color {
            color: #4A1D80;
        }
        .secondary-bg {
            background-color: #4A1D80;
        }
        .nav-link {
            transition: all 0.3s ease;
            text-decoration: none;
            color: #f0f0f5;
            padding: 0.5rem 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nav-link:hover {
            color: #D4C1F0;
        }
        .active-nav {
            border-bottom: 2px solid #D4C1F0;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #2D1152;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: 1px solid #D4C1F0;
        }
        .btn-primary:hover {
            background-color: #4A1D80;
        }
        .card {
            transition: all 0.3s ease;
            border: 1px solid #D4C1F0;
        }
        .card:hover {
            box-shadow: 0 5px 15px rgba(45, 17, 82, 0.3);
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
        .govt-header {
            background-color: #2D1152;
            border-bottom: 3px solid #D4C1F0;
        }
        .govt-footer {
            background-color: #2D1152;
            border-top: 3px solid #D4C1F0;
        }
        .govt-emblem {
            height: 60px;
            margin-right: 15px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header>
        <!-- Government Website Top Bar -->
        <div class="bg-gray-200 py-1 border-b border-gray-300">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center text-xs">
                    <div class="flex space-x-4">
                        <span>Font Size: <a href="#" class="px-1">A-</a> <a href="#" class="px-1">A</a> <a href="#" class="px-1">A+</a></span>
                        <span class="hidden md:inline-block">|</span>
                        <a href="#" class="hidden md:inline-block">Screen Reader Access</a>
                    </div>
                    <div class="flex space-x-4">
                        <a href="#">English</a>
                        <span>|</span>
                        <a href="#">हिंदी</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Government Emblem and Title -->
        <div class="govt-header py-4">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <a href="index.php">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" class="govt-emblem" alt="Government Emblem">
                        </a>
                        <div class="text-center md:text-left">
                            <a href="index.php" class="text-3xl font-bold text-white no-underline">DONATE HERE</a>
                            <p class="text-sm text-gray-200">Ministry of Food Distribution</p>
                            <p class="text-xs text-gray-300">Government of India</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="relative mr-4">
                            <input type="text" placeholder="Search..." class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                            <button class="absolute right-2 top-2">
                                <i class="fas fa-search text-gray-500"></i>
                            </button>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="relative group">
                                <button class="flex items-center space-x-1 focus:outline-none text-white px-3 py-1 border border-gray-300 rounded-md">
                                    <span>My Account</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                                    <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
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
        <nav class="bg-gray-800 shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center">
                    <div class="hidden md:flex items-center">
                        <a href="index.php" class="nav-link px-4 py-3 block <?php echo ($current_page == 'index.php') ? 'active-nav' : ''; ?>">Home</a>
                        <a href="donations.php" class="nav-link px-4 py-3 block <?php echo ($current_page == 'donations.php') ? 'active-nav' : ''; ?>">Donations</a>
                        <a href="donate.php" class="nav-link px-4 py-3 block <?php echo ($current_page == 'donate.php') ? 'active-nav' : ''; ?>">Donate Food</a>
                        <a href="aboutus.php" class="nav-link px-4 py-3 block <?php echo ($current_page == 'aboutus.php') ? 'active-nav' : ''; ?>">About Us</a>
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
            <div id="mobile-menu" class="hidden md:hidden bg-gray-700">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="index.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'index.php') ? 'bg-gray-900' : ''; ?>">Home</a>
                    <a href="donations.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'donations.php') ? 'bg-gray-900' : ''; ?>">Donations</a>
                    <a href="donate.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'donate.php') ? 'bg-gray-900' : ''; ?>">Donate Food</a>
                    <a href="aboutus.php" class="block px-3 py-2 rounded-md text-white <?php echo ($current_page == 'aboutus.php') ? 'bg-gray-900' : ''; ?>">About Us</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="block px-3 py-2 rounded-md text-white">Dashboard</a>
                        <a href="profile.php" class="block px-3 py-2 rounded-md text-white">Profile</a>
                        <a href="logout.php" class="block px-3 py-2 rounded-md text-white">Logout</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        
        <?php if ($current_page != 'login.php' && $current_page != 'signup.php'): ?>
        <div class="primary-bg text-white py-3 border-t border-b border-gray-700">
            <div class="container mx-auto px-4">
                <p class="text-center text-lg font-bold">OFFICIAL NOTICE: Please help us save food and feed the hungry.</p>
            </div>
        </div>
        <?php endif; ?>
    </header>
    <main class="container mx-auto px-4 py-6">