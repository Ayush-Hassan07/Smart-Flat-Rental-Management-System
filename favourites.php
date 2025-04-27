<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch favorite flats with proper JOIN
$query = "SELECT f.* FROM flats f
          JOIN user_favourites uf ON f.id = uf.flat_id
          WHERE uf.user_id = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$favorites = mysqli_stmt_get_result($stmt);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorite Flats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .favorite-card {
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        .favorite-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .heart-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #dc3545;
        }
        .navbar {
            background: linear-gradient(90deg, #007bff, #6610f2);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">AURORA Properties</a>
            <div class="ms-auto">
                <a href="tenant.php" class="btn btn-light me-2">Back to Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
    <h2 class="text-center mb-4">My Favorite Flats</h2>

    <?php if(mysqli_num_rows($favorites) > 0): ?>
        <div class="row">
            <?php while($flat = mysqli_fetch_assoc($favorites)): ?>
                <?php
                    $main_image = $flat['room_picture'];
                    if (!empty($main_image) && !str_starts_with($main_image, 'http') && !str_starts_with($main_image, 'uploads/')) {
                        $main_image = 'uploads/' . $main_image;
                    }
                    $main_image = htmlspecialchars($main_image ?: 'assets/no-image.jpg');
                ?>
                <div class="col-md-4">
                    <div class="card h-100 favorite-card">
                        <img src="<?= $main_image ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($flat['flat_name']) ?></h5>
                            <p class="card-text">
                                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($flat['location']) ?><br>
                                <strong>BDT <?= number_format($flat['rent']) ?>/month</strong>
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between">
                                <a href="flat_details.php?flat_id=<?= $flat['id'] ?>" class="btn btn-primary">View Details</a>
                                <button class="heart-btn remove-favorite" data-flat-id="<?= $flat['id'] ?>">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-heart" style="font-size: 2rem;"></i><br>
            You haven't added any flats to your favorites yet.
        </div>
    <?php endif; ?>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Handle favorite removal
    document.querySelectorAll('.remove-favorite').forEach(btn => {
        btn.addEventListener('click', function() {
            const flatId = this.dataset.flatId;
            
            if(confirm('Remove this flat from favorites?')) {
                fetch('toggle_favourite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `flat_id=${flatId}&action=remove`
                })
                .then(response => {
                    if(response.ok) {
                        this.closest('.col-md-4').remove();
                        // If no favorites left, show message
                        if(document.querySelectorAll('.col-md-4').length === 0) {
                            document.querySelector('.row').innerHTML = `
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <i class="bi bi-heart" style="font-size: 2rem;"></i><br>
                                        You haven't added any flats to your favorites yet.
                                    </div>
                                </div>
                            `;
                        }
                    }
                });
            }
        });
    });
    </script>
</body>
</html>