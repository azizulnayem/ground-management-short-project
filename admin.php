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

// Fetch complaints with error handling
$complaints = $conn->query("SELECT * FROM complaints");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            font-size: 26px;
            margin-bottom: 20px;
        }

        /* Card Container */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        /* Card Styling */
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        /* Action Buttons */
        a, button {
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin: 10px 0;
            width: calc(100% - 20px); /* Adjust width to account for margins */
        }

        a:hover, button:hover {
            opacity: 0.8;
        }
        
        /* Footer Styling */
        footer {
            text-align: center;
            padding: 20px;
            background-color: #34495e;
            color: white;
            font-size: 16px;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Specific Button Colors */
        .manage-complaints { background-color: #3498db; }
        .add-notice { background-color: #1abc9c; }
        .add_blog { background-color: #3498db; }
        .manage-users { background-color: #f39c12; }
        .update-profile { background-color: #6B8E23; }
        .logout { background-color: #e74c3c; }
        .manage-gallery { background-color: #9b59b6; } /* New button color for gallery */
    </style>
</head>
<body>
    <h2>Welcome, Admin <?php echo $_SESSION['username']; ?>!</h2>

    <div class="card-container">

        <div class="card">
            <h3>Manage Complaints</h3>
            <?php
            // Fetch the number of complaints with status "open"
            $openComplaints = $conn->query("SELECT COUNT(*) as open_count FROM complaints WHERE status = 'open'");
            $openCount = $openComplaints->fetch_assoc()['open_count'];
            ?>
            
            <p>
                <?php if ($openCount > 0): ?>
                    You have <?php echo $openCount; ?> complaint(s) to resolve.
                <?php else: ?>
                    No complaints to manage.
                <?php endif; ?>
            </p>

            <a href="manage_complaints.php" class="manage-complaints">
                <i class="fas fa-exclamation-circle"></i> View Complaints
            </a>
        </div>

        <div class="card">
            <h3>Manage Notices</h3>
            <a href="add_notice.php" class="add-notice"><i class="fas fa-plus-circle"></i> Add New Notice</a>
        </div>

        <div class="card">
            <h3>Manage Blogs</h3>
            <a href="add_blog.php" class="add_blog"><i class="fas fa-blog"></i> Add New Blog</a>
        </div>

        <div class="card">
            <h3>User Management</h3>
            <a href="manage_users.php" class="manage-users"><i class="fas fa-users"></i> View All Users</a>
        </div>

        <div class="card">
            <h3>Manage Gallery</h3> <!-- New Gallery Card -->
            <a href="add_gallery.php" class="manage-gallery"><i class="fas fa-images"></i> Add New Image</a>
        </div>

        <div class="card">
            <h3>Admin Profile</h3>
            <a href="update_admin_profile.php" class="update-profile"><i class="fas fa-user-edit"></i> Update Profile</a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

    </div><br>
    <footer>
        <p>UIU Playground Management System 2024</p>
        <p>United City, Madani Ave, Dhaka 1212</p>
    </footer>

</body>
</html>
