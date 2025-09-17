<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "moet1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role, level FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role, $level);
$stmt->fetch();
$stmt->close();

// Check user role and level to redirect
if ($role === 'principal') {
    switch ($level) {
        case 'High':
            $_SESSION['success_message'] = "Thank you we received your data";
            header("Location: Highschool-Dash.php");
            exit();
        case 'Pre':
            $_SESSION['success_message'] = "Thank you we received your data";
            header("Location: kinder-Dash.php");
            exit();
        case 'Primary':
            $_SESSION['success_message'] = "Thank you we received your data";
            header("Location: Primary-Dash.php");
            exit();
        default:
            // Handle unexpected level
            echo "Error: Unrecognized level.";
            exit();
    }
} else {
    // Handle cases where the user is not a principal
    echo "Error: You do not have permission to access this page.";
    exit();
}

// Close the connection
$conn->close();
?>
