<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: auth.php");
    exit();
}

// Handle the complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];

    // Insert complaint into the database with default status as "Open"
    $stmt = $conn->prepare("INSERT INTO complaints (user_id, description, status) VALUES (?, ?, 'Open')");
    $stmt->bind_param("is", $_SESSION['user_id'], $description);
    if ($stmt->execute()) {
        echo "<p>Complaint submitted successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Retrieve previous complaints
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT description, status, created_at, resolution_comment FROM complaints WHERE user_id='$user_id'");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset and Base Styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f7fb;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        /* Container Styling */
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
            margin: 20px;
            padding: 20px 40px;
        }

        h2 {
            color: #0044cc;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3f6fa;
            padding-bottom: 10px;
        }

        /* Form Styling */
        form label {
            font-weight: bold;
            color: #333;
        }

        form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            resize: vertical;
            color: #555;
        }

        form input[type="submit"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #0044cc;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form input[type="submit"]:hover {
            background-color: #0033a0;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            font-size: 14px;
            color: #333;
        }

        table th {
            background-color: #0044cc;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        table tr:nth-child(even) {
            background-color: #f3f6fa;
        }

        table tr:nth-child(odd) {
            background-color: #eaeff5;
        }

        /* Button Styling */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Complaint Form Container -->
    <div class="container">
        <h2><i class="fas fa-comments"></i> Submit a Complaint</h2>
        <form method="POST">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required placeholder="Describe your complaint..."></textarea>
            <input type="submit" value="Submit Complaint">
        </form>
    </div>

    <!-- Complaint History Container -->
    <div class="container">
        <h2><i class="fas fa-history"></i> Your Previous Complaints</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                    <th>Resolution Comment</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo isset($row['resolution_comment']) ? htmlspecialchars($row['resolution_comment']) : 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No previous complaints found.</p>
        <?php endif; ?>
    </div>

    <!-- Navigation Buttons -->
    <a href="user.php" class="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
</body>
</html>
