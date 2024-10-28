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

// Handle updating a notice
if (isset($_GET['edit_id'])) {
    $editId = $_GET['edit_id'];
    $noticeQuery = $conn->query("SELECT * FROM notices WHERE notice_id='$editId'");
    $notice = $noticeQuery->fetch_assoc();

    if (!$notice) {
        die("Notice not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_notice'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $imagePath = $notice['image_path']; // Keep existing image path

        // Handle new image upload if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check file size and type (optional)
            if ($_FILES["image"]["size"] > 500000) { // 500 KB limit
                echo "Sorry, your file is too large.";
                exit();
            }
            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                exit();
            }

            // Upload the new file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit();
            }
        }

        // Update the notice in the database
        $stmt = $conn->prepare("UPDATE notices SET title=?, description=?, image_path=? WHERE notice_id=?");
        $stmt->bind_param("sssi", $title, $description, $imagePath, $editId);
        $stmt->execute();
        $stmt->close();

        // Redirect to avoid re-posting on refresh
        header("Location: add_notice.php");
        exit(); // Always call exit after a redirect
    }
} else {
    header("Location: add_notice.php");
    exit(); // Redirect if no edit_id is set
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Notice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add some basic styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .button {
            display: inline-block;
            background-color: #0044cc;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #002b80;
        }
    </style>
</head>
<body>

    <h2>Edit Notice</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" value="<?php echo htmlspecialchars($notice['title']); ?>" required>
        <textarea name="description" required><?php echo htmlspecialchars($notice['description']); ?></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="update_notice">Update Notice</button>
        <a href="add_notice.php" class="button"> Cancel Notice</a>
    </form>

    <a href="add_notice.php" class="button"><i class="fas fa-arrow-left"></i> Back to Notices</a>
</body>
</html>
