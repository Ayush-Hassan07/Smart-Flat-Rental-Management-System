<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user details
$user_query = mysqli_prepare($con, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($user_query, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($user_query);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($user_query));

// Add this at the top of tenant.php after getting user details
$user_prefs = [];
$pref_query = mysqli_prepare($con, "SELECT * FROM tenant_preferences WHERE user_id = ?");
mysqli_stmt_bind_param($pref_query, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($pref_query);
$user_prefs = mysqli_fetch_assoc(mysqli_stmt_get_result($pref_query)) ?? [];

// Get recommendations
$recommended_flats = [];
$rec_stmt = mysqli_prepare($con,
    "SELECT f.*, u.name AS agent_name 
     FROM recommendations r
     JOIN flats f ON r.flat_id = f.id
     JOIN users u ON r.agent_id = u.id
     WHERE r.tenant_id = ?");
mysqli_stmt_bind_param($rec_stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($rec_stmt);
$recommended_flats = mysqli_stmt_get_result($rec_stmt)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
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
                <span class="navbar-text text-white me-3">
                    Welcome, <?= htmlspecialchars($user['name']) ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-center mb-4">Tenant Dashboard</h2>
     
        <div class="row">
    <!-- My Profile Card -->
    <div class="col-md-4 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-person-circle text-primary" style="font-size: 2rem;"></i>
                <h5 class="card-title mt-3">My Profile</h5>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>
    </div>

    <!-- Favorite Flats Card -->
    <div class="col-md-4 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-heart-fill text-danger" style="font-size: 2rem;"></i>
                <h5 class="card-title mt-3">Favorite Flats</h5>
                <p class="card-text">View your saved favorite properties</p>
                <a href="favourites.php?user_id=<?= $_SESSION['user_id'] ?>" class="btn btn-primary mt-auto">View Favorites</a>
            </div>
        </div>
    </div>

    <!-- Payment History Card -->
    <div class="col-md-4 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <i class="bi bi-credit-card" style="font-size: 2rem;"></i>
                <h5 class="card-title mt-3">Payment History</h5>
                <p class="card-text">View your payment records</p>
                <a href="payments.php" class="btn btn-primary mt-auto">View Payments</a>
            </div>
        </div>
    </div>
    
    <!-- My Reviews Card -->
<div class="col-md-4 mb-4">
    <div class="card dashboard-card">
        <div class="card-body text-center">
            <i class="bi bi-chat-dots text-info" style="font-size: 2rem;"></i>
            <h5 class="card-title mt-3">My Reviews</h5>
            <p class="card-text">View your property reviews</p>
            <a href="my_reviews.php" class="btn btn-primary mt-auto">View Reviews</a>
        </div>
    </div>
</div>

<!-- My Preferences Card -->
<div class="col-md-4 mb-4">
    <div class="card dashboard-card">
        <div class="card-body text-center">
            <i class="bi bi-sliders" style="font-size: 2rem;"></i>
            <h5 class="card-title mt-3">My Preferences</h5>
            <p class="card-text">Set your property preferences</p>
            <button class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#preferencesModal">
                Manage Preferences
            </button>
        </div>
    </div>
</div>

<!-- Preferences Modal -->
<div class="modal fade" id="preferencesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Property Preferences</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="save_preferences.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Preferred Location</label>
                        <input type="text" name="location" class="form-control" 
                               value="<?= $user_prefs['preferred_location'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label>Maximum Rent (BDT)</label>
                        <input type="number" name="max_rent" class="form-control"
                               value="<?= $user_prefs['max_rent'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label>Minimum Bedrooms</label>
                        <select name="min_bedrooms" class="form-select">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?= $i ?>" <?= ($user_prefs['min_bedrooms'] ?? 1) == $i ? 'selected' : '' ?>>
                                    <?= $i ?>+
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Recommendations Card -->
<div class="col-md-4 mb-4">
    <div class="card dashboard-card">
        <div class="card-body text-center">
            <i class="bi bi-stars text-warning" style="font-size: 2rem;"></i>
            <h5 class="card-title mt-3">Agent Recommendations</h5>
            <p class="card-text">View recommended properties</p>
            <button class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#recommendationsModal">
                View Recommendations (<?= count($recommended_flats) ?>)
            </button>
        </div>
    </div>
</div>

<!-- Recommendations Modal -->
<div class="modal fade" id="recommendationsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agent Recommendations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if(!empty($recommended_flats)): ?>
                    <div class="row row-cols-1 g-3">
                        <?php foreach($recommended_flats as $flat): ?>
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <h5><?= htmlspecialchars($flat['flat_name']) ?></h5>
                                        <p>Recommended by: <?= htmlspecialchars($flat['agent_name']) ?></p>
                                        <p>Location: <?= htmlspecialchars($flat['location']) ?></p>
                                        <p>Rent: BDT <?= number_format($flat['rent']) ?></p>
                                        <a href="flat_details.php?flat_id=<?= $flat['id'] ?>" 
                                           class="btn btn-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No recommendations yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
