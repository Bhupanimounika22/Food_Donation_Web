<?php
require_once 'includes/config.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Not sanitizing password to preserve special characters
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required";
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check if password is hashed (starts with $2y$)
            if (substr($user['password'], 0, 4) === '$2y$') {
                // Verify password using password_verify for hashed passwords
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    // Redirect based on user type
                    if ($user['user_type'] === 'admin') {
                        redirect('admin/dashboard.php');
                    } else {
                        redirect('index.php');
                    }
                } else {
                    $error_message = "Invalid password";
                }
            } else {
                // For backward compatibility with non-hashed passwords
                if ($password === $user['password']) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    // Update to hashed password for future logins
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("si", $hashed_password, $user['id']);
                    $update_stmt->execute();
                    
                    // Redirect based on user type
                    if ($user['user_type'] === 'admin') {
                        redirect('admin/dashboard.php');
                    } else {
                        redirect('index.php');
                    }
                } else {
                    $error_message = "Invalid password";
                }
            }
        } else {
            // Try the old table structure for backward compatibility
            $sql = "SELECT * FROM signup1 WHERE Email = '$email' AND Password = '$password'";
            $result = mysqli_query($conn, $sql);
            
            if ($result && $result->num_rows > 0) {
                // Set basic session variables
                $_SESSION['user_email'] = $email;
                redirect('index.php');
            } else {
                $error_message = "User not found. Please check your email or sign up.";
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
    <title>Login - Donate Here</title>
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
        .login-container {
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
    <div class="login-container">
        <div class="flex w-full max-w-5xl bg-white rounded-xl shadow-lg overflow-hidden card-shadow">
            <!-- Left side - Form -->
            <div class="w-full md:w-1/2 px-6 py-12 sm:px-10">
                <div class="text-center mb-8">
                    <a href="index.php" class="inline-block">
                        <div class="flex items-center justify-center">
                            <img src="https://tse1.mm.bing.net/th?id=OIP.8pi6TzMUWQrEq7fkmHJl1AHaHa&pid=Api&P=0&h=180" class="w-14 h-14 mr-2" alt="Logo">
                            <h1 class="text-2xl font-bold primary-color">DONATE HERE</h1>
                        </div>
                    </a>
                    <h2 class="text-3xl font-bold mt-6 text-gray-800">Welcome Back!</h2>
                    <p class="text-gray-600 mt-2">Login to your account to continue your journey</p>
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
                
                <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" 
                                   class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 transition-all duration-200" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" onclick="togglePasswordVisibility()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <i class="fas fa-eye" id="togglePassword"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox" 
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium primary-color hover:text-purple-500 transition-colors duration-200">
                                Forgot password?
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white btn-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                            Sign in
                        </button>
                    </div>
                    
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Don't have an account? 
                            <a href="signup.php" class="font-medium primary-color hover:text-purple-500 transition-colors duration-200">
                                Sign up now
                            </a>
                        </p>
                    </div>
                    
                    <div class="relative mt-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
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
            
            <!-- Right side - Image -->
            <div class="hidden md:block md:w-1/2 relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1593113598332-cd59a93c5156?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
                     alt="Food Donation" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-900 to-transparent opacity-60"></div>
                <div class="absolute inset-0 flex flex-col items-start justify-center p-12 text-white">
                    <h3 class="text-3xl font-bold mb-4">Making a Difference</h3>
                    <p class="text-lg mb-6">Join our community of donors and help reduce food waste while feeding those in need.</p>
                    <div class="flex space-x-4 mt-4">
                        <div class="flex items-center">
                            <div class="bg-white rounded-full p-1 mr-2">
                                <i class="fas fa-utensils text-purple-700"></i>
                            </div>
                            <span>500+ Donations</span>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-white rounded-full p-1 mr-2">
                                <i class="fas fa-users text-purple-700"></i>
                            </div>
                            <span>2000+ People Fed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var toggleIcon = document.getElementById("togglePassword");
            
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
    </script>
</body>
</html>