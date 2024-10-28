<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: auth.php");
    exit();
}

// Get the user_id from the URL
$user_id = $_GET['user_id'];

// First, delete any complaints related to the user
$query = "DELETE FROM complaints WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Next, delete any reservations for the user (if applicable)
$query = "DELETE FROM reservations WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Now delete any related payments for the user's reservations (if applicable)
$query = "DELETE FROM payments WHERE reservation_id IN (SELECT reservation_id FROM reservations WHERE user_id = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Finally, delete the user from the users table
$query = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Redirect back to the manage_users page with a success message
header("Location: manage_users.php?message=User deleted successfully.");
exit();
?>
