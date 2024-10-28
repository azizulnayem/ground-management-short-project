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

// Define time slots with AM/PM format
$timeSlots = [
    "08:00 - 09:00 AM",
    "09:00 - 10:00 AM",
    "10:00 - 11:00 AM",
    "11:00 - 12:00 PM",
    "12:00 - 01:00 PM",
    "01:00 - 02:00 PM",
    "02:00 - 03:00 PM",
    "03:00 - 04:00 PM",
    "04:00 - 05:00 PM"
];

$price = 0;
$successMessage = ""; // Variable for success message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the reservation submission
    $eventType = $_POST['event_type'];
    $date = $_POST['date'];
    $timeSlot = $_POST['time_slot'];
    $price = $_POST['price']; // Get the price from the form submission

    // Prepare the statement to insert the reservation into the database
    $stmt = $conn->prepare("INSERT INTO reservations (user_id, event_type, date, time_slot, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssd", $_SESSION['user_id'], $eventType, $date, $timeSlot, $price);
    if ($stmt->execute()) {
        $successMessage = "Reservation made successfully!"; // Set success message
        $price = 0; // Reset price after successful reservation
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif; /* Use a more modern font */
            background: #f8f9fa; /* Light background for the whole page */
            margin: 0;
            padding: 20px;
            color: #333; /* Dark text for contrast */
        }

        h2 {
            color: #007bff; /* Blue color for headers */
            font-size: 2.5rem; /* Increased header size for emphasis */
            text-align: center; /* Center-align the header */
            margin-bottom: 20px;
            font-weight: 600; /* Semi-bold for header */
        }

        /* Success Message Styling */
        .success-message {
            background-color: #d4edda; /* Light green background */
            color: #155724; /* Dark green text */
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form Styling */
        form {
            background: #ffffff; /* White background for the form */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); /* Softer shadow */
            max-width: 500px; /* Increased width for better sizing */
            margin: 0 auto; /* Center the form */
            transition: transform 0.3s;
        }

        form:hover {
            transform: scale(1.02); /* Slightly scale on hover */
        }

        label {
            font-weight: bold;
            margin-top: 15px; /* More spacing above labels */
            display: block;
            color: #555; /* Dark grey for labels */
        }

        input[type="text"], input[type="date"], select {
            width: calc(100% - 24px); /* Full width minus padding */
            padding: 12px; /* Increased padding for comfort */
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px; /* Softer corners */
            font-size: 1rem;
            transition: border-color 0.3s; /* Transition for focus */
        }

        input[type="text"]:focus, input[type="date"]:focus, select:focus {
            border-color: #007bff; /* Blue border on focus */
            outline: none; /* Remove default outline */
        }

        input[type="submit"] {
            background-color: #007bff; /* Blue button background */
            color: white;
            font-size: 1.1rem; /* Slightly larger font size */
            cursor: pointer;
            padding: 12px; /* Increased padding for buttons */
            border: none;
            border-radius: 8px; /* Softer corners */
            transition: background-color 0.3s ease;
            width: 100%; /* Full width for the button */
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        /* Price Field Styling */
        input[type="text"][readonly] {
            background-color: #f2f2f2; /* Light grey background for read-only */
            color: #555;
            border: 1px solid #ddd;
        }

        /* Button Styling */
        .button {
            display: inline-block;
            padding: 10px 25px; /* More padding for buttons */
            text-decoration: none;
            color: white;
            background-color: #007bff; /* Blue button */
            border-radius: 8px;
            margin-top: 15px;
            font-size: 1rem; /* Larger font size for buttons */
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .button i {
            margin-right: 8px; /* Spacing for icons */
        }

        /* Links */
        a {
            text-decoration: none;
            color: #007bff; /* Blue for links */
            margin-right: 10px;
        }

        a:hover {
            color: #0056b3; /* Darker blue on hover */
        }

        /* Centered Buttons */
        div.text-center {
            text-align: center;
            margin-top: 30px; /* Space above the buttons */
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            h2 {
                font-size: 2rem; /* Smaller header on small screens */
            }

            form {
                padding: 20px; /* Less padding on small screens */
            }

            input[type="submit"] {
                padding: 10px; /* Smaller button padding */
            }

            .button {
                padding: 8px 20px; /* Smaller button padding */
            }
        }
    </style>
    <script>
        function updateTimeSlots() {
            const eventType = document.getElementById('event_type').value;
            const date = document.getElementById('date').value;

            // Enable the time slot selection only if both fields are filled
            const timeSlotSelect = document.getElementById('time_slot');
            if (eventType && date) {
                timeSlotSelect.disabled = false;
            } else {
                timeSlotSelect.disabled = true;
            }
        }

        function generatePrice() {
            const timeSlot = document.getElementById('time_slot').value;

            if (timeSlot) {
                // Generate a random price when a time slot is selected
                const randomPrice = Math.floor(Math.random() * (20000 - 5000 + 1)) + 5000; // Random price between 5000 to 20000
                document.getElementById('price').value = randomPrice; // Set the random price in the input field
            } else {
                document.getElementById('price').value = ''; // Clear price if no time slot is selected
            }
        }
    </script>
</head>
<body>
    <h2><i class="fas fa-calendar-plus"></i> Make a New Reservation</h2>
    
    <?php if ($successMessage): ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="event_type"><i class="fas fa-calendar-alt"></i> Event Type:</label>
        <input type="text" id="event_type" name="event_type" required onkeyup="updateTimeSlots()" />

        <label for="date"><i class="fas fa-calendar-day"></i> Date:</label>
        <input type="date" id="date" name="date" required onchange="updateTimeSlots()" />

        <label for="time_slot"><i class="fas fa-clock"></i> Time Slot:</label>
        <select id="time_slot" name="time_slot" required onchange="generatePrice()">
            <option value="">Select a time slot</option>
            <?php foreach ($timeSlots as $slot): ?>
                <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="price"><i class="fas fa-money-bill-wave"></i> Price:</label>
        <input type="text" id="price" name="price" value="<?php echo $price; ?>" readonly />

        <input type="submit" value="Reserve Now" />
    </form>

    <div class="text-center">
        <a href="user.php" class="button"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</body>
</html>
