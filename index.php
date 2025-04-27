<?php
session_start();
include 'dbconnect.php';

// Search functionality
$search_area = isset($_GET['search_area']) ? trim($_GET['search_area']) : '';
$minRent = isset($_GET['min_rent']) ? (int)$_GET['min_rent'] : null;
$maxRent = isset($_GET['max_rent']) ? (int)$_GET['max_rent'] : null;

// Build query with favorite status
$query = "SELECT f.*, 
          EXISTS(SELECT 1 FROM user_favourites WHERE user_id = ".(isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0)." AND flat_id = f.id) AS is_favourite
          FROM flats f 
          WHERE f.status";

if (!empty($search_area)) {
    $search_area = mysqli_real_escape_string($con, $search_area);
    $query .= " AND location LIKE '%$search_area%'";
}
if ($minRent !== null) $query .= " AND rent >= $minRent";
if ($maxRent !== null) $query .= " AND rent <= $maxRent";
$query .= " ORDER BY id DESC";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURORA Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            color: #f8f9fa;
        }

        .flat-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .flat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
        }

        .flat-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            position: relative;
        }

        .flat-card .image-container {
            position: relative;
        }

        .status-signal {
            position: absolute;
            bottom: 10px;
            right: 10px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 20px;
            text-transform: uppercase;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            border: 1px solid #fff;
            backdrop-filter: blur(5px);
        }

        .status-available {
            background-color: rgba(40, 167, 69, 0.8);
        }

        .status-rented {
            background-color: rgba(220, 53, 69, 0.8);
        }

        .flat-card .card-body {
            padding: 1.2rem;
            background: #ffffff;
            color: #333;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: bold;
        }

        .card-text {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .favourite-btn {
            cursor: pointer;
            background: none;
            border: none;
            transition: transform 0.2s, color 0.3s;
            font-size: 1.7rem;
            color: #6c757d;
        }

        .favourite-btn.active {
            color: #dc3545;
        }

        .favourite-btn:hover {
            transform: scale(1.15);
        }

        .navbar {
            background: linear-gradient(90deg, #007bff, #6610f2);
        }

        .btn-primary {
            background: linear-gradient(45deg, #007bff, #6610f2);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #520dc2);
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-outline-light {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">AURORA Properties</a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] === 'landlord'): ?>
                        <a href="landlord_dashboard.php" class="btn btn-light">
                            <i class="bi bi-speedometer2"></i> My Dashboard
                        </a>
                    <?php elseif($_SESSION['role'] === 'tenant'): ?>
                        <a href="tenant.php" class="btn btn-light">
                            <i class="bi bi-speedometer2"></i> My Dashboard
                        </a>
                        <a href="favourites.php" class="btn btn-light">
                            <i class="bi bi-heart-fill"></i> My Favourites
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-outline-light">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light">Login</a>
                <?php endif; ?>
            </div>
    </nav>

    <div class="container my-4">
        <h2 class="text-center mb-4">Find Your Perfect Flat</h2>
        
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search_area" class="form-control" placeholder="Search location..." 
                           value="<?= htmlspecialchars($search_area) ?>">
                </div>
                <div class="col-md-3">
                    <input type="number" name="min_rent" class="form-control" placeholder="Min rent" 
                           value="<?= $minRent ?>">
                </div>
                <div class="col-md-3">
                    <input type="number" name="max_rent" class="form-control" placeholder="Max rent" 
                           value="<?= $maxRent ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <div class="row">
            <?php while($flat = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 d-flex align-items-stretch mb-4">
                    <div class="card flat-card w-100">
                        
                        <div class="image-container">
                            <img src="uploads/<?= htmlspecialchars($flat['room_picture']) ?>" class="img-fluid" alt="Flat Image">
                            <div class="status-signal <?= $flat['status'] == 'available' ? 'status-available' : 'status-rented' ?>">
                                <?= ucfirst($flat['status']) ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($flat['flat_name']) ?></h5>
                            <p class="card-text mb-1">
                                <?= htmlspecialchars($flat['location']) ?>
                            </p>
                            <p class="card-text mb-1">
                                <strong>BDT <?= number_format($flat['rent']) ?>/month</strong>
                            </p>
                            <p class="card-text text-muted">
                                🛏 <?= $flat['bedrooms'] ?> &nbsp; 🚿 <?= $flat['bathrooms'] ?> &nbsp; 📐 <?= $flat['square_feet'] ?> sqft
                            </p>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="flat_details.php?flat_id=<?= $flat['id'] ?><?= isset($_SESSION['user_id']) ? '&user_id='.$_SESSION['user_id'] : '' ?>" 
                                class="btn btn-primary">
                                View Details
                                </a>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <button class="favourite-btn <?= $flat['is_favourite'] ? 'active' : '' ?>" 
                                            data-flat-id="<?= $flat['id'] ?>">
                                        <i class="bi bi-heart<?= $flat['is_favourite'] ? '-fill' : '' ?>"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle favorite toggling
        document.querySelectorAll('.favourite-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if(!confirm('Are you sure you want to ' + (this.classList.contains('active') ? 'remove from' : 'add to') + ' favorites?')) return;
                
                const flatId = this.dataset.flatId;
                const isFavourite = this.classList.contains('active');
                
                fetch('toggle_favourite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: flat_id=${flatId}&action=${isFavourite ? 'remove' : 'add'}
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        this.classList.toggle('active');
                        const icon = this.querySelector('i');
                        icon.classList.toggle('bi-heart');
                        icon.classList.toggle('bi-heart-fill');
                    } else {
                        alert('Operation failed: ' + (data.message || 'Unknown error'));
                    }
                });
            });
        });
    </script>
</body>
</html>
