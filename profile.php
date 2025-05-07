<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) {
    redirect('login.php');
}

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

if (!$user) {
    redirect('login.php');
}

$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $contact = sanitize_input($_POST['contact']);
    $address = sanitize_input($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($first_name) || empty($last_name) || empty($contact)) {
        $error_message = "First name, last name, and contact are required";
    } else {
        // Check if password change is requested
        if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
            if (empty($current_password)) {
                $error_message = "Current password is required to change password";
            } elseif (empty($new_password)) {
                $error_message = "New password is required";
            } elseif (empty($confirm_password)) {
                $error_message = "Please confirm your new password";
            } elseif ($new_password !== $confirm_password) {
                $error_message = "New passwords do not match";
            } elseif ($current_password !== $user['password']) {
                $error_message = "Current password is incorrect";
            }
        }
        
        if (empty($error_message)) {
            // Handle file upload if present
            $profile_image = $user['profile_image'];
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $upload_dir = 'uploads/profiles/';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
                $target_file = $upload_dir . $file_name;
                
                // Check if image file is a actual image
                $check = getimagesize($_FILES['profile_image']['tmp_name']);
                if ($check !== false) {
                    // Try to upload file
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                        $profile_image = $target_file;
                    } else {
                        $error_message = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $error_message = "File is not an image.";
                }
            }
            
            if (empty($error_message)) {
                if ($user_id > 0) {
                    // Update user in the database
                    $update_sql = "UPDATE users SET first_name = ?, last_name = ?, contact = ?, address = ?, profile_image = ?";
                    $params = [$first_name, $last_name, $contact, $address, $profile_image];
                    $types = "sssss";
                    
                    // Add password update if requested
                    if (!empty($new_password)) {
                        $update_sql .= ", password = ?";
                        $params[] = $new_password;
                        $types .= "s";
                    }
                    
                    $update_sql .= " WHERE id = ?";
                    $params[] = $user_id;
                    $types .= "i";
                    
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param($types, ...$params);
                    
                    if ($update_stmt->execute()) {
                        $success_message = "Profile updated successfully";
                        
                        // Update session variables
                        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                        
                        // Refresh user data
                        $user_stmt->execute();
                        $user_result = $user_stmt->get_result();
                        $user = $user_result->fetch_assoc();
                    } else {
                        $error_message = "Error updating profile: " . $update_stmt->error;
                    }
                } else {
                    // For users from the old system, we need to create a new user in the users table
                    $insert_sql = "INSERT INTO users (first_name, last_name, email, password, contact, address, profile_image, user_type) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    // Just use the new password if provided, or generate a random one
                    if (empty($new_password)) {
                        // Generate a random password if none provided
                        $new_password = bin2hex(random_bytes(8)); // 16 character random password
                    }
                    
                    // Hash the password for security
                    $password_to_use = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    $insert_stmt = $conn->prepare($insert_sql);
                    $user_type = 'donor'; // Default
                    $insert_stmt->bind_param("ssssssss", $first_name, $last_name, $user_email, $password_to_use, $contact, $address, $profile_image, $user_type);
                    
                    if ($insert_stmt->execute()) {
                        $new_user_id = $insert_stmt->insert_id;
                        
                        // Update session variables
                        $_SESSION['user_id'] = $new_user_id;
                        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                        $_SESSION['user_type'] = $user_type;
                        
                        $success_message = "Profile created successfully";
                        
                        // Redirect to refresh the page with the new user_id
                        header("Location: profile.php");
                        exit();
                    } else {
                        $error_message = "Error creating profile: " . $insert_stmt->error;
                    }
                }
            }
        }
    }
}
?>

<?php
// Get user's donations count
$donations_count = 0;
$available_count = 0;
$reserved_count = 0;
$completed_count = 0;

if ($user_id > 0) {
    $donations_sql = "SELECT status, COUNT(*) as count FROM food_donations WHERE donor_id = ? GROUP BY status";
    $donations_stmt = $conn->prepare($donations_sql);
    $donations_stmt->bind_param("i", $user_id);
    $donations_stmt->execute();
    $donations_result = $donations_stmt->get_result();
    
    while ($row = $donations_result->fetch_assoc()) {
        if ($row['status'] == 'available') {
            $available_count = $row['count'];
        } elseif ($row['status'] == 'reserved') {
            $reserved_count = $row['count'];
        } elseif ($row['status'] == 'completed') {
            $completed_count = $row['count'];
        }
        $donations_count += $row['count'];
    }
}

// Get user's reservations count
$reservations_count = 0;
$active_reservations = 0;
$completed_reservations = 0;

if ($user_id > 0) {
    $reservations_sql = "SELECT status, COUNT(*) as count FROM reservations WHERE recipient_id = ? GROUP BY status";
    $reservations_stmt = $conn->prepare($reservations_sql);
    $reservations_stmt->bind_param("i", $user_id);
    $reservations_stmt->execute();
    $reservations_result = $reservations_stmt->get_result();
    
    while ($row = $reservations_result->fetch_assoc()) {
        if ($row['status'] == 'pending' || $row['status'] == 'confirmed') {
            $active_reservations += $row['count'];
        } elseif ($row['status'] == 'completed') {
            $completed_reservations = $row['count'];
        }
        $reservations_count += $row['count'];
    }
}
?>

<div class="bg-gradient-to-b from-blue-50 to-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-900 sm:text-4xl">
                My Profile
            </h1>
            <p class="mt-4 text-lg text-blue-600">
                Update your personal information and preferences.
            </p>
        </div>
        
        <!-- Activity Summary -->
        <div class="mb-12 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Donations Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-blue-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-blue-100 pb-2">My Donation Activity</h3>
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center">
                            <span class="text-3xl font-bold text-blue-600"><?php echo $donations_count; ?></span>
                            <span class="ml-2 text-sm text-gray-500">total donations</span>
                        </div>
                        <p class="text-sm text-gray-500">You've helped many people with your generosity!</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="bg-blue-50 rounded p-3 text-center">
                        <span class="block text-xl font-bold text-blue-600"><?php echo $available_count; ?></span>
                        <span class="text-xs text-gray-500">Available</span>
                    </div>
                    <div class="bg-yellow-50 rounded p-3 text-center">
                        <span class="block text-xl font-bold text-yellow-600"><?php echo $reserved_count; ?></span>
                        <span class="text-xs text-gray-500">Reserved</span>
                    </div>
                    <div class="bg-green-50 rounded p-3 text-center">
                        <span class="block text-xl font-bold text-green-600"><?php echo $completed_count; ?></span>
                        <span class="text-xs text-gray-500">Completed</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all donations <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Reservations Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-blue-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-blue-100 pb-2">My Reservation Activity</h3>
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center">
                            <span class="text-3xl font-bold text-green-600"><?php echo $reservations_count; ?></span>
                            <span class="ml-2 text-sm text-gray-500">total reservations</span>
                        </div>
                        <p class="text-sm text-gray-500">Food you've received from generous donors</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="bg-blue-50 rounded p-3 text-center">
                        <span class="block text-xl font-bold text-blue-600"><?php echo $active_reservations; ?></span>
                        <span class="text-xs text-gray-500">Active</span>
                    </div>
                    <div class="bg-green-50 rounded p-3 text-center">
                        <span class="block text-xl font-bold text-green-600"><?php echo $completed_reservations; ?></span>
                        <span class="text-xs text-gray-500">Completed</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <a href="dashboard.php?tab=reservations" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        View all reservations <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm"><?php echo $success_message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm"><?php echo $error_message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-blue-100">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="px-6 py-6 bg-gradient-to-r from-blue-600 to-blue-800 sm:px-8">
                    <h3 class="text-xl leading-6 font-bold text-white">
                        Personal Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-blue-100">
                        Update your account details and preferences.
                    </p>
                </div>
                <div class="border-t border-blue-200">
                    <div class="px-6 py-6 sm:p-8">
                        <div class="grid grid-cols-6 gap-6">
                            <!-- Profile Image -->
                            <div class="col-span-6 sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                                <div class="mt-2 flex items-center">
                                    <div class="h-32 w-32 rounded-full overflow-hidden bg-blue-100 border-4 border-blue-200 shadow-md">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="h-full w-full object-cover">
                                        <?php else: ?>
                                            <svg class="h-full w-full text-blue-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-5">
                                        <div class="relative bg-white py-2 px-4 border border-blue-300 rounded-lg shadow-sm flex items-center cursor-pointer hover:bg-blue-50 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 transition-all duration-200">
                                            <label for="profile-image-upload" class="relative text-sm font-medium text-blue-700 pointer-events-none">
                                                <span>Change Photo</span>
                                                <span class="sr-only"> profile photo</span>
                                            </label>
                                            <input id="profile-image-upload" name="profile_image" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer border-gray-300 rounded-md" accept="image/*">
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">JPG, PNG or GIF up to 5MB</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- First Name -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-blue-400"></i>
                                    </div>
                                    <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="pl-10 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <!-- Last Name -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-blue-400"></i>
                                    </div>
                                    <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="pl-10 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <!-- Email Address -->
                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-blue-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="pl-10 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-100 transition-all duration-200" disabled>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Email address cannot be changed.</p>
                            </div>
                            
                            <!-- Contact Number -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="contact" class="block text-sm font-medium text-gray-700">Contact number</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-blue-400"></i>
                                    </div>
                                    <input type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" class="pl-10 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <!-- User Type -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="user_type" class="block text-sm font-medium text-gray-700">Account type</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user-tag text-blue-400"></i>
                                    </div>
                                    <input type="text" id="user_type" value="<?php echo ucfirst(htmlspecialchars($user['user_type'])); ?>" class="pl-10 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-100 transition-all duration-200" disabled>
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="col-span-6">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-purple-400"></i>
                                    </div>
                                    <textarea name="address" id="address" rows="3" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg transition-all duration-200"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Password Change Section -->
                <div class="px-6 py-6 sm:px-8 border-t border-purple-200 bg-purple-50">
                    <h3 class="text-lg leading-6 font-bold text-purple-800">
                        Change Password
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-purple-600">
                        Leave blank if you don't want to change your password.
                    </p>
                
                    <div class="mt-6 grid grid-cols-6 gap-6">
                        <!-- Current Password -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current password</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-purple-400"></i>
                                </div>
                                <input type="password" name="current_password" id="current_password" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200">
                            </div>
                        </div>
                        
                        <!-- New Password -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="new_password" class="block text-sm font-medium text-gray-700">New password</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-purple-400"></i>
                                </div>
                                <input type="password" name="new_password" id="new_password" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200">
                            </div>
                        </div>
                        
                        <!-- Confirm New Password -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm new password</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-check-circle text-purple-400"></i>
                                </div>
                                <input type="password" name="confirm_password" id="confirm_password" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 text-right sm:px-8 rounded-b-xl">
                    <a href="dashboard.php" class="inline-flex justify-center py-3 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 mr-3">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>