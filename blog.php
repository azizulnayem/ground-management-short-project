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

// Pagination logic
$limit = 4; // Number of blogs per page
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
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #0044cc;
            margin-bottom: 20px;
        }
        .blog-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        .blog {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .blog:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .blog img {
            width: 100%;
            max-width: 500px;
            height: 250px;
            object-fit: cover;
            border-radius: 4px;
            margin: 10px 0;
        }
        .blog h2 {
            font-size: 1.5em;
            color: #0044cc;
            margin-bottom: 10px;
        }
        .blog p {
            font-size: 1em;
            line-height: 1.5;
        }
        .blog-date {
            font-size: 0.9em;
            color: #999;
            text-align: right;
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
            transition: background-color 0.3s;
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .button:hover {
            background-color: #002b80;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .delete-button {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
            transition: background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .delete-button:hover {
            background-color: #c0392b;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
    <script>
        function confirmDelete(blogId) {
            if (confirm("Are you sure you want to delete this blog?")) {
                document.getElementById("deleteForm_" + blogId).submit();
            }
        }
    </script>
</head>
<body>

<a href="add_blog.php" class="button"><i class="fas fa-plus"></i> Add New Blog</a>
<a href="admin.php" class="button"><i class="fas fa-tools"></i> Admin Panel</a>
<a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>

<h1>All Blogs</h1>

<div class="blog-container">
    <?php while ($blog = $blogs->fetch_assoc()): ?>
        <div class="blog">
            <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
            <?php if (!empty($blog['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="Blog Image">
            <?php endif; ?>
            <p><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>
            <p class="blog-date"><small>Posted on <?php echo htmlspecialchars($blog['created_at']); ?></small></p>
            
            <!-- Edit button -->
            <a href="edit_blog.php?id=<?php echo $blog['blog_id']; ?>" class="button">Edit <i class="fas fa-edit"></i></a>

            <!-- Delete button with confirmation -->
            <form id="deleteForm_<?php echo $blog['blog_id']; ?>" action="delete_blog.php" method="POST" style="display:inline;">
                <input type="hidden" name="blog_id" value="<?php echo $blog['blog_id']; ?>">
                <button type="button" class="delete-button" onclick="confirmDelete(<?php echo $blog['blog_id']; ?>)">Delete <i class="fas fa-trash"></i></button>
            </form>
        </div>
    <?php endwhile; ?>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

</body>
</html>
