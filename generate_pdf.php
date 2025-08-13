<?php
require_once 'admin/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include 'config.php';
session_start();

$usermail = $_SESSION['usermail'] ?? '';
if (!$usermail) {
    die("Unauthorized access.");
}

$sql = "SELECT id, Name, RoomType, Bed, Meal, NoofRoom, cin, cout, nodays, stat FROM roombook WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usermail);
$stmt->execute();
$result = $stmt->get_result();

// Start HTML output buffering
ob_start();
?>

<h2 style="text-align: center;">My Booking Report</h2>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead style="background-color: #f0f0f0;">
        <tr>
            <th>Booking ID</th>
            <th>Name</th>
            <th>Room Type</th>
            <th>Bed</th>
            <th>Meal</th>
            <th>No. of Rooms</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>No. of Days</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['RoomType']) ?></td>
                    <td><?= htmlspecialchars($row['Bed']) ?></td>
                    <td><?= htmlspecialchars($row['Meal']) ?></td>
                    <td><?= htmlspecialchars($row['NoofRoom']) ?></td>
                    <td><?= htmlspecialchars($row['cin']) ?></td>
                    <td><?= htmlspecialchars($row['cout']) ?></td>
                    <td><?= htmlspecialchars($row['nodays']) ?></td>
                    <td><?= htmlspecialchars($row['stat']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10" align="center">No bookings found</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("MyBookingStatus.pdf", ["Attachment" => 1]);
?>
