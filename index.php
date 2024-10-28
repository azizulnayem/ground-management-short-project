<!-- index.php -->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UIU Ground Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');
        
        /* Body and Font Styling */
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Header Styling */
        header {
            background-color: #2c3e50;
            padding: 10px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            color: #fff;
            font-size: 26px;
            letter-spacing: 2px;
            margin: 0;
        }

        header nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #1abc9c;
            border-radius: 50px;
            transition: background-color 0.3s;
            font-weight: 600;
        }

        header nav a:hover {
            background-color: #16a085;
        }

        /* Hero Section Styling */
        .hero-section {
            background: url('images1/pic3.jpg') no-repeat center center/cover;
            height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            position: relative;
            box-shadow: inset 0 0 0 1000px rgba(44, 62, 80, 0.5);
        }

        .hero-section h1 {
            font-size: 50px;
            font-weight: 600;
            margin: 0;
            letter-spacing: 1.5px;
        }

        .hero-section p {
            font-size: 18px;
            margin: 15px 0;
        }

        .hero-buttons button {
            padding: 15px 25px;
            background-color: #f56c27;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .hero-buttons button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        /* Card and Container Styling */
        .container {
            width: 85%;
            margin: 0 auto;
            margin-bottom: 30px;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 1200px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card h3 {
            font-size: 22px;
            margin: 15px 0;
            color: #34495e;
        }

        .card p {
            color: #7f8c8d;
        }

        .card button {
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .card button:hover {
            background-color: #229954;
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
    </style>
</head>
<body>

    <header>
        <h1>United International University</h1> <br><br>
        <nav>
            <a href="gallery.php">Gallery</a> |
            <a href="blog2.php">Blog</a> |
            <a href="notice.php">Notices</a> |
            <a href="make_reservation.php">Reserve Ground</a> |
            <a href="complaint.php">Submit Complaint</a>
            <?php if (isset($_SESSION['username'])): ?>
                | <a href="<?php echo $_SESSION['role'] == 'admin' ? 'admin.php' : 'user.php'; ?>">Dashboard</a> |
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="hero-section">
        <div>
            <h1>Welcome to UIU Playground</h1>
            <p>UIU Playground Management System.</p>
            <div class="hero-buttons">
                <button onclick="window.location.href='auth.php';">Sign In</button>
                <button onclick="window.location.href='auth.php?action=register';">Sign Up</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Event Categories</h2>
        <div class="card-container">
            <div class="card">
                <h3>Cultural Event</h3>
                <p>Host your cultural event at the UIU Playground, a perfect venue for celebrating traditions, music, and creativity. 
                   With ample space for performances and festivities, it’s the ideal spot to showcase talent and bring people together. 
                   Book now to make your cultural celebration memorable!</p>
                <p><img src="images1/pic1.jpg" alt="Cultural event" style="width:650px;height:300px;"></p>
                <button onclick="window.location.href='make_reservation.php';">Book Now</button>
            </div>
            <div class="card">
                <h3>Sports Event</h3>
                <p>Organize your sports tournaments at the UIU Playground, offering spacious grounds for football, cricket, and more.
                   Whether it’s a friendly match or a major competition, our venue is equipped to host exciting sports events.
                   Reserve your spot today!</p>
                <p><img src="images1/pic2.jpg" alt="Sports event" style="width:650px;height:300px;"></p>
                <button onclick="window.location.href='make_reservation.php';">Book Now</button>
            </div>
            <div class="card">
                <h3>Job Fair</h3>
                <p>The UIU Playground is an excellent choice for hosting job fairs, providing space for company booths and networking in a professional outdoor setting. Connect students with employers in an engaging environment. Book your job fair now!</p>
                <p><img src="images1/job.jpeg" alt="Cultural event" style="width:650px;height:300px;"></p>
                <button onclick="window.location.href='make_reservation.php';">Book Now</button>
            </div>
        </div>
    </div>

    <footer>
        <p>UIU Playground Management System 2024</p>
        <p>United City, Madani Ave, Dhaka 1212</p>
    </footer>

</body>
</html>
