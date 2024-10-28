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

$reservation_id = $_GET['reservation_id'];

// First, delete any related payments
$query = "DELETE FROM payments WHERE reservation_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();

// Now, delete the reservation
$query = "DELETE FROM reservations WHERE reservation_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();

// Redirect back to the manage reservations page
header("Location: manage_reservations.php?message=Reservation deleted successfully.");
exit();
?>
