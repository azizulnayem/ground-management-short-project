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

// Fetch current user data
$userId = $_SESSION['user_id'];
$userResult = $conn->query("SELECT * FROM users WHERE user_id = $userId");
$userData = $userResult->fetch_assoc();

$updateStatus = "";
$passwordStatus = "";
$deleteStatus = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $username, $email, $userId);
        if ($stmt->execute()) {
            $updateStatus = "Profile updated successfully!";
            $userData['username'] = $username;
            $userData['email'] = $email;
        } else {
            $updateStatus = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $storedPasswordHash = $stmt->get_result()->fetch_assoc()['password'];

        if (password_verify($current_password, $storedPasswordHash)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $new_password_hash, $userId);
            if ($stmt->execute()) {
                $passwordStatus = "Password changed successfully!";
            } else {
                $passwordStatus = "Error changing password: " . $stmt->error;
            }
        } else {
            $passwordStatus = "Current password is incorrect!";
        }
        $stmt->close();
    } elseif (isset($_POST['delete_account'])) {
        $password = $_POST['password'];
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $storedPasswordHash = $stmt->get_result()->fetch_assoc()['password'];

        if (password_verify($password, $storedPasswordHash)) {
            // Delete associated records in complaints, payments, and reservations tables
            $deleteComplaintsStmt = $conn->prepare("DELETE FROM complaints WHERE user_id = ?");
            $deleteComplaintsStmt->bind_param("i", $userId);
            $deleteComplaintsStmt->execute();
            $deleteComplaintsStmt->close();

            $deletePaymentsStmt = $conn->prepare("DELETE FROM payments WHERE user_id = ?");
            $deletePaymentsStmt->bind_param("i", $userId);
            $deletePaymentsStmt->execute();
            $deletePaymentsStmt->close();

            $deleteReservationsStmt = $conn->prepare("DELETE FROM reservations WHERE user_id = ?");
            $deleteReservationsStmt->bind_param("i", $userId);
            $deleteReservationsStmt->execute();
            $deleteReservationsStmt->close();

            // Now, delete the user
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            if ($stmt->execute()) {
                session_destroy();
                header("Location: auth.php");
                exit();
            } else {
                $deleteStatus = "Error deleting account.";
            }
        } else {
            $deleteStatus = "Incorrect password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
body {
    font-family: 'Poppins', sans-serif; 
    background-color: #f8f9fa; /* Light background color */
    color: #333; /* Dark text color for contrast */
    margin: 0;
    padding: 0;
}

.container {
    max-width: 600px; /* Reduced width for better fit */
    margin: 40px auto;
    padding: 20px;
    background: #ffffff; /* White background for the container */
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s; 
}

.container:hover {
    transform: scale(1.02); 
}

h2, h3 {
    text-align: center;
    color: #007bff; /* Blue header color */
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #007bff; /* Blue label color */
}

input[type="text"], input[type="email"], input[type="password"] {
    width: calc(100% - 20px); /* Full width minus padding */
    padding: 10px;
    border: 1px solid #007bff; /* Blue border color */
    border-radius: 5px;
    background-color: #f0f0f0; /* Light cream background for input */
    color: #333; /* Dark text color */
    transition: border-color 0.3s;
}

input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
    border-color: #0056b3; /* Darker blue on focus */
}

button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    color: #fff;
    background-color: #007bff; /* Blue button background */
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
    transition: background-color 0.3s; 
}

button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.delete-btn {
    background-color: #dc3545; /* Red button for delete */
}

.delete-btn:hover {
    background-color: #c82333; /* Darker red on hover */
}

.nav-buttons {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.nav-buttons a {
    color: #007bff; /* Blue nav link color */
    text-decoration: none;
    margin-left: 15px;
    transition: color 0.3s; 
}

.nav-buttons a:hover {
    color: #0056b3; /* Darker blue on hover */
}

.status-message {
    color: #007bff; /* Blue status message color */
    margin: 10px 0;
    font-weight: bold;
}

.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.9); /* White background for popup */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to your profile, <?php echo htmlspecialchars($userData['username']); ?>!</h2>
        
        <h3>Update Profile</h3>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            </div>
            <button type="submit" name="update_profile">Update Profile</button>
            <p><?php echo $updateStatus; ?></p>
        </form>

        <h3>Change Password</h3>
        <form method="POST">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <button type="submit" name="change_password">Change Password</button>
            <p><?php echo $passwordStatus; ?></p>
        </form>

        <h3>Delete Account</h3>
        <button class="delete-btn" onclick="showDeleteConfirm()">Delete Account</button>
        <p><?php echo $deleteStatus; ?></p>

        <div class="popup" id="deleteConfirm">
            <form method="POST">
                <p>Confirm Password:</p>
                <input type="password" name="password" required>
                <button type="submit" name="delete_account">Confirm Delete</button>
                <button type="button" onclick="closeDeleteConfirm()">Cancel</button>
            </form>
        </div>

        <div class="nav-buttons">
            <a href="user.php">Back</a>
            <a href="auth.php">Logout</a>
        </div>
    </div>

    <script>
        function showDeleteConfirm() {
            document.getElementById('deleteConfirm').style.display = 'block';
        }

        function closeDeleteConfirm() {
            document.getElementById('deleteConfirm').style.display = 'none';
        }
    </script>
</body>
</html>