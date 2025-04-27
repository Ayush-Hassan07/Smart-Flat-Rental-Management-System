<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['flat_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$flat_id = (int) $_GET['flat_id'];

// Get the most recent agreement for this flat/user
$stmt = mysqli_prepare($con, "SELECT id FROM rental_agreements WHERE user_id = ? AND flat_id = ? ORDER BY id DESC LIMIT 1");
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $flat_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$agreement = mysqli_fetch_assoc($result);

if (!$agreement) die("No agreement found.");

$agreement_id = $agreement['id'];

// Get rent amount
$flat_stmt = mysqli_prepare($con, "SELECT rent FROM flats WHERE id = ?");
mysqli_stmt_bind_param($flat_stmt, 'i', $flat_id);
mysqli_stmt_execute($flat_stmt);
$flat_result = mysqli_stmt_get_result($flat_stmt);
$flat_data = mysqli_fetch_assoc($flat_result);

$rent = $flat_data['rent'] ?? 0;

// Update agreement status
$update_stmt = mysqli_prepare($con, "UPDATE rental_agreements SET status = 'Agreed' WHERE id = ?");
mysqli_stmt_bind_param($update_stmt, 'i', $agreement_id);
mysqli_stmt_execute($update_stmt);

// Add to payments
$payment_stmt = mysqli_prepare($con, "INSERT INTO payments (user_id, agreement_id, amount, status) VALUES (?, ?, ?, 'Pending')");
mysqli_stmt_bind_param($payment_stmt, 'iid', $user_id, $agreement_id, $rent);
mysqli_stmt_execute($payment_stmt);

header("Location: payments.php");
exit;
?>
