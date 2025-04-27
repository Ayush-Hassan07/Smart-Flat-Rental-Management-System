<?php
session_start();
require 'dbconnect.php';

// Validate authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$location = trim($_POST['location'] ?? '');
$max_rent = (int)($_POST['max_rent'] ?? 0);
$min_bedrooms = (int)($_POST['min_bedrooms'] ?? 1);

try {
    // Check if preferences exist
    $check = mysqli_prepare($con, "SELECT user_id FROM tenant_preferences WHERE user_id = ?");
    if (!$check) throw new Exception("Database error: " . mysqli_error($con));
    
    mysqli_stmt_bind_param($check, 'i', $user_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check); // Needed for num_rows
    
    $exists = (mysqli_stmt_num_rows($check) > 0);
    mysqli_stmt_close($check);

    if ($exists) {
        // Update existing preferences
        $stmt = mysqli_prepare($con,
            "UPDATE tenant_preferences SET
             preferred_location = ?,
             max_rent = ?,
             min_bedrooms = ?
             WHERE user_id = ?");
        $types = 'siii'; // string, int, int, int
    } else {
        // Insert new preferences
        $stmt = mysqli_prepare($con,
            "INSERT INTO tenant_preferences 
             (preferred_location, max_rent, min_bedrooms, user_id)
             VALUES (?, ?, ?, ?)");
        $types = 'siii'; // string, int, int, int
    }

    if (!$stmt) throw new Exception("Database error: " . mysqli_error($con));
    
    // Bind parameters based on operation
    if ($exists) {
        mysqli_stmt_bind_param($stmt, $types, $location, $max_rent, $min_bedrooms, $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, $types, $location, $max_rent, $min_bedrooms, $user_id);
    }

    mysqli_stmt_execute($stmt);
    
    header("Location: tenant.php");
    exit;

} catch(Exception $e) {
    error_log($e->getMessage());
    die("Error saving preferences. Please try again later.");
}