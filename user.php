<!-- user.php -->
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: auth.php");
    exit();
}

// Fetch user's reservations
$userId = $_SESSION['user_id'];
$reservations = $conn->query("SELECT * FROM reservations WHERE user_id = $userId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        /* Header */
        h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        h3 {
            color: #555;
            font-size: 1.4rem;
        }

        /* Buttons */
        .button {
            padding: 10px 20px;
            margin: 8px 5px 8px 0;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
            text-align: center;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .button i {
            margin-right: 8px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            color: #555;
        }

        th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #ddd;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Links */
        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            color: #0056b3;
        }

        /* Empty reservation message */
        p {
            color: #666;
            font-size: 1rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2><i class="fas fa-user"></i> User Dashboard</h2>
    
    <h3><i class="fas fa-calendar-check"></i> Your Reservations</h3>
    <a class="button" href="make_reservation.php"><i class="fas fa-plus-circle"></i> Make a New Reservation</a>
    <a class="button" href="payment.php"><i class="fas fa-wallet"></i> View Payments</a>
    <a class="button" href="complaint.php"><i class="fas fa-comments"></i> Submit Complaint</a>
    <a class="button" href="profile.php"><i class="fas fa-user-cog"></i> Update Profile</a>
    <a class="button" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <br><br>
    <a class="button" href="index.php"><i class="fas fa-arrow-left"></i> Back</a>

    <?php if ($reservations->num_rows > 0): ?>
        <table>
            <tr>
                <th><i class="fas fa-id-card"></i> Reservation ID</th>
                <th><i class="fas fa-calendar-alt"></i> Event Type</th>
                <th><i class="fas fa-calendar-day"></i> Date</th>
                <th><i class="fas fa-clock"></i> Time Slot</th>
                <th><i class="fas fa-info-circle"></i> Status</th>
            </tr>
            <?php while ($reservation = $reservations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $reservation['reservation_id']; ?></td>
                    <td><?php echo htmlspecialchars($reservation['event_type']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['date']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['time_slot']); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($reservation['status'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p><i class="fas fa-exclamation-circle"></i> You have no reservations.</p>
    <?php endif; ?>
</body>
</html>