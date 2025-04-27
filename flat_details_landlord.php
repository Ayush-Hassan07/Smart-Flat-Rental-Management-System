<?php
include("dbconnect.php"); 

session_start();
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$flat_id = intval($_GET['id']);

$sql = "SELECT * FROM agent_flats WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $flat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Flat not found.";
    exit;
}

$flat = $result->fetch_assoc();
$amenities = array_map('trim', explode(',', $flat['amenities']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($flat['flat_name']); ?> - Flat Details</title>
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
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
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
                <h2 class="text-primary my-3">BDT <?= number_format($flat['price']); ?></h2>

                <!-- Flat Description -->
                <?php if (!empty($flat['description'])): ?>
                        <div class="mb-4">
                            <h4>Description</h4>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($flat['description'])) ?></p>
                        </div>
                    <?php endif; ?>

                <!-- Sale Agreement -->
                <?php if ($user_id && $role === 'landlord'): ?>
                    <div class="text-center mb-4">
                        <a href="landlord_generate_agreement.php?flat_id=<?= $flat_id ?>" 
                        class="btn btn-lg btn-info text-white d-inline-flex align-items-center gap-2 fw-semibold shadow px-4 py-2"
                        style="border-radius: 30px; transition: transform 0.2s ease-in-out;"
                        onmouseover="this.style.transform='scale(1.05)';" 
                        onmouseout="this.style.transform='scale(1)';">
                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                            Generate Sale Agreement
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

                <!-- Contact Section -->
                <div class="row g-2 mt-3">
                    <div class="col-md-6">
                        <?php if($user_id && $role === 'landlord'): 
                            // Get landlord name
                            $landlord_stmt = mysqli_prepare($con, "SELECT name FROM users WHERE id = ?");
                            mysqli_stmt_bind_param($landlord_stmt, 'i', $user_id);
                            mysqli_stmt_execute($landlord_stmt);
                            $landlord_result = mysqli_stmt_get_result($landlord_stmt);
                            $landlord = mysqli_fetch_assoc($landlord_result);
                            $landlord_name = $landlord['name'] ?? 'Landlord';

                            // Format phone number
                            $phone = $flat['contact_phone'];
                            $phone = preg_replace('/[^0-9]/', '', $phone);
                            if (strlen($phone) == 11 && strpos($phone, '01') === 0) {
                                $phone = '880' . substr($phone, 1);
                            } elseif (strlen($phone) == 10 && strpos($phone, '1') === 0) {
                                $phone = '880' . $phone;
                            }

                            // WhatsApp message (buying request)
                            $message = rawurlencode(
                                "Flat Purchase Inquiry: {$flat['flat_name']}\n\n" .
                                "{$landlord_name} (Landlord ID: {$user_id})\n\n" .
                                "Hello,\n\n" .
                                "I am a landlord interested in purchasing the flat you have listed:\n\n" .
                                "Flat Name: {$flat['flat_name']}\n" .
                                "Location: {$flat['location']}\n\n" .
                                "Please let me know the next steps, pricing details, and if we can schedule a call or meeting.\n\n" .
                                "Name: {$landlord_name}\n" .
                                "Phone: [Your Number]"
                            );
                        ?>
                        <a href="https://wa.me/<?= $phone ?>?text=<?= $message ?>" 
                        target="_blank" 
                        class="btn btn-success w-100"
                        onclick="window.open(this.href, '_blank', 'width=600,height=800'); return false;">
                            <i class="fab fa-whatsapp"></i> WhatsApp (Buy Flat Inquiry)
                        </a>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <?php if($user_id && $role === 'landlord'):
                            $subject = rawurlencode("Interested in Buying Flat: {$flat['flat_name']}");
                            $body = rawurlencode(
                                "Dear Agent,\n\n" .
                                "I came across your flat listing: \"{$flat['flat_name']}\" located at {$flat['location']}.\n\n" .
                                "I am a landlord looking to buy a property and would like to inquire about this one in particular.\n\n" .
                                "Could you please share further details about the price, availability, and the process to proceed?\n\n" .
                                "My contact info:\n" .
                                "Name: {$landlord_name}\n" .
                                "Phone: [Your Number]\n" .
                                "Landlord ID: {$user_id}\n\n" .
                                "Looking forward to your response.\n\n" .
                                "Regards,\n" .
                                "{$landlord_name}"
                            );
                        ?>
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= htmlspecialchars($flat['contact_email']); ?>&su=<?= $subject ?>&body=<?= $body ?>" 
                        target="_blank" 
                        class="btn btn-primary w-100">
                            <i class="fas fa-envelope"></i> Email (Buy Flat Inquiry)
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>