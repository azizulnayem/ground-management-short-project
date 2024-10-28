<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the blog ID from the query string
if (isset($_GET['id'])) {
    $blogId = (int)$_GET['id'];

    // Fetch the blog from the database
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE blog_id = ?");
    $stmt->bind_param("i", $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    $stmt->close();

    // Check if the blog exists
    if (!$blog) {
        header("Location: blog.php"); // Redirect to blogs page if not found
        exit();
    }
} else {
    header("Location: blog.php"); // Redirect to blogs page if no ID is provided
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .blog-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .blog-content img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .blog-date {
            font-size: 0.9em;
            color: #999;
            text-align: right;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            background-color: #0044cc;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 1em;
        }
        .back-button:hover {
            background-color: #002b80;
        }
        .icon {
            margin-right: 8px;
            color: #fff; /* Change this to match your color scheme */
        }
        .date-icon {
            margin-right: 5px;
            color: #999; /* Color for date icon */
        }
    </style>
</head>
<body>
    <a href="blog2.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Blogs</a>
    <h1><i class="fas fa-pencil-alt icon"></i><?php echo htmlspecialchars($blog['title']); ?></h1>
    <div class="blog-content">
        <?php if (!empty($blog['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="Blog Image">
        <?php endif; ?>
        <p class="blog-date"><i class="fas fa-calendar-alt date-icon"></i><small>Posted on <?php echo date('F j, Y', strtotime($blog['created_at'])); ?></small></p>
        <p><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>
    </div>
</body>
</html>
