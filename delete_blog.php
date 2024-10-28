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

// Check if a blog_id is provided
if (isset($_POST['blog_id'])) {
    $blogId = $_POST['blog_id'];
    
    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM blogs WHERE blog_id = ?");
    $stmt->bind_param("i", $blogId);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the blogs page after deletion
header("Location: blog.php");
exit();
?>
