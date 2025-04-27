<?php
session_start();
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'landlord') {
    $flat_id = intval($_POST['flat_id']);
    $status = $_POST['status'];

    if (in_array($status, ['available', 'rented'])) {
        $stmt = $con->prepare("UPDATE flats SET status = ? WHERE id = ? AND landlord_id = ?");
        $stmt->bind_param("sii", $status, $flat_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: landlord_dashboard.php");
exit;
