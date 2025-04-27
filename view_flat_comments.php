<?php
// CHANGE THIS to your real DB name from phpMyAdmin
$con = mysqli_connect("localhost", "root", "", "flat_management");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check for flat_id
if (!isset($_GET['flat_id'])) {
    die("Flat ID not provided.");
}
$flat_id = intval($_GET['flat_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flat Comments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color: #f2f2f2;">
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h3 class="mb-4">Comments for Flat ID: <?php echo $flat_id; ?></h3>

            <?php
            // Updated query to work with the `users` table for tenants
            $query = "
                SELECT r.rating, r.comment, u.name AS tenant_name
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.flat_id = ? AND u.role = 'tenant'
            ";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, 'i', $flat_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>Tenant</th><th>Rating</th><th>Comment</th></tr></thead><tbody>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['tenant_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['rating']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['comment']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>No comments available for this flat.</p>';
            }
            ?>
            <a href="landlord_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
