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

// Handle adding a notice
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notice'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $timestamp = date("Y-m-d H:i:s");

    // Handle image upload
    $imagePath = "";
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

        // Upload the file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    // Insert notice into the database
    $stmt = $conn->prepare("INSERT INTO notices (title, description, image_path, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $imagePath, $timestamp);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid re-posting on refresh
    header("Location: add_notice.php");
    exit(); // Always call exit after a redirect
}

// Handle deleting a notice
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $conn->query("DELETE FROM notices WHERE notice_id='$deleteId'");
}

// Fetch all notices
$notices = $conn->query("SELECT * FROM notices");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Notice</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .links a {
            margin-right: 15px;
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

    <h2>Add New Notice</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Notice Title" required>
        <textarea name="description" placeholder="Notice Description" required></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="add_notice">Add Notice</button>
    </form>

    <h3>Existing Notices</h3>
    <table>
        <tr>
            <th>Notice ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($notice = $notices->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($notice['notice_id']); ?></td>
                <td><?php echo htmlspecialchars($notice['title']); ?></td>
                <td><?php echo htmlspecialchars($notice['description']); ?></td>
                <td>
                    <?php if (!empty($notice['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" alt="Notice Image" width="100">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($notice['created_at']); ?></td>
                <td>
                    <a href="edit_notice.php?edit_id=<?php echo $notice['notice_id']; ?>"><i class="fas fa-edit"></i> Edit</a> |
                    <a href="add_notice.php?delete_id=<?php echo $notice['notice_id']; ?>" onclick="return confirm('Are you sure you want to delete this notice?');"><i class="fas fa-trash-alt"></i> Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Navigation Buttons -->
    <a href="admin.php" class="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
</body>
</html>
