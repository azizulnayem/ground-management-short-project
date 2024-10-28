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

// Get reservation ID from query parameters
$reservation_id = isset($_GET['reservation_id']) ? (int)$_GET['reservation_id'] : 0;

// Check if reservation ID is valid
if ($reservation_id > 0) {
    // Update the status to approved
    $query = "UPDATE reservations SET status = 'approved' WHERE reservation_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to the view reservations page with a success message
    header("Location: view_reservations.php?user_id=" . $_GET['user_id'] . "&message=Reservation approved successfully.");
    exit();
} else {
    // Redirect back with an error message if reservation ID is invalid
    header("Location: view_reservations.php?user_id=" . $_GET['user_id'] . "&message=Invalid reservation ID.");
    exit();
}
?>
