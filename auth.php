<!-- auth.php -->
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ground");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Registration Logic
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $role = $_POST['role']; // Get role from form input

    // Check if the username is already taken
    $check_user = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check_user->num_rows > 0) {
        $register_error = "Username already exists. Please choose a different one.";
    } else {
        $conn->query("INSERT INTO users (username, password, email, role) VALUES ('$username', '$password', '$email', '$role')");
        $register_success = "Registration successful! You can now log in.";
    }
}

// Login Logic
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND role='$role'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit();
        } else {
            $login_error = "Invalid password. Please try again.";
        }
    } else {
        $login_error = "Username or role not found. Please check your credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UIU Ground Management - Auth</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #73a5ff, #5477f5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        /* Form container */
        .form-container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            text-align: center;
        }

        /* Form titles */
        h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 10px;
        }

        /* Input fields and button */
        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        select:focus {
            border-color: #5477f5;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            background: #5477f5;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background: #415ecf;
        }

        /* Links */
        a {
            color: #5477f5;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Font Awesome Icons */
        label i {
            margin-right: 8px;
            color: #5477f5;
        }
    </style>
</head>
<body>

<div class="form-container">
    <?php if (isset($_GET['action']) && $_GET['action'] == 'register'): ?>
        <h2><i class="fas fa-user-plus"></i> Register</h2>
        <?php if (isset($register_error)) echo "<p style='color:red;'>$register_error</p>"; ?>
        <?php if (isset($register_success)) echo "<p style='color:green;'>$register_success</p>"; ?>
        <form action="auth.php?action=register" method="post">
            <label><i class="fas fa-user"></i> Username:</label>
            <input type="text" name="username" required><br>
            <label><i class="fas fa-lock"></i> Password:</label>
            <input type="password" name="password" required><br>
            <label><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" required><br>
            <label><i class="fas fa-user-tag"></i> Role:</label>
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select><br>
            <button type="submit" name="register"><i class="fas fa-user-plus"></i> Register</button>
        </form>
        <p>Already have an account? <a href="auth.php">Login here</a></p>
    <?php else: ?>
        <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
        <?php if (isset($login_error)) echo "<p style='color:red;'>$login_error</p>"; ?>
        <form action="auth.php" method="post">
            <label><i class="fas fa-user"></i> Username:</label>
            <input type="text" name="username" required><br>
            <label><i class="fas fa-lock"></i> Password:</label>
            <input type="password" name="password" required><br>
            <label><i class="fas fa-user-tag"></i> Role:</label>
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select><br>
            <button type="submit" name="login"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <p>Don't have an account? <a href="auth.php?action=register">Register here</a></p>
    <?php endif; ?>
</div>

</body>
</html>
