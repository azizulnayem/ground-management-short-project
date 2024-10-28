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

// Fetch all reservations
$query = "SELECT r.*, u.username FROM reservations r JOIN users u ON r.user_id = u.user_id";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
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
        table {
            width: 100%;
            max-width: 800px;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        .actions a {
            margin: 0 5px;
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
            margin-top: 20px;
        }
        .button:hover {
            background-color: #002b80;
        }
    </style>
</head>
<body>

<h2>Manage Reservations</h2>

<table>
    <tr>
        <th>Reservation ID</th>
        <th>Username</th>
        <th>Event Type</th>
        <th>Date</th>
        <th>Time Slot</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($reservation = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
            <td><?php echo htmlspecialchars($reservation['username']); ?></td>
            <td><?php echo htmlspecialchars($reservation['event_type']); ?></td>
            <td><?php echo htmlspecialchars($reservation['date']); ?></td>
            <td><?php echo htmlspecialchars($reservation['time_slot']); ?></td>
            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
            <td class="actions">
                <a href="approve_reservation2.php?reservation_id=<?php echo htmlspecialchars($reservation['reservation_id']); ?>&user_id=<?php echo htmlspecialchars($reservation['user_id']); ?>">Approve</a>
                <a href="cancel_reservation2.php?reservation_id=<?php echo htmlspecialchars($reservation['reservation_id']); ?>" onclick="return confirm('Are you sure you want to cancel this reservation?');">Cancel</a>
                <a href="delete_reservation2.php?reservation_id=<?php echo htmlspecialchars($reservation['reservation_id']); ?>" onclick="return confirm('Are you sure you want to delete this reservation?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="admin.php" class="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
<a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>

</body>
</html>
