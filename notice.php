<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all notices
$notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Basic Reset */
        body, h1, h2, h3, p {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f9f9f9;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .notice-container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the cards horizontally */
            gap: 20px;
        }

        .notice {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
            max-width: 600px; /* Set a max width for the cards */
            width: 100%; /* Ensure it takes full width within max */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
        }

        .notice:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .notice h2 {
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .notice p {
            color: #555;
            line-height: 1.6;
        }

        .notice img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin: 10px 0;
        }

        .notice-time {
            font-size: 0.9em;
            color: #999;
            position: absolute;
            bottom: 10px;
            right: 15px;
        }

        .posted-by {
            font-weight: bold;
            color: #4CAF50;
        }

        /* Media Queries for Responsive Design */
        @media (max-width: 600px) {
            .notice {
                padding: 10px;
            }
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

    <h1>All Notices</h1>
    <div class="notice-container">
        <?php while ($notice = $notices->fetch_assoc()): ?>
            <div class="notice">
                <h2><?php echo htmlspecialchars($notice['title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($notice['description'])); ?></p>
                <?php if (!empty($notice['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($notice['image_path']); ?>" alt="Notice Image">
                <?php endif; ?>
                <div class="notice-time">
                    <span class="posted-by">Posted by Admin</span> | 
                    <span><?php echo htmlspecialchars($notice['created_at']); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
        <a href="index.php" class="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</body>
</html>
