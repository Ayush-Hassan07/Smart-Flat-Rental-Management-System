<?php
session_start();
include 'dbconnect.php';

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied");
}

$user_id = $_SESSION['user_id'];
$landlord_id = $user_id;

// Handle status update (if form is submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flat_id'], $_POST['status'])) {
    $flat_id = intval($_POST['flat_id']);
    $status = $_POST['status'] === 'rented' ? 'rented' : 'available';

    $stmt = $con->prepare("UPDATE flats SET status = ? WHERE id = ? AND landlord_id = ?");
    $stmt->bind_param("sii", $status, $flat_id, $landlord_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch flats owned by the landlord (excluding paid)
$flat_query = "SELECT * FROM flats WHERE landlord_id = ? AND status != 'paid'";
$flat_stmt = $con->prepare($flat_query);
$flat_stmt->bind_param("i", $landlord_id);
$flat_stmt->execute();
$flat_result = $flat_stmt->get_result();

// Fetch Rent Info with Flat Name
$rent_query = "
    SELECT 
        p.flat_id,
        f.flat_name,
        p.tenant_id, 
        u.name AS tenant_name, 
        p.total_rent, 
        p.remaining_due, 
        p.status 
    FROM payments p
    JOIN users u ON p.tenant_id = u.id
    JOIN flats f ON p.flat_id = f.id
    WHERE f.landlord_id = ?
";
$rent_stmt = $con->prepare($rent_query);
$rent_stmt->bind_param("i", $landlord_id);
$rent_stmt->execute();
$rent_result = $rent_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Landlord Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .table {
            background-color: white;
        }
        .table-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .favourite-btn:hover {
            transform: scale(1.1);
        }
        .stars {
            color: gold;
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

    <!-- Flats Table -->
    <div class="container mt-4 table-container">
        <h2>My Flats</h2>
        <a href="add_flat_landlord.php" class="btn btn-success mb-3">Add New Flat</a>

        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Flat Name</th>
                    <th>Location</th>
                    <th>Rent (BDT)</th>
                    <th>Bedrooms</th>
                    <th>Bathrooms</th>
                    <th>Average Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($flat_result) > 0): ?>
                    <?php while ($flat = mysqli_fetch_assoc($flat_result)) : ?>
                        <?php
                            $ratingQuery = "SELECT IFNULL(AVG(r.rating), 0) AS avg_rating FROM reviews r WHERE r.flat_id = ?";
                            $ratingStmt = $con->prepare($ratingQuery);
                            $ratingStmt->bind_param("i", $flat['id']);
                            $ratingStmt->execute();
                            $ratingResult = $ratingStmt->get_result();
                            $ratingRow = $ratingResult->fetch_assoc();
                            $avg_rating = number_format($ratingRow['avg_rating'], 2);

                            $full_stars = floor($avg_rating);
                            $half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
                            $empty_stars = 5 - ($full_stars + $half_star);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($flat['flat_name']) ?></td>
                            <td><?= htmlspecialchars($flat['location']) ?></td>
                            <td><?= htmlspecialchars($flat['rent']) ?></td>
                            <td><?= $flat['bedrooms'] ?></td>
                            <td><?= $flat['bathrooms'] ?></td>
                            <td>
                                <span class="stars">
                                    <?= str_repeat("★", $full_stars) ?>
                                    <?= $half_star ? "⯨" : "" ?>
                                    <?= str_repeat("☆", $empty_stars) ?>
                                </span>
                                <br>
                                <?= $avg_rating ?> / 5
                            </td>
                            <td style="white-space: nowrap;">
                                <a href="edit_flat.php?id=<?= $flat['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="view_flat_comments.php?flat_id=<?= $flat['id'] ?>" class="btn btn-sm btn-info">View Comments</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No flats found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Rent Table -->
    <div class="container mt-5 table-container">
        <h2 class="text-center mb-4">💰 Rent Overview</h2>
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered shadow-sm">
                <thead class="thead-light text-center">
                    <tr>
                        <th>🏢 Flat Name</th>
                        <th>🏢 Flat ID</th>
                        <th>👤 Tenant ID</th>
                        <th>🧾 Tenant Name</th>
                        <th>💵 Total Rent (BDT)</th>
                        <th>💳 Remaining Due (BDT)</th>
                        <th>📌 Status</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if (mysqli_num_rows($rent_result) > 0): ?>
                        <?php while ($rent = mysqli_fetch_assoc($rent_result)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($rent['flat_name']) ?></td>
                                <td><?= htmlspecialchars($rent['flat_id']) ?></td>
                                <td><?= htmlspecialchars($rent['tenant_id']) ?></td>
                                <td><?= htmlspecialchars($rent['tenant_name']) ?></td>
                                <td><strong><?= number_format($rent['total_rent'], 2) ?></strong></td>
                                <td class="<?= $rent['remaining_due'] > 0 ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' ?>">
                                    <?= number_format($rent['remaining_due'], 2) ?>
                                </td>
                                <td>
                                    <?php if (strtolower($rent['status']) === 'paid'): ?>
                                        <span class="badge badge-success">Paid</span>
                                    <?php elseif (strtolower($rent['status']) === 'partial'): ?>
                                        <span class="badge badge-warning">Partial</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Due</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No rent records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <!-- Payment History Card -->
    <div class="container mt-5 table-container">
        <h2 class="text-center mb-4">📄 Payment History</h2>
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <p class="card-text fs-5">View your complete payment records, including past transactions, due payments, and completed payments.</p>
                <a href="payments_landlord.php" class="btn btn-primary mt-3 px-4 py-2">View Payments</a>
            </div>
        </div>
    </div>
</body>
</html>