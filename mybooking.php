<?php
include 'config.php';
session_start();

$usermail = $_SESSION['usermail'] ?? '';
if (!$usermail) {
    header("location: index.php");
    exit();
}

$sql = "SELECT id, Name, RoomType, Bed, Meal, NoofRoom, cin, cout, nodays, stat FROM roombook WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usermail);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Booking Status - RadissonBlu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-top: 40px;
        }
        h2 {
            font-weight: 600;
            margin-bottom: 25px;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .badge {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📋 My Booking Status</h2>

    <a href="generate_pdf.php" class="btn btn-primary mb-3">⬇️ Download PDF</a>

    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
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
                    <td>
                        <?php
                            $status = $row['stat'];
                            if ($status === 'Confirm') {
                                echo '<span class="badge bg-success">Confirmed</span>';
                            } elseif ($status === 'Cancel') {
                                echo '<span class="badge bg-danger">Canceled</span>';
                            } else {
                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                            }
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info">You have not made any bookings yet.</div>
    <?php endif; ?>
</div>

</body>
</html>
