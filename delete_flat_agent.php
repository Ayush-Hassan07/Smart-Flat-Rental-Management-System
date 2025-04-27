<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flat_id'])) {
    $flat_id = (int)$_POST['flat_id'];
    $agent_id = $_SESSION['user_id'];

    // Verify ownership before deletion
    $stmt = mysqli_prepare($con, 
        "DELETE FROM agent_flats 
        WHERE id = ? AND agent_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $flat_id, $agent_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        die("Delete failed: " . mysqli_stmt_error($stmt));
    }

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: agent.php");
    } else {
        die("No flat found or access denied");
    }
    exit;
}

header("Location: agent.php"); // Fallback redirect