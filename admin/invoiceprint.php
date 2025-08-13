<?php
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Connect to database
$conn = new mysqli("localhost", "bluebird_user", "password", "bluebirdhotel");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM payment WHERE id = $id LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("No invoice found.");
}

$payment = $result->fetch_assoc();

// Create HTML invoice content
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - ' . $payment['Name'] . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .invoice-box {
            max-width: 800px;
            padding: 30px;
            border: 1px solid #eee;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            line-height: 24px;
            color: #555;
        }
        h1 { text-align: center; }
        table { width: 100%; line-height: inherit; text-align: left; }
        table td { padding: 5px; vertical-align: top; }
        .heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .total td { font-weight: bold; }
        .download-btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 12px;
            background: #3498db;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h1>Hotel Invoice</h1>
        <table>
            <tr class="heading">
                <td>Field</td>
                <td>Details</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>' . $payment['Name'] . '</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>' . $payment['Email'] . '</td>
            </tr>
            <tr>
                <td>Room Type</td>
                <td>' . $payment['RoomType'] . ' - ' . $payment['Bed'] . '</td>
            </tr>
            <tr>
                <td>Check-In</td>
                <td>' . $payment['cin'] . '</td>
            </tr>
            <tr>
                <td>Check-Out</td>
                <td>' . $payment['cout'] . '</td>
            </tr>
            <tr>
                <td>No. of Days</td>
                <td>' . $payment['noofdays'] . '</td>
            </tr>
            <tr>
                <td>Room Charges</td>
                <td>₹' . number_format($payment['roomtotal'], 2) . '</td>
            </tr>
            <tr>
                <td>Bed Charges</td>
                <td>₹' . number_format($payment['bedtotal'], 2) . '</td>
            </tr>
            <tr>
                <td>Meal</td>
                <td>' . $payment['meal'] . '</td>
            </tr>
            <tr>
                <td>Meal Charges</td>
                <td>₹' . number_format($payment['mealtotal'], 2) . '</td>
            </tr>
            <tr class="total">
                <td>Total</td>
                <td>₹' . number_format($payment['finaltotal'], 2) . '</td>
            </tr>
        </table>
    </div>
    <form method="post" style="text-align: center;">
        <button name="download_pdf" class="download-btn">Download Invoice PDF</button>
    </form>
</body>
</html>
';

if (isset($_POST['download_pdf'])) {
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Invoice_" . $payment['id'] . ".pdf", ["Attachment" => 1]);
    exit;
}

echo $html;
?>
