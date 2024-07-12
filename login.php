<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     
    $servername = "sql311.infinityfree.com";
    $username = "if0_36132155";
    $password = "mCaZa3Xy6nt5t";
    $dbname = "if0_36132155_fd_db";
 
    $conn = mysqli_connect($servername, $username, $password, $dbname);

  
  
    $email = $_POST['email'];
    $password = $_POST['password'];

  
    $sql = "SELECT * FROM  signup1 WHERE Email = '$email' AND Password = '$password'";
    $result = mysqli_query($conn,$sql);

    if ($result->num_rows > 0) {
        header("Location:http://spammers1.rf.gd/");
        exit();
    } else {
        
        $error_message = "Invalid email or password";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex justify-center items-center h-screen">
    <div class="w-full h-full md:w-1/2 px-8 py-6 bg-white rounded-lg shadow-md">
        <div class="text-center pb-8 pt-9" style="margin-top: 100px;">
            <h1 class="text-3xl font-bold text-gray-800">Login to the account</h1>
        </div>
        <form class="space-y-4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email"
                       class="shadow-none appearance-none border-b border-gray-500 rounded-none w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="relative mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password"
                       class="shadow-none appearance-none border-b border-gray-500 rounded-none w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="button" onclick="togglePasswordVisibility()"
                        class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.728 16.728A9.956 9.956 0 0021 12c0-5.523-4.477-10-10-10S1 6.477 1 12s4.477 10 10 10c1.717 0 3.336-.434 4.728-1.272l-1.456-1.457C15.65 19.196 13.423 20 11 20c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8c0 2.423-.804 4.65-2.153 6.272l1.457 1.456zM11 18a6 6 0 100-12 6 6 0 000 12z"/>
                    </svg>
                </button>
            </div>
            <?php if (isset($error_message)) { ?>
                <div class="text-red-500"><?php echo $error_message; ?></div>
            <?php } ?>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="#" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-700">Forgot
                        Password?</a>
                </div>
            </div>
            <button type="submit"
                    class="bg-purple-900 hover:bg-purple-700 text-white font-bold py-4 px-20 rounded focus:outline-none focus:shadow-outline" style="margin-left: 250px;">Login
            </button>
            <div class="mt-4 text-center">
                <p class="text-sm">Don't have an account? <a class="text-blue-500 hover:text-blue-700"
                                                              href="http://spammers2.rf.gd/">Sign Up</a></p>
            </div>
        </form>
    </div>
    <div class="hidden md:block md:w-1/2">
        <img src="https://media.istockphoto.com/id/1289345395/photo/food-bank-donation.jpg?s=612x612&w=0&k=20&c=RVDBpBghFhormz4_en93beMhIkSr-tFq662hJG8ykYw=" alt="Food Donation App Image" style="width: auto; height: 730px;">
    </div>
</div>
<script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    }
</script>
</body>
</html>