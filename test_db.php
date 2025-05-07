<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected to MySQL server successfully.<br>";
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (mysqli_query($conn, $sql)) {
    echo "Database created or already exists.<br>";
    
    // Select the database
    mysqli_select_db($conn, $dbname);
    echo "Selected database: $dbname<br>";
    
    // Check if tables exist
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($result) == 0) {
        echo "Users table doesn't exist. Creating tables from database.sql...<br>";
        
        // Import database schema
        $sql_file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/hack_athon-main/database.sql');
        $sql_array = explode(';', $sql_file);
        
        foreach($sql_array as $sql_query) {
            if(trim($sql_query) != '') {
                if(mysqli_query($conn, $sql_query)) {
                    echo "Query executed successfully.<br>";
                } else {
                    echo "Error executing query: " . mysqli_error($conn) . "<br>";
                }
            }
        }
    } else {
        echo "Tables already exist.<br>";
        
        // Show all tables
        $result = mysqli_query($conn, "SHOW TABLES");
        echo "Tables in database:<br>";
        while($row = mysqli_fetch_row($result)) {
            echo "- " . $row[0] . "<br>";
        }
    }
} else {
    die("Error creating database: " . mysqli_error($conn));
}

// Close connection
mysqli_close($conn);
echo "Database connection closed.";
?>