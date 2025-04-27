<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $agent_id = $_SESSION['user_id'];
    
    // Retrieve form data
    $flat_name = $_POST['flat_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $bedrooms = $_POST['bedrooms'] ?? null;
    $bathrooms = $_POST['bathrooms'] ?? null;
    $square_feet = $_POST['square_feet'] ?? null;
    $amenities = $_POST['amenities'] ?? '';  // Direct text input
    $contact_email = $_POST['contact_email'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';

    // Validate required fields
    if (empty($flat_name) || empty($description) || empty($location) || $price <= 0) {
        die("All required fields must be filled properly");
    }

    // Handle file uploads
    if (empty($_FILES['room_picture']['name'])) {
        die("Main picture is required");
    }

    $room_picture = uniqid() . '_' . basename($_FILES['room_picture']['name']);
    move_uploaded_file($_FILES['room_picture']['tmp_name'], "uploads/$room_picture");

    $additional_images = [];
    foreach ($_FILES['additional_images']['name'] as $key => $name) {
        if (!empty($name)) {
            $file_name = uniqid() . '_' . basename($name);
            $file_tmp = $_FILES['additional_images']['tmp_name'][$key];
            move_uploaded_file($file_tmp, "uploads/$file_name");
            $additional_images[] = $file_name;
        }
    }
    $additional_images_json = json_encode($additional_images);

    // Database insertion
    $stmt = mysqli_prepare($con, 
        "INSERT INTO agent_flats (
            agent_id, flat_name, description, location, room_picture, 
            price, bedrooms, bathrooms, square_feet, 
            amenities, contact_email, contact_phone, additional_images
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    mysqli_stmt_bind_param($stmt, 'issssdiiissss', 
        $agent_id, 
        $flat_name, 
        $description, 
        $location, 
        $room_picture,
        $price,
        $bedrooms,
        $bathrooms,
        $square_feet,
        $amenities,  // Direct text input
        $contact_email, 
        $contact_phone, 
        $additional_images_json
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        die("Error saving flat: " . mysqli_stmt_error($stmt));
    }

    header("Location: agent.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($flat) ? 'Edit Flat' : 'Add New Flat' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add the same style section from edit_flat.php */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .edit-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .form-header {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }
        .form-section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .form-section h3 {
            color: #3498db;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 12px 25px;
            font-weight: 500;
            width: 100%;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .current-image {
            max-width: 150px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }
        .image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .file-input-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2 class="form-header"><?= isset($flat) ? 'Edit Flat' : 'Add New Flat' ?></h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-section">
                <h3>Basic Information</h3>
                <div class="mb-3">
                    <label class="form-label">Flat Name</label>
                    <input type="text" name="flat_name" class="form-control" 
                           value="<?= isset($flat) ? htmlspecialchars($flat['flat_name']) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?= 
                        isset($flat) ? htmlspecialchars($flat['description']) : '' 
                    ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" 
                           value="<?= isset($flat) ? htmlspecialchars($flat['location']) : '' ?>" required>
                </div>
            </div>

            <div class="form-section">
                <h3>Images</h3>
                <div class="mb-3">
                    <label class="file-input-label">Main Picture</label>
                    <?php if (isset($flat) && !empty($flat['room_picture'])): ?>
                        <p>Current Image:</p>
                        <img src="uploads/<?= htmlspecialchars($flat['room_picture']) ?>" class="current-image">
                    <?php endif; ?>
                    <input type="file" name="room_picture" class="form-control" <?= !isset($flat) ? 'required' : '' ?>>
                </div>
                
                <div class="mb-3">
    <label class="file-input-label">Additional Images (Max 3)</label>
    <?php if (isset($flat) && !empty($additional_images)): ?>
        <div class="image-preview-container">
            <?php foreach ($additional_images as $image): ?>
                <img src="uploads/<?= htmlspecialchars($image) ?>" class="image-preview">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="file-input-group mb-3">
        <label class="form-label">Additional Image 1</label>
        <input type="file" name="additional_images[]" class="form-control">
    </div>
    <div class="file-input-group mb-3">
        <label class="form-label">Additional Image 2</label>
        <input type="file" name="additional_images[]" class="form-control">
    </div>
    <div class="file-input-group">
        <label class="form-label">Additional Image 3</label>
        <input type="file" name="additional_images[]" class="form-control">
    </div>
</div>
            <!-- Property Details Section -->
<div class="form-section">
    <h3>Property Details</h3>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Price (BDT)</label>
            <!-- Corrected name attribute -->
            <!-- Change the price input to ensure numeric value -->
<input type="number" name="price" class="form-control" 
       step="0.01" min="0" 
       value="<?= isset($flat) ? htmlspecialchars($flat['price']) : '' ?>" 
       required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Bedrooms</label>
            <input type="number" name="bedrooms" class="form-control" 
                   value="<?= isset($flat) ? htmlspecialchars($flat['bedrooms']) : '' ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Bathrooms</label>
            <input type="number" name="bathrooms" class="form-control" 
                   value="<?= isset($flat) ? htmlspecialchars($flat['bathrooms']) : '' ?>">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Area (sq ft)</label>
        <input type="number" name="square_feet" class="form-control" 
               value="<?= isset($flat) ? htmlspecialchars($flat['square_feet']) : '' ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Amenities</label>
        <!-- Ensure textarea is properly closed -->
        <textarea name="amenities" class="form-control" rows="3"><?= 
            isset($flat) ? htmlspecialchars($flat['amenities']) : '' 
        ?></textarea>
    </div>
</div>

            <div class="form-section">
                <h3>Contact Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" 
                               value="<?= isset($flat) ? htmlspecialchars($flat['contact_email']) : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" 
                               value="<?= isset($flat) ? htmlspecialchars($flat['contact_phone']) : '' ?>">
                    </div>
                </div>
            </div>

            <?php if (isset($flat)): ?>
                <input type="hidden" name="flat_id" value="<?= $flat_id ?>">
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">
                <?= isset($flat) ? 'Update Flat' : 'Add Flat' ?>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>