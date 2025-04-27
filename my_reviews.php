<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user details
$user_query = mysqli_prepare($con, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($user_query, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($user_query);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($user_query));

// Fetch user's reviews
$stmt = mysqli_prepare($con,
    "SELECT flats.flat_name, reviews.* 
     FROM reviews 
     JOIN flats ON reviews.flat_id = flats.id 
     WHERE user_id = ? 
     ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$reviews = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    body {
        background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
    
    .dashboard-card {
    height: 100%;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .dashboard-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

</style>

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">AURORA Properties</a>
            <div class="ms-auto">
                <span class="navbar-text text-white me-3">
                    Welcome, <?= htmlspecialchars($user['name']) ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    
    <div class="container my-5">
        <h2 class="text-center mb-4">My Reviews</h2>
        
        <?php if(mysqli_num_rows($reviews) > 0): ?>
            <div class="row row-cols-1 g-4">
                <?php while($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($review['flat_name']) ?></h5>
                                <div class="text-warning mb-2">
                                    <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                                </div>
                                <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                <small class="text-muted">
                                    Reviewed on <?= date('M j, Y g:i a', strtotime($review['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                You haven't reviewed any properties yet
            </div>
        <?php endif; ?>
    </div>
</body>
</html>