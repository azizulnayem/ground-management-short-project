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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = $_POST['reservation_id'];
    $paymentMethod = $_POST['payment_method'];
    $amount = (float) $_POST['amount'];
    $phoneNumber = $_POST['phone_number'];

    // Insert payment into the database
    $paymentStmt = $conn->prepare("INSERT INTO payments (user_id, reservation_id, amount, payment_method, phone_number, status) VALUES (?, ?, ?, ?, ?, 'completed')");
    $paymentStmt->bind_param("iisss", $_SESSION['user_id'], $reservationId, $amount, $paymentMethod, $phoneNumber);
    
    if ($paymentStmt->execute()) {
        // Update the reservation status to 'approved'
        $updateStmt = $conn->prepare("UPDATE reservations SET status = 'approved' WHERE reservation_id = ?");
        $updateStmt->bind_param("i", $reservationId);
        $updateStmt->execute();
        $updateStmt->close();

        // Redirect to the same page to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . "?payment_success=1");
        exit();
    } else {
        echo "<p>Error: " . $paymentStmt->error . "</p>";
    }
    $paymentStmt->close();
}

// Fetch user's reservations
$userId = $_SESSION['user_id'];
$reservations = $conn->query("SELECT * FROM reservations WHERE user_id = $userId AND status = 'pending'");

// Fetch previous payments for display
$payments = $conn->query("SELECT * FROM payments WHERE user_id = $userId ORDER BY created_at DESC");

require('fpdf.php'); // Include FPDF library

function generatePaymentSlip($payment, $reservation) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Payment Slip', 0, 1, 'C');
    $pdf->Ln(10);

    // Section: Payment Details
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Payment Details', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    // Payment Table Headers
    $pdf->SetFillColor(220, 220, 220); // Light gray background for headers
    $pdf->Cell(40, 10, 'Payment ID', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'User ID', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Reservation ID', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Amount', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Payment Method', 1, 0, 'C', true);
    $pdf->Ln();

    // Payment Table Data
    $pdf->Cell(40, 10, $payment['payment_id'], 1);
    $pdf->Cell(30, 10, $payment['user_id'], 1);
    $pdf->Cell(30, 10, $payment['reservation_id'], 1);
    $pdf->Cell(30, 10, 'BDT ' . number_format($payment['amount'], 2), 1);
    $pdf->Cell(40, 10, ucfirst($payment['payment_method']), 1);
    $pdf->Ln(15);

    // Section: Reservation Details
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Reservation Details', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(50, 10, 'Event Type:', 0, 0, 'L');
    $pdf->Cell(0, 10, $reservation['event_type'], 0, 1, 'L');
    $pdf->Cell(50, 10, 'Date:', 0, 0, 'L');
    $pdf->Cell(0, 10, $reservation['date'], 0, 1, 'L');
    $pdf->Cell(50, 10, 'Time Slot:', 0, 0, 'L');
    $pdf->Cell(0, 10, $reservation['time_slot'], 0, 1, 'L');
    $pdf->Ln(10);

    // Footer Note
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Thank you for your payment!', 0, 1, 'C');

    // Output the PDF as a downloadable file
    $pdf->Output('D', 'payment_slip_' . $payment['payment_id'] . '.pdf');
    exit();
}

// Check if the user wants to download a payment slip
if (isset($_GET['download_payment_id'])) {
    $paymentId = $_GET['download_payment_id'];
    $result = $conn->query("SELECT * FROM payments WHERE payment_id = $paymentId");
    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();

        // Fetch reservation details to include in the slip
        $reservationResult = $conn->query("SELECT * FROM reservations WHERE reservation_id = {$payment['reservation_id']}");
        $reservation = $reservationResult->fetch_assoc();

        generatePaymentSlip($payment, $reservation);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset and Overall Styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f3f6fa;
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
            font-weight: 600;
            margin-top: 15px;
            display: inline-block;
            color: #333;
        }

        form input[type="text"],
        form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
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

        /* Payment Icons */
        .payment-icons {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .payment-icons img, 
        .payment-icons i {
            width: 30px;
            height: 30px;
        }

        /* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

table th,
table td {
    padding: 12px;
    text-align: center;
    font-size: 14px;
}

table th {
    background-color: #0044cc;
    color: #fff;
    font-weight: 600;
}

table td {
    background-color: #f9f9f9;
    border-bottom: 1px solid #ddd;
}

table tr:nth-child(even) td {
    background-color: #eaeff5;
}

table tr:hover td {
    background-color: #d1e0ff; /* Highlight on hover */
}


        /* Action Buttons */
        .button {
            display: inline-block;
            background-color: #333;
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
            background-color: #555;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
        label[for="password"] {
    font-weight: 600;
    margin-top: 15px;
    display: inline-block;
    color: #333;
}

input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    color: #555;
}
    </style>
    <script>
        function updateAmount(select) {
        const amountInput = document.getElementById('amount');
        const selectedOption = select.options[select.selectedIndex];
        const amount = selectedOption.getAttribute('data-amount');

        // Update the amount input field with the fetched amount
        amountInput.value = amount ? amount : '';
    }
    </script>
</head>
<body>
<?php if (isset($_GET['payment_success'])): ?>
    <div style="color: green; text-align: center; margin-bottom: 20px;">
        <strong>Payment completed successfully!</strong>
    </div>
<?php endif; ?>

    <!-- Payment Form Container -->
    <div class="container">
        <h2>Make a Payment</h2>
        <form method="POST" action="">
    <label for="reservation_id">Select Reservation:</label>
    <select id="reservation_id" name="reservation_id" required onchange="updateAmount(this)">
        <option value="">-- Select Reservation --</option>
        <?php while ($reservation = $reservations->fetch_assoc()): ?>
            <option value="<?php echo $reservation['reservation_id']; ?>" data-amount="<?php echo $reservation['price']; ?>">
                <?php echo $reservation['event_type']; ?> - BDT <?php echo $reservation['price']; ?>
            </option>
        <?php endwhile; ?>
    </select>

            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required readonly>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required pattern="01[0-9]{9}" maxlength="11" placeholder="Enter your phone number">

            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="">Select a payment method</option>
                <option value="bkash">bKash</option>
                <option value="nagad">Nagad</option>
                <option value="rocket">Rocket</option>
                <option value="upay">Upay</option>
                <option value="card">Card</option>
            </select>

            <div class="payment-icons">
                <img src="images/bkash.png" alt="bKash" title="bKash">
                <img src="images/nagad.png" alt="Nagad" title="Nagad">
                <img src="images/rocket.png" alt="Rocket" title="Rocket">
                <img src="images/upay.png" alt="Upay" title="Upay">
                <img src="images/card.png" alt="Card" title="Card">
            </div>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Pay Now">
        </form>
    </div>

    <!-- Payment History Container -->
    <div class="container">
        <h2>Your Payment History</h2>
        <table>
            <tr>
                <th>Payment ID</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php while ($payment = $payments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $payment['payment_id']; ?></td>
                    <td><?php echo $payment['amount']; ?></td>
                    <td><?php echo ucfirst($payment['payment_method']); ?></td>
                    <td><?php echo $payment['phone_number']; ?></td>
                    <td><?php echo $payment['status']; ?></td>
                    <td><?php echo $payment['created_at']; ?></td>
                    <td><a href="?download_payment_id=<?php echo $payment['payment_id']; ?>" class="button">Download Slip</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Navigation Buttons -->
    <a href="user.php" class="button">Back to Dashboard</a>
    <a href="logout.php" class="button">Logout</a>
</body>
</html>
