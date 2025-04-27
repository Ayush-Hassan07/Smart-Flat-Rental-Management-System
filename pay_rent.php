<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    die("Access denied");
}

$tenant_id = $_SESSION['user_id'];
$flat_id = isset($_GET['flat_id']) ? intval($_GET['flat_id']) : 0;

if ($flat_id === 0) {
    die("Invalid Flat ID");
}

$month = date('m');
$year = date('Y');

// Fetch flat rent
$flat_query = mysqli_query($con, "SELECT * FROM flats WHERE id = $flat_id");
if (mysqli_num_rows($flat_query) == 0) {
    die("Flat not found");
}
$flat = mysqli_fetch_assoc($flat_query);
$monthly_rent = (float) $flat['rent'];

// Get agreement_id for this tenant & flat
function getAgreementId($tenant_id, $flat_id, $con) {
    $stmt = mysqli_prepare($con, "SELECT id FROM rental_agreements WHERE user_id = ? AND flat_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ii", $tenant_id, $flat_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['id'];
    }
    return null;
}

$agreement_id = getAgreementId($tenant_id, $flat_id, $con);

// Get existing payment data (if any)
$pay_query = mysqli_query($con, "SELECT * FROM payments WHERE tenant_id = $tenant_id AND flat_id = $flat_id AND month = $month AND year = $year");
$payment = mysqli_fetch_assoc($pay_query);

$advance = $payment['advance_paid'] ?? 0;
$paid = $payment['amount_paid'] ?? 0;
$total_payable = $monthly_rent - $advance;
$due = $total_payable - $paid;

// Handle form submission
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pay_amount = floatval($_POST['pay_amount']);
    $status = 'Pending';

    if ($pay_amount <= 0 || $pay_amount > $due) {
        echo "<script>alert('Invalid payment amount.');</script>";
    } else {
        $user_id = $_SESSION['user_id'];
        $remaining_due = max(0, $monthly_rent - ($paid + $pay_amount));
        if ($pay_amount == 0) {
            $status = 'Pending';
        } elseif ($remaining_due == 0) {
            $status = 'Paid';
        } else {
            $status = 'Due';
        }
        
        if ($payment) {
            $new_paid = $paid + $pay_amount;
            $stmt = mysqli_prepare($con, "UPDATE payments 
                SET amount_paid = ?, amount = ?, remaining_due = ?, status = ?, total_rent = ? 
                WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "dddsdi", $new_paid, $pay_amount, $remaining_due, $status, $monthly_rent, $payment['id']);
            mysqli_stmt_execute($stmt);
        } else {
            $advance_paid = 0;
            $stmt = mysqli_prepare($con, "INSERT INTO payments 
                (tenant_id, flat_id, month, year, total_rent, amount_paid, advance_paid, user_id, agreement_id, amount, remaining_due, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiidddiidds", 
                $tenant_id, $flat_id, $month, $year, $monthly_rent, $pay_amount, $advance_paid, 
                $user_id, $agreement_id, $pay_amount, $remaining_due, $status
            );
            mysqli_stmt_execute($stmt);
        }

        // Update the flat status to 'rented' if the rent is fully paid
        if ($remaining_due == 0) {
            $update_flat_stmt = mysqli_prepare($con, "UPDATE flats SET status = 'rented' WHERE id = ?");
            mysqli_stmt_bind_param($update_flat_stmt, "i", $flat_id);
            mysqli_stmt_execute($update_flat_stmt);
        }

        header("Location: pay_rent.php?flat_id=$flat_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pay Rent</title>
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
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center text-primary">Monthly Rent Payment</h2>

        <div class="mb-3">
            <h5>Flat: <?= htmlspecialchars($flat['flat_name']) ?> (৳<?= number_format($monthly_rent, 2) ?>)</h5>
            <p><strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?></p>
        </div>

        <table class="table table-bordered table-hover">
            <tr><th>Advance Paid</th><td>৳<?= number_format($advance, 2) ?></td></tr>
            <tr><th>Total Payable</th><td>৳<?= number_format($total_payable, 2) ?></td></tr>
            <tr><th>Amount Paid</th><td>৳<?= number_format($paid, 2) ?></td></tr>
            <tr><th>Remaining Due</th><td><strong class="text-danger">৳<?= number_format(max(0, $due), 2) ?></strong></td></tr>
        </table>

        <?php if ($due > 0): ?>
            <form method="post">
                <div class="mb-3">
                    <label for="pay_amount" class="form-label">Enter Payment Amount</label>
                    <input type="number" name="pay_amount" id="pay_amount" step="0.01" min="1" max="<?= $due ?>" required class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Settle Payment</button>
            </form>
        <?php else: ?>
            <div class="alert alert-success text-center">You have fully paid for this month. ✅</div>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="payments.php" class="btn btn-secondary">
                ⬅️ Back to Payment History
            </a>
        </div>
    </div>
</div>
</body>
</html>