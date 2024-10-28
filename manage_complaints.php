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

// Mark complaint as resolved
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['resolve'])) {
    $complaintId = $_POST['complaint_id'];
    $resolutionComment = $_POST['resolution_comment'];
    
    $stmt = $conn->prepare("UPDATE complaints SET status = 'resolved', resolution_comment = ? WHERE complaint_id = ?");
    $stmt->bind_param("si", $resolutionComment, $complaintId);
    $stmt->execute();
    $stmt->close();
}

// Fetch complaints
$result = $conn->query("SELECT * FROM complaints WHERE status = 'open'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Complaints</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .complaint-container {
            width: 100%;
            max-width: 700px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .complaint-card {
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .complaint-card:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .complaint-card h3 {
            color: #2980b9;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .complaint-card p {
            margin: 10px 0;
            color: #555;
            font-size: 0.95em;
        }
        .resolve-form {
            display: flex;
            flex-direction: column;
        }
        .resolve-form textarea {
            padding: 12px;
            font-size: 0.95em;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: none;
            transition: border-color 0.3s;
        }
        .resolve-form textarea:focus {
            border-color: #3498db;
        }
        .resolve-form button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        .resolve-form button:hover {
            background-color: #218838;
            transform: translateY(-2px);
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

    <div class="complaint-container">
        <h2><i class="fas fa-tasks"></i> Manage Complaints</h2>

        <?php while ($complaint = $result->fetch_assoc()): ?>
            <div class="complaint-card">
                <h3><i class="fas fa-exclamation-circle"></i> Complaint #<?php echo htmlspecialchars($complaint['complaint_id']); ?></h3>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($complaint['description']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
                
                <form method="POST" class="resolve-form">
                    <input type="hidden" name="complaint_id" value="<?php echo $complaint['complaint_id']; ?>">
                    <textarea name="resolution_comment" rows="3" placeholder="Enter resolution comment..." required></textarea>
                    <button type="submit" name="resolve"><i class="fas fa-check"></i> Mark as Resolved</button>
                </form>
            </div>
        <?php endwhile; ?>
              <!-- Navigation Buttons -->
    <a href="admin.php" class="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

</body>
</html>
