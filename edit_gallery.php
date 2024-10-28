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

// Fetch the gallery image to edit
$imageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$imageData = null;

if ($imageId > 0) {
    $stmt = $conn->prepare("SELECT image_url, description FROM gallery WHERE image_id = ?");
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $imageData = $result->fetch_assoc();
    } else {
        echo "No image found with the provided ID.";
        exit();
    }
    $stmt->close();
}

// Handle updating the gallery image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image'])) {
    $description = $_POST['description'];
    $imagePath = $imageData['image_url']; // Default to the current image

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/gallery/";
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
            // Delete the old image from the server
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $imagePath = $targetFile; // Update to new image path
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    // Update the image record in the database
    $stmt = $conn->prepare("UPDATE gallery SET image_url = ?, description = ? WHERE image_id = ?");
    $stmt->bind_param("ssi", $imagePath, $description, $imageId);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid re-posting on refresh
    header("Location: add_gallery.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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

<h2>Edit Gallery Image</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="description" placeholder="Image Description" value="<?php echo htmlspecialchars($imageData['description']); ?>" required>
    <input type="file" name="image" accept="image/*">
    <button type="submit" name="update_image">Update Image</button>
</form>

<!-- Current Image Display -->
<h3>Current Image</h3>
<img src="<?php echo htmlspecialchars($imageData['image_url']); ?>" alt="Current Gallery Image" style="max-width: 100%; height: auto;">
<br>
<!-- Navigation Buttons -->
<a href="add_gallery.php" class="button"><i class="fas fa-arrow-left"></i> Back to Gallery</a>
<a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>

</body>
</html>
