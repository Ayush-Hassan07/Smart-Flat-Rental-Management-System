<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['flat_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$flat_id = (int) $_GET['flat_id'];

// Get user name
$user_stmt = mysqli_prepare($con, "SELECT name FROM users WHERE id = ?");
mysqli_stmt_bind_param($user_stmt, 'i', $user_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user = mysqli_fetch_assoc($user_result);
$tenant_name = $user['name'] ?? 'Unknown';

// Get flat details
$flat_stmt = mysqli_prepare($con, "SELECT * FROM flats WHERE id = ?");
mysqli_stmt_bind_param($flat_stmt, 'i', $flat_id);
mysqli_stmt_execute($flat_stmt);
$flat_result = mysqli_stmt_get_result($flat_stmt);
$flat = mysqli_fetch_assoc($flat_result);
if (!$flat) die("Flat not found.");

// If Agreed button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agree'])) {
    $agreement_text = "
Rental Agreement
------------------------
Tenant ID: $user_id
Tenant Name: $tenant_name
Flat: {$flat['flat_name']}
Location: {$flat['location']}
Rent: BDT {$flat['rent']}/month
Bedrooms: {$flat['bedrooms']}
Bathrooms: {$flat['bathrooms']}
Agreement Date: " . date('Y-m-d H:i:s') . "
By proceeding, the tenant agrees to the terms and conditions set by Aurora Properties.";

    // Insert into rental_agreements table
    $stmt = $con->prepare("INSERT INTO rental_agreements (user_id, flat_id, agreement_text) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $user_id, $flat_id, $agreement_text);
    $stmt->execute();
    $agreement_id = $stmt->insert_id;
    $stmt->close();

    // Payment info
    $month = date('n');
    $year = date('Y');
    $payment_date = date('Y-m-d H:i:s');
    $total_rent = $flat['rent'];
    $advance_paid = 0.00;
    $amount_paid = 0.00;
    $remaining_due = $total_rent;

    // Check if payment already exists
    $check_stmt = $con->prepare("SELECT id FROM payments WHERE tenant_id = ? AND flat_id = ? AND month = ? AND year = ?");
    $check_stmt->bind_param("iiii", $user_id, $flat_id, $month, $year);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Duplicate payment found
        header("Location: payments.php?flat_id=$flat_id&already_exists=1");
        exit();
    }
    $check_stmt->close();

    // Insert payment
    $stmt = $con->prepare("INSERT INTO payments 
        (tenant_id, flat_id, month, year, total_rent, amount_paid, remaining_due, payment_date, user_id, agreement_id, amount, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param(
        "iiidddssiid",
        $user_id,
        $flat_id,
        $month,
        $year,
        $total_rent,
        $amount_paid,
        $remaining_due,
        $payment_date,
        $user_id,
        $agreement_id,
        $amount_paid
    );
    $stmt->execute();
    $stmt->close();

    header("Location: payments.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rental Agreement</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1564937494144-59898c6afbd2?q=80&w=1523&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .agreement-box {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 40px;
            background: #fff;
            font-family: 'Georgia', serif;
        }
        .signature {
            position: relative;
            margin-top: 100px;
            text-align: right;
            padding-right: 30px;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-bottom: 1px solid #000;
        }
        .download-btn {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }
        .spinner-active {
            position: relative;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">AURORA Properties</a>
        <div class="ms-auto">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="tenant.php" class="btn btn-light me-2">My Dashboard</a>
                <a href="favourites.php" class="btn btn-light me-2">My Favourites</a>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div id="agreement" class="agreement-box shadow-lg bg-white">
        <h2 class="text-center text-uppercase mb-4">Rental Agreement</h2>

        <p><strong>Tenant ID:</strong> <?= $user_id ?></p>
        <p><strong>Tenant Name:</strong> <?= htmlspecialchars($tenant_name) ?></p>
        <p><strong>Flat Name:</strong> <?= htmlspecialchars($flat['flat_name']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?></p>
        <p><strong>Rent:</strong> BDT <?= $flat['rent'] ?>/month</p>
        <p><strong>Bedrooms:</strong> <?= $flat['bedrooms'] ?> | <strong>Bathrooms:</strong> <?= $flat['bathrooms'] ?></p>
        <p><strong>Agreement Date:</strong> <?= date('Y-m-d H:i:s') ?></p>

        <hr>
        <p>
            This Rental Agreement is entered into between the landlord of Aurora Properties and the tenant named above.
            The tenant agrees to abide by all rules, responsibilities, and rent commitments associated with the mentioned flat.
            This document stands as a formal rental agreement for legal and tenancy purposes.
        </p>

        <div class="signature">
            <div class="signature-line"></div>
            <p>Tenant's Signature</p>
        </div>
    </div>

    <div class="download-btn">
        <button onclick="downloadPDF()" class="btn btn-success px-4" id="downloadBtn">
            <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
            Download Agreement (PDF)
        </button>
    </div>

    <form method="POST" class="text-center mt-4">
        <button type="submit" name="agree" class="btn btn-secondary w-40">Agreed</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
window.downloadPDF = function() {
    const btn = document.getElementById('downloadBtn');
    const spinner = document.getElementById('spinner');
    btn.classList.add('spinner-active');
    spinner.classList.remove('d-none');

    html2canvas(document.querySelector("#agreement"), {
        scale: 2,
        logging: true,
        useCORS: true
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const imgRatio = canvas.width / canvas.height;
        const imgWidth = pageWidth - 20;
        const imgHeight = imgWidth / imgRatio;
        pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
        pdf.save(`Rental_Agreement_${Date.now()}.pdf`);
    }).catch(error => {
        console.error('PDF generation failed:', error);
        alert('Failed to generate PDF. Please try again.');
    }).finally(() => {
        btn.classList.remove('spinner-active');
        spinner.classList.add('d-none');
    });
};
</script>
</body>
</html>
