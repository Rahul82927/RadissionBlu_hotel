<?php
require '../config.php';
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$id = $_GET['id'];
$query = "SELECT * FROM payment WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$html = '
<h1>Invoice</h1>
<p><strong>Customer Name:</strong> ' . $row['name'] . '</p>
<p><strong>Email:</strong> ' . $row['email'] . '</p>
<p><strong>Phone:</strong> ' . $row['phone'] . '</p>
<p><strong>Room Type:</strong> ' . $row['room_type'] . '</p>
<p><strong>Amount:</strong> ₹' . $row['amount'] . '</p>
<p><strong>Transaction ID:</strong> ' . $row['transaction_id'] . '</p>
<p><strong>Date:</strong> ' . $row['created_at'] . '</p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("invoice_" . $row['id'] . ".pdf", array("Attachment" => 1));
?>
