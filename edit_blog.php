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

// Handle editing a blog
if (isset($_GET['id'])) {
    $editId = $_GET['id'];
    $blogQuery = $conn->query("SELECT * FROM blogs WHERE blog_id='$editId'");
    $blog = $blogQuery->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_blog'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $image_url = $blog['image_url']; // Keep the existing image by default

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/";
            $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $targetFile = $targetDir . uniqid() . '.' . $imageFileType;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image_url = $targetFile; // Update the image URL if the new image was uploaded
            }
        }

        // Update the blog in the database
        $stmt = $conn->prepare("UPDATE blogs SET title=?, content=?, image_url=? WHERE blog_id=?");
        $stmt->bind_param("sssi", $title, $content, $image_url, $editId);
        $stmt->execute();
        $stmt->close();

        // Redirect to avoid re-posting on refresh
        header("Location: blog.php");
        exit();
    }
} else {
    // Redirect if no ID is provided
    header("Location: blog.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #002b80;
        }
    </style>
</head>
<body>

    <h2>Edit Blog</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Blog Title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
        <textarea name="content" placeholder="Blog Content" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="update_blog">Update Blog</button>
        <a href="blog.php" class="button">Cancel Update</a>
    </form>

    <a href="blog.php" class="button"><i class="fas fa-arrow-left"></i> Back to Blogs</a>
    <a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
</body>
</html>
