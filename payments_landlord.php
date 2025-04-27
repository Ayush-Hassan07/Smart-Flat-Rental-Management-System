<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    die("Login required.");
}

$user_id = $_SESSION['user_id']; // ✅ Moved up so it's available for delete block

// ✅ Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    // Ensure user owns this payment
    $check_stmt = mysqli_prepare($con, "SELECT id FROM payments_landlord WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($check_stmt, 'ii', $delete_id, $user_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_fetch_assoc($check_result)) {
        $delete_stmt = mysqli_prepare($con, "DELETE FROM payments_landlord WHERE id = ?");
        mysqli_stmt_bind_param($delete_stmt, 'i', $delete_id);
        mysqli_stmt_execute($delete_stmt);
    }

    header("Location: payments_landlord.php");
    exit;
}

// ✅ Fetch payment history
$query = "
SELECT 
    p.id,
    p.flat_id,
    p.total_price,
    p.status AS payment_status,
    f.flat_name,
    f.location
FROM payments_landlord AS p
JOIN sales_agreements AS r ON p.agreement_id = r.id
JOIN agent_flats AS f ON r.flat_id = f.id
WHERE p.user_id = ?
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
        .navbar {
            background: linear-gradient(90deg, #007bff, #6610f2);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="agent_flats.php">Properties for Sale</a>
            <div class="ms-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="landlord_dashboard.php" class="btn btn-light me-2">
                        <i class="bi bi-speedometer2"></i> My Dashboard
                    </a>
                        <a href="logout.php" class="btn btn-outline-light">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light">Login</a>
                <?php endif; ?>
        </div>
    </nav>
<div class="container mt-5 table-container">
    <h3 class="mb-4">My Payment History</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Flat</th>
                <th>Location</th>
                <th>Amount (BDT)</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['flat_name']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= $row['total_price'] ?></td>
                    <td><?= $row['payment_status'] ?></td>
                    <td>
                        <a href="payments_landlord.php?delete=<?= $row['id'] ?>" 
                        class="btn btn-danger btn-sm" 
                        onclick="return confirm('Are you sure you want to delete this payment record?');">
                        Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>