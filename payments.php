<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    die("Login required.");
}

$user_id = $_SESSION['user_id'];

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    // Ensure user owns this payment
    $check_stmt = mysqli_prepare($con, "SELECT id FROM payments WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($check_stmt, 'ii', $delete_id, $user_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_fetch_assoc($check_result)) {
        $delete_stmt = mysqli_prepare($con, "DELETE FROM payments WHERE id = ?");
        mysqli_stmt_bind_param($delete_stmt, 'i', $delete_id);
        mysqli_stmt_execute($delete_stmt);
    }

    header("Location: payments.php");
    exit;
}

// Fetch payment history
$result = mysqli_query($con, "
    SELECT 
        payments.id AS payment_id,
        payments.amount_paid,
        payments.total_rent,
        payments.status AS payment_status,
        payments.payment_date,
        payments.flat_id,
        flats.flat_name,
        flats.location,
        flats.rent
    FROM payments
    JOIN flats ON payments.flat_id = flats.id
    WHERE payments.tenant_id = $user_id
    ORDER BY payments.payment_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">AURORA Properties</a>
        <div class="ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="tenant.php" class="btn btn-light me-2">
                    <i class="bi bi-speedometer2"></i> My Dashboard
                </a>
                <a href="favourites.php" class="btn btn-light me-2">
                    <i class="bi bi-heart-fill"></i> My Favourites
                </a>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-5 table-container">
    <h3 class="mb-4">My Payment History</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Flat</th>
                <th>Location</th>
                <th>Rent</th>
                <th>Paid Amount (BDT)</th>
                <th>Remaining Due</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <?php 
                    $total_rent = (float) $row['total_rent'];
                    $amount_paid = (float) $row['amount_paid'];
                    $remaining_due = max(0, $total_rent - $amount_paid);

                    $db_status = strtolower(trim($row['payment_status']));

                    // Badge color based on DB status
                    $badge_class = match ($db_status) {
                        'paid' => 'bg-success',
                        'pending' => 'bg-warning text-dark',
                        'due' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['flat_name']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td>৳<?= number_format((float) $row['rent'], 2) ?></td>
                    <td>৳<?= number_format($amount_paid, 2) ?></td>
                    <td>৳<?= number_format($remaining_due, 2) ?></td>
                    <td><span class="badge <?= $badge_class ?>"><?= ucfirst($db_status) ?></span></td>
                    <td><?= htmlspecialchars($row['payment_date']) ?></td>
                    <td class="d-flex gap-2">
                        <a href="payments.php?delete=<?= $row['payment_id'] ?>"
                        class="btn btn-danger btn-sm flex-fill"
                        onclick="return confirm('Are you sure you want to delete this payment record?');">
                        Delete
                        </a>
                        <a href="pay_rent.php?flat_id=<?= $row['flat_id'] ?>"
                        class="btn btn-success btn-sm flex-fill">
                        Pay Now
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
