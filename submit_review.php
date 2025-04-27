<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['user_id'])) { // Fixed syntax error here
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $flat_id = (int)$_POST['flat_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    // Validate inputs
    if ($rating < 1 || $rating > 5) {
        die("Invalid rating");
    }
    
    if (empty($comment) || strlen($comment) > 500) {
        die("Comment must be between 1-500 characters");
    }

    // Check if user already reviewed
    $check_stmt = mysqli_prepare($con, 
        "SELECT id FROM reviews 
         WHERE user_id = ? AND flat_id = ?");
    mysqli_stmt_bind_param($check_stmt, 'ii', $user_id, $flat_id);
    mysqli_stmt_execute($check_stmt);
    
    // Store result first
    mysqli_stmt_store_result($check_stmt);
    
    // Use stmt->num_rows instead
    if(mysqli_stmt_num_rows($check_stmt) > 0) {
        die("You've already reviewed this property");
    }

    // Insert review
    $insert_stmt = mysqli_prepare($con,
        "INSERT INTO reviews 
        (flat_id, user_id, rating, comment) 
        VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($insert_stmt, 'iiis', 
        $flat_id, $user_id, $rating, $comment);
    
    if(mysqli_stmt_execute($insert_stmt)) {
        header("Location: flat_details.php?flat_id=$flat_id");
        exit;
    } else {
        die("Error saving review: " . mysqli_error($con));
    }
}