<?php
session_start();
include 'dbconnect.php';

$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Retrieve and validate parameters
$flat_id = isset($_GET['flat_id']) ? (int)$_GET['flat_id'] : null;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Fetch flat details with prepared statement
$stmt = mysqli_prepare($con, "SELECT * FROM flats WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $flat_id);
mysqli_stmt_execute($stmt);
$flat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Handle invalid IDs
if (!$flat) {
    die("Invalid flat ID");
}

// Check if flat is favorited
$is_favorite = false;
if ($user_id) {
    $fav_stmt = mysqli_prepare($con, "SELECT id FROM user_favourites WHERE user_id = ? AND flat_id = ?");
    mysqli_stmt_bind_param($fav_stmt, 'ii', $user_id, $flat_id);
    mysqli_stmt_execute($fav_stmt);
    mysqli_stmt_store_result($fav_stmt);
    $is_favorite = (mysqli_stmt_num_rows($fav_stmt) > 0);
}

// Process amenities data
$amenities = [];
if (!empty($flat['amenities'])) {
    // First try to decode as JSON
    $decoded = json_decode($flat['amenities'], true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        $amenities = $decoded;
    } else {
        // If not JSON, split by common delimiters
        $amenities = preg_split('/\r\n|\r|\n|,/', $flat['amenities']);
        $amenities = array_map('trim', $amenities);
        $amenities = array_filter($amenities);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($flat['flat_name']); ?> - Aurora Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .main-image { height: 400px; object-fit: cover; }
        .gallery-image { height: 200px; object-fit: cover; }
        .favourite-btn {
            background: none;
            border: none;
            transition: all 0.3s;
        }
        .favourite-btn:hover {
            transform: scale(1.1);
        }
        .login-required {
            position: relative;
            opacity: 0.7;
        }
        .navbar {
            background: linear-gradient(90deg, #007bff, #6610f2);
        }
        .login-required::after {
            content: "Login to contact";
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            font-size: 0.8rem;
            color: #dc3545;
            text-align: center;
        }
        .amenity-badge {
            margin: 2px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">AURORA Properties</a>
            <div class="ms-auto">
                <?php if($user_id): ?>
                    <a href="logout.php" class="btn btn-outline-light">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <!-- Main Image -->
            <?php
            $main_image = $flat['room_picture'];
            if (!empty($main_image) && !str_starts_with($main_image, 'http') && !str_starts_with($main_image, 'uploads/')) {
                $main_image = 'uploads/' . $main_image;
            }
            ?>
            <img src="<?= htmlspecialchars($main_image ?: 'assets/no-image.jpg'); ?>" class="card-img-top main-image" alt="Main Image">

            <div class="card-body">
                <h1 class="card-title"><?= htmlspecialchars($flat['flat_name']); ?></h1>
                <h5 class="text-muted"><?= htmlspecialchars($flat['location']); ?></h5>
                <h2 class="text-primary my-3">BDT <?= number_format($flat['rent']); ?>/month</h2>

                <!-- Flat Description -->
                <?php if (!empty($flat['description'])): ?>
                        <div class="mb-4">
                            <h4>Description</h4>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($flat['description'])) ?></p>
                        </div>
                    <?php endif; ?>

                <!-- Favorite Button -->
                <?php if($user_id && $role === 'tenant'): ?>
                    <div class="text-center mb-4">
                        <button class="btn btn-lg favourite-btn" 
                                data-flat-id="<?= $flat_id ?>"
                                title="<?= $is_favorite ? 'Remove from favorites' : 'Add to favorites' ?>">
                            <i class="bi bi-heart<?= $is_favorite ? '-fill' : '' ?>" 
                            style="font-size: 2rem; color: <?= $is_favorite ? '#dc3545' : '#6c757d' ?>;"></i>
                            <?= $is_favorite ? ' Remove Favorite' : ' Add to Favorites' ?>
                        </button>
                    </div>
                <?php endif; ?>


                <!-- Rental Agreement -->
                <?php if ($user_id && $role === 'tenant'): ?>
                    <div class="text-center mb-4">
                        <a href="generate_agreement.php?flat_id=<?= $flat_id ?>" 
                        class="btn btn-lg btn-info text-white d-inline-flex align-items-center gap-2 fw-semibold shadow px-4 py-2"
                        style="border-radius: 30px; transition: transform 0.2s ease-in-out;"
                        onmouseover="this.style.transform='scale(1.05)';" 
                        onmouseout="this.style.transform='scale(1)';">
                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                            Generate Rental Agreement
                        </a>
                    </div>
                <?php endif; ?>


                <!-- Details Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>Property Details</h4>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Bedrooms:</strong> <?php echo $flat['bedrooms']; ?></li>
                            <li class="list-group-item"><strong>Bathrooms:</strong> <?php echo $flat['bathrooms']; ?></li>
                            <li class="list-group-item"><strong>Size:</strong> <?php echo number_format($flat['square_feet']); ?> sq.ft</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Amenities</h4>
                        <div class="d-flex flex-wrap">
                            <?php if (!empty($amenities)): ?>
                                <?php foreach ($amenities as $amenity): ?>
                                    <?php if (!empty(trim($amenity))): ?>
                                        <span class="badge bg-primary amenity-badge"><?= htmlspecialchars(trim($amenity)) ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No amenities listed</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Image Gallery -->
                <h4>Gallery</h4>
                <div class="row g-3">
                    <?php 
                    $additional_images = json_decode($flat['additional_images'], true);
                    if (!empty($additional_images)) {
                        foreach ($additional_images as $img) {
                            if (!str_starts_with($img, 'http') && !str_starts_with($img, 'uploads/')) {
                                $img = 'uploads/' . $img;
                            }
                            echo '<div class="col-md-4"><img src="' . htmlspecialchars($img) . '" class="img-fluid gallery-image rounded"></div>';
                        }
                    } else {
                        echo '<p class="text-muted">No additional images</p>';
                    }
                    ?>
                </div>

                <!-- Reviews Section -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title">Tenant Reviews</h4>
                        
                        <?php
                        // Fetch reviews
                        $review_stmt = mysqli_prepare($con, 
                            "SELECT users.name, reviews.rating, reviews.comment, reviews.created_at 
                            FROM reviews 
                            JOIN users ON reviews.user_id = users.id 
                            WHERE flat_id = ? 
                            ORDER BY created_at DESC");
                        mysqli_stmt_bind_param($review_stmt, 'i', $flat_id);
                        mysqli_stmt_execute($review_stmt);
                        $reviews = mysqli_stmt_get_result($review_stmt);
        
                        if(mysqli_num_rows($reviews) > 0): ?>
                            <div class="row row-cols-1 g-3">
                                <?php while($review = mysqli_fetch_assoc($reviews)): ?>
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h5><?= htmlspecialchars($review['name']) ?></h5>
                                                    <div class="text-warning">
                                                        <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                                                    </div>
                                                </div>
                                                <p class="text-muted"><?= date('M j, Y', strtotime($review['created_at'])) ?></p>
                                                <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No reviews yet. Be the first to review!</p>
                        <?php endif; ?>

                        <!-- Review Form -->
                        <?php if($user_id && $role === 'tenant'): ?>
                            <div class="mt-4 border-top pt-4">
                                <h5>Write a Review</h5>
                                <form action="submit_review.php" method="POST">
                                    <input type="hidden" name="flat_id" value="<?= $flat_id ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="rating-stars">
                                            <?php for($i = 5; $i >= 1; $i--): ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" 
                                                           id="rating<?= $i ?>" value="<?= $i ?>" required>
                                                    <label class="form-check-label text-warning" for="rating<?= $i ?>">
                                                        <?= str_repeat('★', $i) . str_repeat('☆', 5 - $i) ?>
                                                    </label>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Comment</label>
                                        <textarea class="form-control" name="comment" rows="3" 
                                                  maxlength="500" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                </form>
                            </div>
                            <?php else: ?>
                                <div class="alert alert-info mt-4">
                                    <a href="login.php" class="alert-link">Login</a> as a tenant to leave a review.
                                </div>
                            <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="row g-2 mt-3">
                    <div class="col-md-6">
                        <?php if($user_id && $role === 'tenant'): 
                            // Get tenant name
                            $tenant_stmt = mysqli_prepare($con, "SELECT name FROM users WHERE id = ?");
                            mysqli_stmt_bind_param($tenant_stmt, 'i', $user_id);
                            mysqli_stmt_execute($tenant_stmt);
                            $tenant_result = mysqli_stmt_get_result($tenant_stmt);
                            $tenant = mysqli_fetch_assoc($tenant_result);
                            $tenant_name = $tenant['name'] ?? 'Tenant';
                            
                            // Format phone number properly for WhatsApp
                            $phone = $flat['contact_phone'];
                            // Remove all non-numeric characters
                            $phone = preg_replace('/[^0-9]/', '', $phone);
                            // Ensure it starts with country code (Bangladesh = 880)
                            if (strlen($phone) == 11 && strpos($phone, '01') === 0) {
                                $phone = '880' . substr($phone, 1);
                            } elseif (strlen($phone) == 10 && strpos($phone, '1') === 0) {
                                $phone = '880' . $phone;
                            }
                            
                            // Create message template
                            $message = rawurlencode(
                                "Inquiry / Visit Request for {$flat['flat_name']}\n\n" .
                                "{$tenant_name} ({$user_id})\n\n" .
                                "**Inquiry / Visit Request for {$flat['flat_name']}**\n\n" .
                                "Hi,\n\n" .
                                "I'm interested in your flat. Please let me know if you prefer to:\n\n" .
                                "1. Just answer questions about the flat.\n" .
                                "2. Schedule a visit (My preferred timing: [Date/Time]).\n\n" .
                                "Name: {$tenant_name}\n" .
                                "Phone: [Your Number]"
                            );
                            ?>
                            <a href="https://wa.me/<?= $phone ?>?text=<?= $message ?>" 
                            target="_blank" 
                            class="btn btn-success w-100"
                            onclick="window.open(this.href, '_blank', 'width=600,height=800'); return false;">
                                <i class="fab fa-whatsapp"></i> WhatsApp (Inquire or Schedule)
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <?php if($user_id && $role === 'tenant'): 
                            $subject = rawurlencode("Inquiry about {$flat['flat_name']}");
                            $body = rawurlencode(
                                "Dear Landlord,\n\n" .
                                "I'm interested in your flat \"{$flat['flat_name']}\" at {$flat['location']}.\n\n" .
                                "Please let me know:\n\n" .
                                "1. If you could answer some questions I have about the flat.\n" .
                                "2. Or if I could schedule a visit (my preferred timing would be [Date/Time]).\n\n" .
                                "My details:\n" .
                                "Name: {$tenant_name}\n" .
                                "Phone: [Your Number]\n" .
                                "Tenant ID: {$user_id}\n\n" .
                                "Looking forward to your response.\n\n" .
                                "Best regards,\n" .
                                "{$tenant_name}"
                            );
                            ?>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= htmlspecialchars($flat['contact_email']); ?>&su=<?= $subject ?>&body=<?= $body ?>" 
                            target="_blank" 
                            class="btn btn-primary w-100">
                                <i class="fas fa-envelope"></i> Email (Inquire or Schedule)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Favorite toggle functionality
    document.querySelector('.favourite-btn')?.addEventListener('click', function() {
        const flatId = this.dataset.flatId;
        const icon = this.querySelector('i');
        const isFavorite = icon.classList.contains('bi-heart-fill');
        const action = isFavorite ? 'remove' : 'add';
        
        if(!confirm(`Are you sure you want to ${action} this favorite?`)) return;
        
        fetch('toggle_favourite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `flat_id=${flatId}&action=${action}&user_id=<?= $user_id ?>`
        })
        .then(response => {
            if(!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if(data.success) {
                icon.classList.toggle('bi-heart');
                icon.classList.toggle('bi-heart-fill');
                icon.style.color = isFavorite ? '#6c757d' : '#dc3545';
                this.innerHTML = isFavorite 
                    ? '<i class="bi bi-heart"></i> Add to Favorites' 
                    : '<i class="bi bi-heart-fill"></i> Remove Favorite';
                this.title = isFavorite ? 'Add to favorites' : 'Remove from favorites';
            } else {
                throw new Error(data.message || 'Operation failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
        });
    });
    </script>
</body>
</html>
