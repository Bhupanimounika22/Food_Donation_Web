<?php
require_once 'includes/config.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = sanitize_input($_POST['firstName']);
    $lastName = sanitize_input($_POST['lastName']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Not sanitizing password to preserve special characters
    $cPassword = $_POST['c-password'];
    $contact = sanitize_input($_POST['contact']);
    $user_type = isset($_POST['user_type']) ? sanitize_input($_POST['user_type']) : 'donor';
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($cPassword) || empty($contact)) {
        $error_message = "All fields are required";
    } elseif ($password !== $cPassword) {
        $error_message = "Passwords do not match";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else {
        // Check if email already exists in users table
        $check_sql = "SELECT * FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "Email already exists. Please use a different email or login.";
        } else {
            // Insert into users table
            $insert_sql = "INSERT INTO users (first_name, last_name, email, password, contact, user_type) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssssss", $firstName, $lastName, $email, $password, $contact, $user_type);
            
            if ($insert_stmt->execute()) {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Update the user record with the hashed password
                $update_sql = "UPDATE users SET password = ? WHERE email = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $hashed_password, $email);
                $update_stmt->execute();
                
                // Also insert into the old signup1 table for backward compatibility
                $old_sql = "INSERT INTO signup1 (FirstName, LastName, Email, Password, Cpassword, Contact)
                           VALUES ('$firstName', '$lastName', '$email', '$password', '$cPassword', '$contact')";
                mysqli_query($conn, $old_sql);
                
                $success_message = "Registration successful! You can now login.";
                
                // Redirect to login page after 2 seconds
                header("refresh:2;url=login.php");
            } else {
                $error_message = "Error: " . $insert_stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Donate Here</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
        }
        .primary-color {
            color: #6C3082;
        }
        .primary-bg {
            background-color: #6C3082;
        }
        input:focus, select:focus {
            border-color: #6C3082;
            outline: none;
            box-shadow: 0 0 0 3px rgba(108, 48, 130, 0.2);
        }
        .btn-primary {
            background-color: #6C3082;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #9B59B6;
            transform: translateY(-2px);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .signup-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .success-message {
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="flex w-full max-w-6xl bg-white rounded-xl shadow-lg overflow-hidden card-shadow">
            <!-- Left side - Image -->
            <div class="hidden md:block md:w-1/2 relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
                     alt="Food Donation" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-900 to-transparent opacity-60"></div>
                <div class="absolute inset-0 flex flex-col items-start justify-center p-12 text-white">
                    <h3 class="text-3xl font-bold mb-4">Join Our Mission</h3>
                    <p class="text-lg mb-6">Create an account today and be part of our community fighting food waste and hunger.</p>
                    <div class="flex space-x-4 mt-4">
                        <div class="flex items-center">
                            <div class="bg-white rounded-full p-1 mr-2">
                                <i class="fas fa-heart text-purple-700"></i>
                            </div>
                            <span>100+ Active Donors</span>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-white rounded-full p-1 mr-2">
                                <i class="fas fa-handshake text-purple-700"></i>
                            </div>
                            <span>50+ Partners</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Form -->
            <div class="w-full md:w-1/2 px-6 py-8 sm:px-10 overflow-y-auto" style="max-height: 100vh;">
                <div class="text-center mb-6">
                    <a href="index.php" class="inline-block">
                        <div class="flex items-center justify-center">
                            <img src="https://tse1.mm.bing.net/th?id=OIP.8pi6TzMUWQrEq7fkmHJl1AHaHa&pid=Api&P=0&h=180" class="w-14 h-14 mr-2" alt="Logo">
                            <h1 class="text-2xl font-bold primary-color">DONATE HERE</h1>
                        </div>
                    </a>
                    <h2 class="text-3xl font-bold mt-4 text-gray-800">Create Account</h2>
                    <p class="text-gray-600 mt-2">Join us in making a difference</p>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
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
                
                <?php if (!empty($success_message)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md success-message" role="alert">
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
            
            <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="signupForm">
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                    <div class="md:w-1/2">
                        <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="firstName" name="firstName" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200" required>
                        </div>
                    </div>
                    <div class="md:w-1/2">
                        <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="lastName" name="lastName" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200" required>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200"
                               placeholder="your@email.com" required>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                    <div class="md:w-1/2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" 
                                   class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" onclick="togglePasswordVisibility('password', 'togglePassword')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <i class="fas fa-eye" id="togglePassword"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="md:w-1/2">
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="confirmPassword" name="c-password" 
                                   class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" onclick="togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                                </button>
                            </div>
                        </div>
                        <p id="passwordError" class="text-xs text-red-500 hidden mt-1">Passwords do not match</p>
                    </div>
                </div>
                
                <div>
                    <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="text" id="contact" name="contact" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200" required>
                    </div>
                </div>
                
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-700">I want to</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-users text-gray-400"></i>
                        </div>
                        <select id="user_type" name="user_type" 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200">
                            <option value="donor">Donate Food</option>
                            <option value="recipient">Receive Food</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        By signing up, you accept the <a href="#" class="primary-color hover:underline">Terms of Service</a> and <a href="#" class="primary-color hover:underline">Privacy Policy</a>
                    </label>
                </div>
                
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white btn-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        Create Account
                    </button>
                </div>
                
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="login.php" class="font-medium primary-color hover:text-purple-500 transition-colors duration-200">
                            Log In
                        </a>
                    </p>
                </div>
                
                <div class="relative mt-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div>
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-google text-red-500 mr-2"></i>
                            Google
                        </a>
                    </div>
                    <div>
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-facebook-f text-blue-600 mr-2"></i>
                            Facebook
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function togglePasswordVisibility(inputId, toggleId) {
            var passwordInput = document.getElementById(inputId);
            var toggleIcon = document.getElementById(toggleId);
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }

        document.getElementById("signupForm").addEventListener("submit", function(event) {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirmPassword").value;
            var passwordError = document.getElementById("passwordError");
            
            if (password !== confirmPassword) {
                passwordError.classList.remove("hidden");
                event.preventDefault();
            } else {
                passwordError.classList.add("hidden");
            }
        });
    </script>
</body>
</html>
