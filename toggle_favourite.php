<?php
session_start();
include 'dbconnect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$flat_id = intval($_POST['flat_id']);
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

try {
    if ($action === 'add') {
        $query = "INSERT INTO user_favourites (user_id, flat_id) VALUES (?, ?)";
    } else {
        $query = "DELETE FROM user_favourites WHERE user_id = ? AND flat_id = ?";
    }
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $flat_id);
    mysqli_stmt_execute($stmt);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>