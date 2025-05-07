<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";

// Site configuration
$site_name = "Donate Here";
$site_email = "mounimounikav3417@gmail.com";
$site_description = "A platform to connect food donors with recipients to reduce food waste and help those in need.";

// Team members
$team_members = [
    [
        'name' => 'Mounika',
        'role' => 'Project Lead',
        'email' => 'mounimounikav3417@gmail.com'
    ],
    [
        'name' => 'Toufiq',
        'role' => 'Developer',
        'email' => 'toufiq@example.com'
    ],
    [
        'name' => 'Mohana',
        'role' => 'Designer',
        'email' => 'mohana@example.com'
    ],
    [
        'name' => 'Laxmisai',
        'role' => 'Content Manager',
        'email' => 'laxmisai@example.com'
    ]
];

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection without database first
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
if (!mysqli_select_db($conn, $dbname)) {
    die("Error selecting database: " . mysqli_error($conn));
}

// Check if tables exist
$result = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (!$result) {
    die("Error checking tables: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    // Import database schema
    $sql_file_path = __DIR__ . '/../database.sql';
    if (!file_exists($sql_file_path)) {
        die("Database schema file not found at: " . $sql_file_path);
    }
    
    $sql_file = file_get_contents($sql_file_path);
    if ($sql_file === false) {
        die("Could not read database schema file");
    }
    
    $sql_array = explode(';', $sql_file);
    
    foreach($sql_array as $sql_query) {
        $sql_query = trim($sql_query);
        if (!empty($sql_query)) {
            if (!mysqli_query($conn, $sql_query)) {
                die("Error executing query: " . mysqli_error($conn) . "\nQuery was: " . $sql_query);
            }
        }
    }
    
    // Insert default admin user
    $admin_email = "mounimounikav3417@gmail.com";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $admin_sql = "INSERT INTO users (first_name, last_name, email, password, contact, user_type) 
                  VALUES ('Admin', 'User', ?, ?, '1234567890', 'admin')";
    
    $stmt = $conn->prepare($admin_sql);
    if (!$stmt) {
        die("Error preparing admin user query: " . mysqli_error($conn));
    }
    
    $stmt->bind_param("ss", $admin_email, $admin_password);
    if (!$stmt->execute()) {
        die("Error inserting admin user: " . $stmt->error);
    }
    $stmt->close();
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function is_admin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Function to redirect to a URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to display error message
function display_error($message) {
    return "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4 shadow-sm' role='alert'>
                <div class='flex'>
                    <div class='flex-shrink-0'>
                        <i class='fas fa-exclamation-circle'></i>
                    </div>
                    <div class='ml-3'>
                        <p class='text-sm'>$message</p>
                    </div>
                </div>
            </div>";
}

// Function to display success message
function display_success($message) {
    return "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4 shadow-sm' role='alert'>
                <div class='flex'>
                    <div class='flex-shrink-0'>
                        <i class='fas fa-check-circle'></i>
                    </div>
                    <div class='ml-3'>
                        <p class='text-sm'>$message</p>
                    </div>
                </div>
            </div>";
}

// Function to get user details
function get_user_details($user_id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Function to format date
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

// Function to format time
function format_time($time) {
    return date('g:i A', strtotime($time));
}

// Function to get time ago
function time_ago($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } else {
        return format_date($datetime);
    }
}
?>