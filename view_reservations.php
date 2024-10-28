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
// Check for a success message
if (isset($_GET['message'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['message']) . "</div>";
}

// Get user reservations
$user_id = $_GET['user_id'];
$query = "SELECT * FROM reservations WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reservations</title>
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
        .alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 5px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

    </style>
</head>
<body>

<h2>User Reservations</h2>

<table>
    <tr>
        <th>Reservation ID</th>
        <th>Event Type</th>
        <th>Date</th>
        <th>Time Slot</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($reservation = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
            <td><?php echo htmlspecialchars($reservation['event_type']); ?></td>
            <td><?php echo htmlspecialchars($reservation['date']); ?></td>
            <td><?php echo htmlspecialchars($reservation['time_slot']); ?></td>
            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
            <td>
                <a href="cancel_reservation.php?reservation_id=<?php echo $reservation['reservation_id']; ?>" onclick="return confirm('Are you sure you want to cancel this reservation?');" class="button"><i class="fas fa-times"></i> Cancel</a>
                <a href="approve_reservation.php?reservation_id=<?php echo $reservation['reservation_id']; ?>" class="button"><i class="fas fa-check"></i> Approve</a>
                <a href="delete_reservation.php?reservation_id=<?php echo $reservation['reservation_id']; ?>" onclick="return confirm('Are you sure you want to delete this reservation?');" class="button"><i class="fas fa-trash-alt"></i> Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="manage_users.php" class="button"><i class="fas fa-arrow-left"></i> Back to Manage Users</a>

</body>
</html>
