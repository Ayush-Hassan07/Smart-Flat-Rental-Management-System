<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['landlord', 'agent'])) {
    header("Location: login.php");
    exit;
}

// Search functionality
$search_area = isset($_GET['search_area']) ? trim($_GET['search_area']) : '';
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;

// Base query
$query = "SELECT af.*, u.name AS agent_name 
          FROM agent_flats af
          JOIN users u ON af.agent_id = u.id
          WHERE 1=1";

// Add search filters
if (!empty($search_area)) {
    $search_area = mysqli_real_escape_string($con, $search_area);
    $query .= " AND af.location LIKE '%$search_area%'";
}
if ($minPrice !== null) $query .= " AND af.price >= $minPrice";
if ($maxPrice !== null) $query .= " AND af.price <= $maxPrice";
//$query .= " ORDER BY af.created_at DESC";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Properties for Sale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    .flat-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        border-radius: 10px;
        overflow: hidden;
    }

    .flat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
    }

    .flat-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .flat-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
        padding: 1rem;
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .card-text {
        font-size: 0.95rem;
        color: #555;
    }

    .favourite-btn {
        cursor: pointer;
        background: none;
        border: none;
        transition: transform 0.2s;
        padding: 0;
    }

    .favourite-btn.active i {
        color: #dc3545;
    }

    .favourite-btn:hover {
        transform: scale(1.1);
    }

    .login-required {
        position: relative;
    }

    .login-required::after {
        content: "Login required";
        position: absolute;
        bottom: -20px;
        left: 0;
        right: 0;
        font-size: 0.8rem;
        color: #dc3545;
        text-align: center;
    }
    .agent-badge {
            position: absolute;
            top: 10px;
            right: 10px;
    }
    .navbar {
            background: linear-gradient(90deg, #007bff, #6610f2);
    }
</style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="agent_flats.php">Properties for Sale</a>
            <div class="ms-auto">
                <?php if ($_SESSION['role'] === 'landlord'): ?>
                    <a href="landlord_dashboard.php" class="btn btn-light me-2">
                        <i class="bi bi-speedometer2"></i> My Dashboard
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Properties Available for Purchase</h2>
        
        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search_area" class="form-control" 
                           placeholder="Search location..." value="<?= htmlspecialchars($search_area) ?>">
                </div>
                <div class="col-md-3">
                    <input type="number" name="min_price" class="form-control" 
                           placeholder="Min price" value="<?= $minPrice ?>">
                </div>
                <div class="col-md-3">
                    <input type="number" name="max_price" class="form-control" 
                           placeholder="Max price" value="<?= $maxPrice ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($flat = mysqli_fetch_assoc($result)): ?>
                <div class="col">
                    <div class="card h-100 flat-card">
                        <span class="badge bg-primary agent-badge">Agent: <?= htmlspecialchars($flat['agent_name']) ?></span>
                        <img src="uploads/<?= htmlspecialchars($flat['room_picture']) ?>" class="card-img-top" alt="Flat image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($flat['flat_name']) ?></h5>
                            <p class="card-text mb-1">
                                <?= htmlspecialchars($flat['location']) ?>
                            </p>
                            <p class="card-text mb-1">
                                <strong>BDT <?= number_format($flat['price']) ?>/month</strong>
                            </p>
                            <p class="card-text text-muted">
                                🛏 <?= $flat['bedrooms'] ?> &nbsp; 🚿 <?= $flat['bathrooms'] ?> &nbsp; 📐 <?= $flat['square_feet'] ?> sqft
                            </p>
                            <div class="d-flex justify-content-between">
                            <a href="flat_details_landlord.php?id=<?= $flat['id'] ?>" class="btn btn-primary">View Details</a>
                
                                <button class="btn btn-success" data-bs-toggle="modal" 
                                        data-bs-target="#contactModal-<?= $flat['id'] ?>">
                                    Express Interest
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No properties found matching your criteria</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modals for contact -->
    <?php mysqli_data_seek($result, 0); ?>
    <?php while($flat = mysqli_fetch_assoc($result)): ?>
    <div class="modal fade" id="contactModal-<?= $flat['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Agent: <?= htmlspecialchars($flat['agent_name']) ?></p>
                    <p>Email: <?= htmlspecialchars($flat['contact_email']) ?></p>
                    <p>Phone: <?= htmlspecialchars($flat['contact_phone']) ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>