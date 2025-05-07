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
    // Try to get user from old table
    $user_sql = "SELECT * FROM signup1 WHERE Email = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("s", $user_email);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($user_result->num_rows > 0) {
        $old_user = $user_result->fetch_assoc();
        $user = [
            'id' => 0,
            'first_name' => $old_user['FirstName'],
            'last_name' => $old_user['LastName'],
            'email' => $old_user['Email'],
            'contact' => $old_user['Contact'],
            'user_type' => 'donor', // Default
            'address' => '',
            'profile_image' => ''
        ];
    }
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
                    $old_password = '';
                    
                    // Get password from old table
                    $old_pass_sql = "SELECT Password FROM signup1 WHERE Email = ?";
                    $old_pass_stmt = $conn->prepare($old_pass_sql);
                    $old_pass_stmt->bind_param("s", $user_email);
                    $old_pass_stmt->execute();
                    $old_pass_result = $old_pass_stmt->get_result();
                    if ($old_pass_result->num_rows > 0) {
                        $old_pass_row = $old_pass_result->fetch_assoc();
                        $old_password = $old_pass_row['Password'];
                    }
                    
                    // Use new password if provided, otherwise use the old one
                    $password_to_use = !empty($new_password) ? $new_password : $old_password;
                    
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

<div class="bg-gradient-to-b from-purple-50 to-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-purple-900 sm:text-4xl">
                My Profile
            </h1>
            <p class="mt-4 text-lg text-purple-600">
                Update your personal information and preferences.
            </p>
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
        
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="px-6 py-6 bg-gradient-to-r from-purple-600 to-purple-800 sm:px-8">
                    <h3 class="text-xl leading-6 font-bold text-white">
                        Personal Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-purple-100">
                        Update your account details and preferences.
                    </p>
                </div>
                <div class="border-t border-purple-200">
                    <div class="px-6 py-6 sm:p-8">
                        <div class="grid grid-cols-6 gap-6">
                            <!-- Profile Image -->
                            <div class="col-span-6 sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                                <div class="mt-2 flex items-center">
                                    <div class="h-32 w-32 rounded-full overflow-hidden bg-purple-100 border-4 border-purple-200 shadow-md">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="h-full w-full object-cover">
                                        <?php else: ?>
                                            <svg class="h-full w-full text-purple-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-5">
                                        <div class="relative bg-white py-2 px-4 border border-purple-300 rounded-lg shadow-sm flex items-center cursor-pointer hover:bg-purple-50 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500 transition-all duration-200">
                                            <label for="profile-image-upload" class="relative text-sm font-medium text-purple-700 pointer-events-none">
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
                                        <i class="fas fa-user text-purple-400"></i>
                                    </div>
                                    <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <!-- Last Name -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-purple-400"></i>
                                    </div>
                                    <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <!-- Email Address -->
                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-purple-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 bg-gray-100 transition-all duration-200" disabled>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Email address cannot be changed.</p>
                            </div>
                            
                            <!-- Contact Number -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="contact" class="block text-sm font-medium text-gray-700">Contact number</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-purple-400"></i>
                                    </div>
                                    <input type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" class="pl-10 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-3 transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <!-- User Type -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="user_type" class="block text-sm font-medium text-gray-700">Account type</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user-tag text-purple-400"></i>
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