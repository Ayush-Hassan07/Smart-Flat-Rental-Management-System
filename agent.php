<?php
session_start();
require 'dbconnect.php';

// Check role and authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$agent_id = $user_id;

// 2. Now handle tenant matches
// Modified tenant matches query - simpler matching
$tenant_matches = [];
$matches_stmt = mysqli_prepare($con,
    "SELECT 
        tp.user_id,
        u.name,
        tp.preferred_location,
        tp.max_rent,
        tp.min_bedrooms,
        f.id AS flat_id,
        f.flat_name,
        f.location AS flat_location,
        f.rent,
        f.bedrooms
     FROM tenant_preferences tp
     JOIN flats f ON f.location = tp.preferred_location
        AND f.rent <= tp.max_rent
        AND f.bedrooms >= tp.min_bedrooms
     JOIN users u ON tp.user_id = u.id");

if ($matches_stmt && mysqli_stmt_execute($matches_stmt)) {
    $matches_result = mysqli_stmt_get_result($matches_stmt);
    while ($row = mysqli_fetch_assoc($matches_result)) {
        $tenant_matches[] = $row;
    }
    mysqli_stmt_close($matches_stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
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

    .match-badge {
        position: absolute;
        right: -10px;
        top: -10px;
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
            <!-- Change from Aurora Properties to Properties for Sale -->
            <a class="navbar-brand fw-bold" href="agent_flats.php">Properties for Sale</a>
                <div class="ms-auto">
                    <?php if ($user_id): ?>
                        <a href="logout.php" class="btn btn-outline-light">Logout</a>
                    <?php endif; ?>
                </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-center mb-4">Agent Dashboard</h2>
        
    <!-- Listed Properties section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-house-door"></i> My Listed Properties</h4>
        </div>
        <div class="card-body">
            <a href="add_flat_agent.php" class="btn btn-success mb-3">Add New Property</a>
            <?php 
            $agent_flats = mysqli_query($con, 
                "SELECT * FROM agent_flats WHERE agent_id = " . $_SESSION['user_id']);
            ?>
            <?php if(mysqli_num_rows($agent_flats) > 0): ?>
            <div class="row row-cols-1 g-3">
                <?php while($flat = mysqli_fetch_assoc($agent_flats)): ?>
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($flat['flat_name']) ?></h5>
                            <p>Location: <?= htmlspecialchars($flat['location']) ?></p>
                            <p>Price: BDT <?= number_format($flat['price']) ?></p>
                            <div class="d-flex gap-2">
                                <a href="edit_flat_agent.php?id=<?= $flat['id'] ?>" 
                                class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="delete_flat_agent.php" onsubmit="return confirm('Are you sure?')">
                                    <input type="hidden" name="flat_id" value="<?= $flat['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">No properties listed yet</div>
        <?php endif; ?>
    </div>
</div>

        <!-- Tenant Matches Section -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0"><i class="bi bi-people-fill"></i> Tenant Matches</h4>
    </div>
    <div class="card-body">
        <?php if(!empty($tenant_matches)): ?>
            <div class="row row-cols-1 g-3">
                <?php foreach($tenant_matches as $match): ?>
                    <div class="col">
                        <div class="card position-relative">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Tenant Preferences</h5>
                                        <p><strong>Name:</strong> <?= htmlspecialchars($match['name']) ?></p>
                                        <p><strong>Location:</strong> <?= htmlspecialchars($match['preferred_location']) ?></p>
                                        <p><strong>Max Rent:</strong> BDT <?= number_format($match['max_rent']) ?></p>
                                        <p><strong>Min Bedrooms:</strong> <?= $match['min_bedrooms'] ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Matching Flat</h5>
                                        <p><strong>Flat Name:</strong> <?= htmlspecialchars($match['flat_name']) ?></p>
                                        <p><strong>Location:</strong> <?= htmlspecialchars($match['flat_location']) ?></p>
                                        <p><strong>Rent:</strong> BDT <?= number_format($match['rent']) ?></p>
                                        <p><strong>Bedrooms:</strong> <?= $match['bedrooms'] ?></p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="flat_details.php?flat_id=<?= $match['flat_id'] ?>" 
                                       class="btn btn-info me-2">
                                        <i class="bi bi-eye"></i> View Flat
                                   </a>
                                    <form action="create_recommendation.php" method="POST" class="d-inline">
                                      <input type="hidden" name="tenant_id" value="<?= $match['user_id'] ?>">
                                       <input type="hidden" name="flat_id" value="<?= $match['flat_id'] ?>">
                                       <button type="submit" class="btn btn-warning">
                                       <i class="bi bi-star"></i> Recommend
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">No tenant matches found</div>
        <?php endif; ?>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
