<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: auth.php");
    exit();
}

// Handle adding a gallery image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_image'])) {
    $description = $_POST['description'];
    $imagePath = "";

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/gallery/"; // Change this if needed
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

    // Insert image into the database
    $stmt = $conn->prepare("INSERT INTO gallery (image_url, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $imagePath, $description);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid re-posting on refresh
    header("Location: add_gallery.php");
    exit();
}

// Handle deleting a gallery image
if (isset($_GET['delete'])) {
    $imageId = $_GET['delete'];

    // First, fetch the image URL to delete the file from the server
    $stmt = $conn->prepare("SELECT image_url FROM gallery WHERE image_id = ?");
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $image = $result->fetch_assoc();
        $imagePath = $image['image_url'];

        // Delete the image file from the server
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete the image record from the database
        $stmt = $conn->prepare("DELETE FROM gallery WHERE image_id = ?");
        $stmt->bind_param("i", $imageId);
        $stmt->execute();
    }
    
    $stmt->close();
    header("Location: add_gallery.php");
    exit();
}

// Fetch all gallery images
$galleryImages = $conn->query("SELECT * FROM gallery");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="text"] {
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

        img {
            max-width: 100px; /* Set a max width for gallery images */
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

<h2>Add New Gallery Image</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="description" placeholder="Image Description" required>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit" name="add_image">Add Image</button>
</form>

<h3>Existing Gallery Images</h3>
<table>
    <tr>
        <th>Image ID</th>
        <th>Image</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>
    <?php while ($image = $galleryImages->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($image['image_id']); ?></td>
            <td><img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Gallery Image"></td>
            <td><?php echo htmlspecialchars($image['description']); ?></td>
            <td>
                <a href="edit_gallery.php?id=<?php echo htmlspecialchars($image['image_id']); ?>" class="button">Edit</a>
                <a href="?delete=<?php echo htmlspecialchars($image['image_id']); ?>" class="button" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Navigation Buttons -->
<a href="admin.php" class="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
<a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>

</body>
</html>
