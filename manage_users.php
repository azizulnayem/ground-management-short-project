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

// Search functionality
$searchTerm = '';
$query = "SELECT u.*, COALESCE(r.status, 'No Reservations') AS reservation_status 
          FROM users u 
          LEFT JOIN reservations r ON u.user_id = r.user_id 
          WHERE u.role != 'admin'";

if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    if (!empty($searchTerm)) {
        $query .= " AND (u.username LIKE ? OR u.email LIKE ?)";
    }
}
$query .= " GROUP BY u.user_id"; // Group users without duplicating

$stmt = $conn->prepare($query);

if (!empty($searchTerm)) {
    $likeSearch = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }
        .search-bar input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            outline: none;
            font-size: 16px;
        }
        .search-bar button {
            padding: 10px 15px;
            border: none;
            border-radius: 0 5px 5px 0;
            background-color: #3498db;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #2980b9;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 600px;
        }
        .card-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }
        .card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .card h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .card p {
            margin: 5px 0;
        }
        .actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
        .actions a {
            text-decoration: none;
            color: #0044cc;
        }
        .actions a:hover {
            color: #002b80;
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
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #002b80;
        }
        .nav-buttons {
           display: flex;
           justify-content: flex-start; /* Aligns buttons to the left */
           gap: 15px;
           margin-top: 20px;
        }
        .nav-container {
           display: flex;
           justify-content: flex-start; /* Aligns content to the left */
           width: 100%;
           margin-top: 20px;
        }

         .nav-buttons {
           display: flex;
           gap: 15px;
        }

    </style>
</head>
<body>
    
<div class="nav-buttons">
    <a href="admin.php" class="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <a href="manage_reservations.php" class="button"><i class="fas fa-calendar-alt"></i> Manage Reservations</a> <!-- New Button -->
    <a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>



    <h2>Manage Users</h2>

    <form method="POST" class="search-bar">
        <input type="text" name="search" placeholder="Search by username or email" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>

    <!-- Wrapper for centering card-container -->
    <div class="wrapper">
        <div class="card-container">
            <?php while ($user = $result->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <p>Status: <?php echo htmlspecialchars($user['reservation_status']); ?></p>
                    <div class="actions">
                        <a href="view_reservations.php?user_id=<?php echo $user['user_id']; ?>"><i class="fas fa-eye"></i> View Reservations</a>
                        <a href="delete_user.php?user_id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash-alt"></i> Delete User</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
