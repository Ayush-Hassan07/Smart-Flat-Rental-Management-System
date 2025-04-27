<?php
session_start();
include 'dbconnect.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    $stmt = mysqli_prepare($con, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
}

// Handle flat deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_flat'])) {
    $flat_id = (int)$_POST['flat_id'];

    // Delete from all dependent tables first
    $tables = ['payments', 'user_favourites', 'reviews', 'rental_agreements'];
    foreach ($tables as $table) {
        $stmt = mysqli_prepare($con, "DELETE FROM `$table` WHERE flat_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $flat_id);
        mysqli_stmt_execute($stmt);
    }

    // Finally, delete from flats
    $stmt = mysqli_prepare($con, "DELETE FROM flats WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $flat_id);
    mysqli_stmt_execute($stmt);
}

// Fetch all users grouped by role
$tenants = mysqli_query($con, "SELECT * FROM users WHERE role = 'tenant'");
$landlords = mysqli_query($con, "SELECT * FROM users WHERE role = 'landlord'");
$agents = mysqli_query($con, "SELECT * FROM users WHERE role = 'agent'");

// Fetch all flats with landlord information
$flats = mysqli_query($con,
    "SELECT f.*, u.name AS landlord_name 
     FROM flats f
     JOIN users u ON f.landlord_id = u.id
     ORDER BY f.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .dashboard-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .user-table th {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(90deg, #007bff, #6610f2);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">AURORA Admin</a>
        <div class="ms-auto">
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <h2 class="mb-4">User Management</h2>

    <!-- Tenants Table -->
    <div class="dashboard-section">
        <h4>Tenants</h4>
        <div class="table-responsive">
            <table class="table user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($tenant = mysqli_fetch_assoc($tenants)): ?>
                    <tr>
                        <td><?= $tenant['id'] ?></td>
                        <td><?= htmlspecialchars($tenant['name']) ?></td>
                        <td><?= htmlspecialchars($tenant['email']) ?></td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="user_id" value="<?= $tenant['id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Landlords Table -->
    <div class="dashboard-section">
        <h4>Landlords</h4>
        <div class="table-responsive">
            <table class="table user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($landlord = mysqli_fetch_assoc($landlords)): ?>
                    <tr>
                        <td><?= $landlord['id'] ?></td>
                        <td><?= htmlspecialchars($landlord['name']) ?></td>
                        <td><?= htmlspecialchars($landlord['email']) ?></td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="user_id" value="<?= $landlord['id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agents Table -->
    <div class="dashboard-section">
        <h4>Agents</h4>
        <div class="table-responsive">
            <table class="table user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($agent = mysqli_fetch_assoc($agents)): ?>
                    <tr>
                        <td><?= $agent['id'] ?></td>
                        <td><?= htmlspecialchars($agent['name']) ?></td>
                        <td><?= htmlspecialchars($agent['email']) ?></td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="user_id" value="<?= $agent['id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Flats Table -->
    <h2 class="mb-4 mt-5">Property Management</h2>
    <div class="dashboard-section">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Flat ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Rent</th>
                        <th>Landlord</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($flat = mysqli_fetch_assoc($flats)): ?>
                    <tr>
                        <td><?= $flat['id'] ?></td>
                        <td><?= htmlspecialchars($flat['flat_name']) ?></td>
                        <td><?= htmlspecialchars($flat['location']) ?></td>
                        <td>BDT <?= number_format($flat['rent']) ?></td>
                        <td><?= htmlspecialchars($flat['landlord_name']) ?></td>
                        <td>
                            <a href="flat_details.php?flat_id=<?= $flat['id'] ?>" class="btn btn-primary btn-sm">View</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this flat? This will remove all associated records.');">
                                <input type="hidden" name="flat_id" value="<?= $flat['id'] ?>">
                                <button type="submit" name="delete_flat" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
