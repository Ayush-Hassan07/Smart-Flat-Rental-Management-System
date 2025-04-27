<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "flat_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";
$name = $email = $role = "";
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // New role field

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "❌ All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $error = "❌ Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if email exists
        $check_stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error = "❌ Email already registered!";
        } else {
            // Insert new user
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $name, $email, $hashed_password,$role);

            if ($insert_stmt->execute()) {
                $success = "✅ User registered successfully!";
                // Clear form fields
                $name = $email = $role = "";
            } else {
                $error = "❌ Registration failed: " . $insert_stmt->error;
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | AURORA Properties</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --gold-color: #ffd700;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            color: var(--dark-color);
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            z-index: 1;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 10px;
            border-radius: 5px;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        h4 {
            color: var(--secondary-color);
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* Floating particles */
        .particle {
            position: absolute;
            background: rgba(67, 97, 238, 0.6);
            border-radius: 50%;
            animation: float infinite ease-in-out;
            z-index: 0;
        }

        .particle:nth-child(odd) {
            background: rgba(76, 201, 240, 0.6);
        }

        .particle:nth-child(3n) {
            background: rgba(255, 215, 0, 0.6);
        }
        .navbar {
            background: linear-gradient(90deg, #007bff, #6610f2);
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(var(--move-x), var(--move-y));
            }
            50% {
                transform: translate(calc(var(--move-x) * -1), calc(var(--move-y) * -1));
            }
            75% {
                transform: translate(calc(var(--move-x) * 0.5), calc(var(--move-y) * 0.5));
            }
        }
    </style>
</head>
<body>
<div id="particles" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; overflow: hidden;"></div>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">AURORA Properties</a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-light me-2">Home</a>
        </div>
    </div>
</nav>
<div class="form-container">

  <h4>Create Account</h4>
    
    <form method="post" class="mt-3">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" id="password" required minlength="6">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" id="confirm_password" required minlength="6">
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" class="form-control" id="role" required style="font-size: 1.2rem; padding: 10px; height: 45px;">
                <option value="tenant">Tenant</option>
                <option value="landlord">Landlord</option>
                <option value="agent">Agent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
        <a class="btn btn-outline-primary w-100 mt-2" href="login.php">Already have an account? Login</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create floating particles
    const particlesContainer = document.getElementById('particles');
    const particleCount = 30;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        const size = Math.random() * 8 + 2;
        const posX = Math.random() * window.innerWidth;
        const posY = Math.random() * window.innerHeight;
        const opacity = Math.random() * 0.6 + 0.3;
        const duration = Math.random() * 8 + 4;
        const delay = Math.random() * 5;
        const moveX = Math.random() * 100 - 50;
        const moveY = Math.random() * 100 - 50;

        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${posX}px`;
        particle.style.top = `${posY}px`;
        particle.style.opacity = opacity;
        particle.style.animation = `float ${duration}s ease-in-out ${delay}s infinite`;
        particle.style.setProperty('--move-x', `${moveX}px`);
        particle.style.setProperty('--move-y', `${moveY}px`);
        
        particlesContainer.appendChild(particle);
    }
});
</script>
</body>
</html>
