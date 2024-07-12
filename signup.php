<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        input, select {
            border: none;
            border-bottom: 1px solid #a0aec0;  
            outline: none;
        }
        input:focus, select:focus {
            border-bottom: 2px solid #6C3082;  
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
  <div class="flex h-screen">
   
    <div class="hidden md:block md:w-1/2 text-black font-semibold">
      <img src="https://fleurcreativestrategy.com/wp-content/uploads/2019/11/LYF_FeaturedImage.png" style="position: relative; ;
      height: 729px; width: 800px;">
    </div>

 
    <div class="w-full md:w-1/2 px-8 py-6 bg-white rounded-lg shadow-md">
      <div class="text-center pb-8">
        <h1 class="text-3xl font-bold text-gray-800">Hello!</h1>
        <p class="text-gray-600">Create your account</p>
      </div>
      <form class="space-y-4" method="post" id="signupForm">
       
        <div class="flex space-x-4">
          <div class="w-1/2">
            <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" id="firstName" name="firstName" class="mt-1 p-2 w-full">
          </div>
          <div class="w-1/2">
            <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" id="lastName" name="lastName" class="mt-1 p-2 w-full">
          </div>
        </div>
       
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email ID</label>
          <input type="email" id="email" name="email" class="mt-1 p-2 w-full">
          <p class="text-xs text-red-500">Please enter a valid email id</p>
        </div>
       

        <div class="flex space-x-4">

          <div class="relative mb-4 w-1/2">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" class="shadow-none appearance-none border-b border-gray-500 rounded-none w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.728 16.728A9.956 9.956 0 0021 12c0-5.523-4.477-10-10-10S1 6.477 1 12s4.477 10 10 10c1.717 0 3.336-.434 4.728-1.272l-1.456-1.457C15.65 19.196 13.423 20 11 20c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8c0 2.423-.804 4.65-2.153 6.272l1.457 1.456zM11 18a6 6 0 100-12 6 6 0 000 12z" />
              </svg>
            </button>
          </div>
          <div class="relative mb-4 w-1/2">
            <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="confirmPassword" name="c-password" class="shadow-none appearance-none border-b border-gray-500 rounded-none w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <button type="button" onclick="togglePasswordVisibility('confirmPassword')" class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.728 16.728A9.956 9.956 0 0021 12c0-5.523-4.477-10-10-10S1 6.477 1 12s4.477 10 10 10c1.717 0 3.336-.434 4.728-1.272l-1.456-1.457C15.65 19.196 13.423 20 11 20c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8c0 2.423-.804 4.65-2.153 6.272l1.457 1.456zM11 18a6 6 0 100-12 6 6 0 000 12z" />
              </svg>
            </button>
            <p id="passwordError" class="text-xs text-red-500 hidden">Passwords do not match</p>
          </div>
        </div>

        <div class="mb-4">
          <label for="contact" class="block text-sm font-medium text-gray-700">Contact</label>
          <input type="contact" id="contact" name="contact" class="mt-1 p-2 w-full" >
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <a href="#" class="px-2 py-2 text-sm font-medium text-gray-600 hover:text-gray-700"><input type="checkbox" name="cb" id="cb"> signing up you accept the Terms of Service and Privacy Policy</a>
          </div>
        </div>
        <button type="submit" class="text-white font-bold py-3 px-20 rounded focus:outline-none focus:shadow-outline" style="background-color: #6C3082; margin-left: 250px;">Sign Up</button>
    
        <div class="mt-4 text-center">
            <p class="text-sm">Already have an account? <a class="text-blue-500 hover:text-blue-700" href="http://spammers3.rf.gd/">Log In</a></p>
        </div>
      </form>
    </div>
  </div>
  
  <script>
    function togglePasswordVisibility(inputId) {
      var passwordInput = document.getElementById(inputId);
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
      } else {
        passwordInput.type = "password";
      }
    }

 
    document.getElementById("signupForm").addEventListener("submit", function(event) {
      var password = document.getElementById("password").value;
      var confirmPassword = document.getElementById("confirmPassword").value;
      if (password !== confirmPassword) {
        document.getElementById("passwordError").classList.remove("hidden");
        event.preventDefault();  
      }
    });
  </script>
</body>
</html>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "sql311.infinityfree.com";
    $username = "if0_36132155";
    $password = "mCaZa3Xy6nt5t";
    $dbname = "if0_36132155_fd_db";

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $cPassword = $_POST['c-password'];
  $contact = $_POST['contact'];

  $sql = "INSERT INTO signup1 (FirstName, LastName, Email, Password, Cpassword, Contact)
  VALUES ('$firstName', '$lastName', '$email', '$password', '$cPassword', '$contact')";
  $result=mysqli_query($conn,$sql);

  if ($result) {
      header("Location:http://spammers3.rf.gd/");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}
?>
