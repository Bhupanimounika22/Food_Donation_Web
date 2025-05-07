<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in, redirecting to login page");
    redirect('login.php');
}

// Debug information
error_log("Dashboard accessed by user ID: " . $_SESSION['user_id']);
error_log("Session data: " . print_r($_SESSION, true));

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

// Get user details
$user = null;
if ($user_id > 0) {
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
    }
} elseif (!empty($user_email)) {
    // If we get here and user is not found in the users table,
    // they need to register properly
    // No need to check old tables
}

// Handle donation status updates
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['donation_id'])) {
    $action = sanitize_input($_POST['action']);
    $donation_id = sanitize_input($_POST['donation_id']);
    
    if ($action == 'complete') {
        $update_sql = "UPDATE food_donations SET status = 'completed' WHERE id = ? AND donor_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $donation_id, $user_id);
        
        if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
            // Also update any reservations
            $update_res_sql = "UPDATE reservations SET status = 'completed' WHERE donation_id = ?";
            $update_res_stmt = $conn->prepare($update_res_sql);
            $update_res_stmt->bind_param("i", $donation_id);
            $update_res_stmt->execute();
            
            $success_message = "Donation marked as completed successfully.";
        } else {
            $error_message = "Failed to update donation status.";
        }
    } elseif ($action == 'cancel') {
        $update_sql = "UPDATE food_donations SET status = 'available' WHERE id = ? AND donor_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $donation_id, $user_id);
        
        if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
            // Also update any reservations
            $update_res_sql = "UPDATE reservations SET status = 'cancelled' WHERE donation_id = ?";
            $update_res_stmt = $conn->prepare($update_res_sql);
            $update_res_stmt->bind_param("i", $donation_id);
            $update_res_stmt->execute();
            
            $success_message = "Reservation cancelled successfully.";
        } else {
            $error_message = "Failed to cancel reservation.";
        }
    } elseif ($action == 'delete') {
        $delete_sql = "DELETE FROM food_donations WHERE id = ? AND donor_id = ? AND status != 'reserved'";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $donation_id, $user_id);
        
        if ($delete_stmt->execute() && $delete_stmt->affected_rows > 0) {
            $success_message = "Donation deleted successfully.";
        } else {
            $error_message = "Failed to delete donation. You cannot delete a reserved donation.";
        }
    }
}

// Get user's donations
$donations = [];
if ($user_id > 0) {
    $donations_sql = "SELECT fd.*, 
                        (SELECT COUNT(*) FROM reservations WHERE donation_id = fd.id) as reservation_count 
                      FROM food_donations fd 
                      WHERE fd.donor_id = ? 
                      ORDER BY fd.created_at DESC";
    $donations_stmt = $conn->prepare($donations_sql);
    $donations_stmt->bind_param("i", $user_id);
    $donations_stmt->execute();
    $donations_result = $donations_stmt->get_result();
    while ($row = $donations_result->fetch_assoc()) {
        $donations[] = $row;
    }
}

// Get user's reservations
$reservations = [];
if ($user_id > 0) {
    $reservations_sql = "SELECT r.*, fd.food_name, fd.food_type, fd.expiry_time, fd.pickup_address, 
                            u.first_name, u.last_name, u.contact 
                          FROM reservations r 
                          JOIN food_donations fd ON r.donation_id = fd.id 
                          JOIN users u ON fd.donor_id = u.id 
                          WHERE r.recipient_id = ? 
                          ORDER BY r.reservation_time DESC";
    $reservations_stmt = $conn->prepare($reservations_sql);
    $reservations_stmt->bind_param("i", $user_id);
    $reservations_stmt->execute();
    $reservations_result = $reservations_stmt->get_result();
    while ($row = $reservations_result->fetch_assoc()) {
        $reservations[] = $row;
    }
}
?>

<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-900 sm:text-4xl">
                My Dashboard
            </h1>
            <?php if ($user): ?>
                <p class="mt-4 text-lg text-gray-600">
                    Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!
                </p>
            <?php endif; ?>
        </div>
        
        <!-- User Profile Summary -->
        <?php if ($user): ?>
            <div class="bg-blue-50 rounded-lg shadow-sm p-6 mb-8 border border-blue-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                            <p class="text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['contact']); ?></p>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <a href="profile.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profile
                        </a>
                        <a href="donate.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Donate Food
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Activity Statistics -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Donations Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-blue-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">My Donations</h3>
                            <div class="flex items-center mt-1">
                                <span class="text-3xl font-bold text-blue-600"><?php echo count($donations); ?></span>
                                <span class="ml-2 text-sm text-gray-500">total donations</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded p-3 text-center">
                            <span class="block text-xl font-bold text-blue-600">
                                <?php 
                                    $available = 0;
                                    foreach ($donations as $donation) {
                                        if ($donation['status'] == 'available') $available++;
                                    }
                                    echo $available;
                                ?>
                            </span>
                            <span class="text-xs text-gray-500">Available</span>
                        </div>
                        <div class="bg-yellow-50 rounded p-3 text-center">
                            <span class="block text-xl font-bold text-yellow-600">
                                <?php 
                                    $reserved = 0;
                                    foreach ($donations as $donation) {
                                        if ($donation['status'] == 'reserved') $reserved++;
                                    }
                                    echo $reserved;
                                ?>
                            </span>
                            <span class="text-xs text-gray-500">Reserved</span>
                        </div>
                    </div>
                </div>
                
                <!-- Reservations Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-blue-100">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">My Reservations</h3>
                            <div class="flex items-center mt-1">
                                <span class="text-3xl font-bold text-green-600"><?php echo count($reservations); ?></span>
                                <span class="ml-2 text-sm text-gray-500">total reservations</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded p-3 text-center">
                            <span class="block text-xl font-bold text-green-600">
                                <?php 
                                    $active = 0;
                                    foreach ($reservations as $reservation) {
                                        if ($reservation['status'] == 'pending' || $reservation['status'] == 'confirmed') $active++;
                                    }
                                    echo $active;
                                ?>
                            </span>
                            <span class="text-xs text-gray-500">Active</span>
                        </div>
                        <div class="bg-blue-50 rounded p-3 text-center">
                            <span class="block text-xl font-bold text-blue-600">
                                <?php 
                                    $completed = 0;
                                    foreach ($reservations as $reservation) {
                                        if ($reservation['status'] == 'completed') $completed++;
                                    }
                                    echo $completed;
                                ?>
                            </span>
                            <span class="text-xs text-gray-500">Completed</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-blue-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="donate.php" class="flex items-center p-3 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors duration-200">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Create New Donation</span>
                        </a>
                        <a href="donations.php" class="flex items-center p-3 bg-green-50 rounded-md hover:bg-green-100 transition-colors duration-200">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Find Available Food</span>
                        </a>
                        <a href="profile.php" class="flex items-center p-3 bg-yellow-50 rounded-md hover:bg-yellow-100 transition-colors duration-200">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Update Profile</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Dashboard Tabs -->
        <div class="mb-8">
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" onchange="switchTab(this.value)" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="donations">My Donations</option>
                    <option value="reservations">My Reservations</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="#" onclick="switchTab('donations'); return false;" class="tab-link border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" id="donations-tab">
                            My Donations
                        </a>
                        <a href="#" onclick="switchTab('reservations'); return false;" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" id="reservations-tab">
                            My Reservations
                        </a>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- My Donations Tab -->
        <div id="donations-content" class="tab-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">My Donations</h2>
                <a href="donate.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Donation
                </a>
            </div>
            
            <?php if (count($donations) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Food Item</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservations</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Posted</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                <?php if ($donation['food_type'] == 'raw_food'): ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                <?php else: ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($donation['food_name']); ?></div>
                                                <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo htmlspecialchars(substr($donation['food_details'], 0, 50) . (strlen($donation['food_details']) > 50 ? '...' : '')); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $donation['food_type'] == 'raw_food' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                            <?php echo $donation['food_type'] == 'raw_food' ? 'Raw Food' : 'Cooked Food'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($donation['expiry_time']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($donation['status'] == 'available'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Available
                                            </span>
                                        <?php elseif ($donation['status'] == 'reserved'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Reserved
                                            </span>
                                        <?php elseif ($donation['status'] == 'completed'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Completed
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Expired
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $donation['reservation_count']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($donation['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="donation_details.php?id=<?php echo $donation['id']; ?>" class="text-purple-600 hover:text-purple-900" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            
                                            <?php if ($donation['status'] == 'reserved'): ?>
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="inline" onsubmit="return confirm('Are you sure you want to mark this donation as completed?');">
                                                    <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                                                    <input type="hidden" name="action" value="complete">
                                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Mark as Completed">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($donation['status'] == 'available'): ?>
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="inline" onsubmit="return confirm('Are you sure you want to delete this donation?');">
                                                    <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No donations yet</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Get started by creating a new donation.
                    </p>
                    <div class="mt-6">
                        <a href="donate.php" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            New Donation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- My Reservations Tab -->
        <div id="reservations-content" class="tab-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">My Reservations</h2>
                <a href="donations.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Find Food
                </a>
            </div>
            
            <?php if (count($reservations) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Food Item</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved On</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                <?php if ($reservation['food_type'] == 'raw_food'): ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                <?php else: ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($reservation['food_name']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo $reservation['food_type'] == 'raw_food' ? 'Raw Food' : 'Cooked Food'; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($reservation['contact']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($reservation['expiry_time']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($reservation['status'] == 'pending'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        <?php elseif ($reservation['status'] == 'confirmed'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Confirmed
                                            </span>
                                        <?php elseif ($reservation['status'] == 'completed'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Completed
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Cancelled
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($reservation['reservation_time'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="donation_details.php?id=<?php echo $reservation['donation_id']; ?>" class="text-purple-600 hover:text-purple-900" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            
                                            <?php if ($reservation['status'] == 'pending' || $reservation['status'] == 'confirmed'): ?>
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="inline" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                                                    <input type="hidden" name="donation_id" value="<?php echo $reservation['donation_id']; ?>">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Cancel Reservation">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No reservations yet</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Browse available donations and reserve food.
                    </p>
                    <div class="mt-6">
                        <a href="donations.php" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Find Food
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show the selected tab content
        document.getElementById(tabId + '-content').classList.remove('hidden');
        
        // Update tab styles
        document.querySelectorAll('.tab-link').forEach(tab => {
            tab.classList.remove('border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });
        
        document.getElementById(tabId + '-tab').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        document.getElementById(tabId + '-tab').classList.add('border-blue-500', 'text-blue-600');
    }
</script>

<?php
require_once 'includes/footer.php';
?>