<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    die("Access denied");
}

$flat = null;
$additional_images = [];
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Invalid or missing flat ID");
}

$stmt = mysqli_prepare($con, "SELECT * FROM agent_flats WHERE id = ? AND agent_id = ?");
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}


// Load existing flat data
if ($id) {
    $stmt = mysqli_prepare($con, "SELECT * FROM agent_flats WHERE id = ? AND agent_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $flat = mysqli_fetch_assoc($result);
    
    if ($flat) {
        $additional_images = json_decode($flat['additional_images'] ?? '[]', true);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if (!$id || !$flat) die("Invalid request");
    
    // Retrieve form data
    $flat_name = $_POST['flat_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $bedrooms = $_POST['bedrooms'] ?? null;
    $bathrooms = $_POST['bathrooms'] ?? null;
    $square_feet = $_POST['square_feet'] ?? null;
    $amenities = $_POST['amenities'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';

    // Validate required fields
    if (empty($flat_name) || empty($description) || empty($location) || $price <= 0) {
        die("All required fields must be filled properly");
    }

    // Handle main picture
    $room_picture = $flat['room_picture'];
    if (!empty($_FILES['room_picture']['name'])) {
        $room_picture = uniqid() . '_' . basename($_FILES['room_picture']['name']);
        move_uploaded_file($_FILES['room_picture']['tmp_name'], "uploads/$room_picture");
    }

    // Handle additional images
    $existing_additional = json_decode($flat['additional_images'], true) ?? [];
    $new_additional_images = [];
    
    foreach ($_FILES['additional_images']['name'] as $key => $name) {
        if (!empty($name)) {
            $file_name = uniqid() . '_' . basename($name);
            $file_tmp = $_FILES['additional_images']['tmp_name'][$key];
            move_uploaded_file($file_tmp, "uploads/$file_name");
            $new_additional_images[] = $file_name;
        }
    }
    
    $additional_images_json = !empty($new_additional_images) ? 
        json_encode($new_additional_images) : 
        $flat['additional_images'];

    // Update database
    $stmt = mysqli_prepare($con, 
        "UPDATE agent_flats SET
            flat_name = ?,
            description = ?,
            location = ?,
            room_picture = ?,
            price = ?,
            bedrooms = ?,
            bathrooms = ?,
            square_feet = ?,
            amenities = ?,
            contact_email = ?,
            contact_phone = ?,
            additional_images = ?
        WHERE id = ? AND agent_id = ?");
    
    mysqli_stmt_bind_param($stmt, 'ssssdiiissssii', 
        $flat_name,
        $description,
        $location,
        $room_picture,
        $price,
        $bedrooms,
        $bathrooms,
        $square_feet,
        $amenities,
        $contact_email,
        $contact_phone,
        $additional_images_json,
        $id,
        $_SESSION['user_id']
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Error updating flat: " . mysqli_error($con));
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
    <title>Edit Flat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Keep the same styles as add_flat_agent.php */
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .edit-container { max-width: 800px; margin: 30px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .form-header { color: #2c3e50; margin-bottom: 30px; text-align: center; font-weight: 600; }
        .form-section { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .form-section h3 { color: #3498db; margin-bottom: 20px; font-size: 1.2rem; }
        .form-label { font-weight: 500; color: #495057; margin-bottom: 8px; }
        .form-control, .form-select { padding: 12px; border-radius: 8px; border: 1px solid #ced4da; transition: all 0.3s; }
        .form-control:focus { border-color: #3498db; box-shadow: 0 0 0 0.25rem rgba(52,152,219,0.25); }
        .btn-primary { background-color: #3498db; border: none; padding: 12px 25px; font-weight: 500; width: 100%; margin-top: 20px; }
        .btn-primary:hover { background-color: #2980b9; }
        .current-image { max-width: 150px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; padding: 5px; }
        .image-preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; }
        .image-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .file-input-label { display: block; margin-bottom: 8px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2 class="form-header">Edit Flat</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <!-- Basic Information Section -->
            <div class="form-section">
                <h3>Basic Information</h3>
                <div class="mb-3">
                    <label class="form-label">Flat Name</label>
                    <input type="text" name="flat_name" class="form-control" 
                           value="<?= htmlspecialchars($flat['flat_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?= 
                        htmlspecialchars($flat['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" 
                           value="<?= htmlspecialchars($flat['location']) ?>" required>
                </div>
            </div>

            <!-- Images Section -->
            <div class="form-section">
                <h3>Images</h3>
                <div class="mb-3">
                    <label class="file-input-label">Main Picture</label>
                    <?php if (!empty($flat['room_picture'])): ?>
                        <p>Current Image:</p>
                        <img src="uploads/<?= htmlspecialchars($flat['room_picture']) ?>" class="current-image">
                    <?php endif; ?>
                    <input type="file" name="room_picture" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="file-input-label">Additional Images</label>
                    <?php if (!empty($additional_images)): ?>
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
            </div>

            <!-- Property Details Section -->
            <div class="form-section">
                <h3>Property Details</h3>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price (BDT)</label>
                        <input type="number" name="price" class="form-control" 
                               step="0.01" min="0" value="<?= htmlspecialchars($flat['price']) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bedrooms</label>
                        <input type="number" name="bedrooms" class="form-control" 
                               value="<?= htmlspecialchars($flat['bedrooms']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bathrooms</label>
                        <input type="number" name="bathrooms" class="form-control" 
                               value="<?= htmlspecialchars($flat['bathrooms']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Area (sq ft)</label>
                    <input type="number" name="square_feet" class="form-control" 
                           value="<?= htmlspecialchars($flat['square_feet']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Amenities</label>
                    <textarea name="amenities" class="form-control" rows="3"><?= 
                        htmlspecialchars($flat['amenities']) ?></textarea>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <h3>Contact Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" 
                               value="<?= htmlspecialchars($flat['contact_email']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" 
                               value="<?= htmlspecialchars($flat['contact_phone']) ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Flat</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>