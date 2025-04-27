<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenant_id = (int)$_POST['tenant_id'];
    $flat_id = (int)$_POST['flat_id'];
    $agent_id = $_SESSION['user_id'];

// Validate recommendation doesn't exist
$check_stmt = mysqli_prepare($con, 
    "SELECT id FROM recommendations 
     WHERE tenant_id = ? AND flat_id = ?");
mysqli_stmt_bind_param($check_stmt, 'ii', $tenant_id, $flat_id);
mysqli_stmt_execute($check_stmt);

// Store the result and check rows
mysqli_stmt_store_result($check_stmt);
if (mysqli_stmt_num_rows($check_stmt) === 0) {
    // Insert recommendation
    $insert_stmt = mysqli_prepare($con,
        "INSERT INTO recommendations (tenant_id, flat_id, agent_id)
         VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insert_stmt, 'iii', $tenant_id, $flat_id, $agent_id);
    mysqli_stmt_execute($insert_stmt);
}

// Free result
mysqli_stmt_free_result($check_stmt);
    
    header("Location: agent.php");
    exit;
}