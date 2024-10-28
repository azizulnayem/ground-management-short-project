<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination logic
$limit = 6; // Number of blogs per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch blogs
$blogs = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$totalBlogs = $conn->query("SELECT COUNT(*) as count FROM blogs")->fetch_assoc()['count'];
$totalPages = ceil($totalBlogs / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Blogs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .blog-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .blog {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .blog img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .blog-content {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }
        .blog h2 {
            font-size: 1.5em;
            margin: 0;
            color: #0044cc;
        }
        .blog p {
            font-size: 0.9em;
            color: #555;
        }
        .read-more {
            background-color: #0044cc;
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            margin: 10px 0;
            transition: background-color 0.3s;
        }
        .read-more:hover {
            background-color: #002b80;
        }
        .blog-date {
            font-size: 0.8em;
            color: #999;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .pagination a {
            padding: 10px 15px;
            background-color: #0044cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #002b80;
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
<a href="index.php" class="button"><i class="fas fa-arrow-left"></i> Back to Blogs</a>
<a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <h1>All Blogs</h1>
    <div class="blog-container">
        <?php while ($blog = $blogs->fetch_assoc()): ?>
            <div class="blog">
                <?php if (!empty($blog['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="Blog Image">
                <?php endif; ?>
                <div class="blog-content">
                    <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
                    <p><?php echo substr(htmlspecialchars($blog['content']), 0, 100) . '...'; ?></p>
                    <p class="blog-date"><small>Posted on <?php echo date('F j, Y', strtotime($blog['created_at'])); ?></small></p>
                    <a href="blog_detail.php?id=<?php echo $blog['blog_id']; ?>" class="read-more">Read More</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
  
</body>
</html>
