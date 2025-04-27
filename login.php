<?php
$con = mysqli_connect("localhost", "root", "", "flat_management");
if (!$con) die("Connection Error");
$user_id=0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            // Start session and redirect
            session_start();
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // <- this line is essential
            
            $role = $user['role']; // assuming role column exists in DB
    
            // Role-based redirection
            if ($role === 'admin') {
                header("Location: admin.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'agent') {
                header("Location: agent.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'landlord') {
                header("Location: landlord_dashboard.php?user_id=$user_id&flat_id=null");
            } else {
                header("Location: tenant.php?user_id=$user_id&flat_id=null");
            }
        }
    }
    
    $error = "Invalid email or password";

    // Retrieve the user_id and flat_id from the URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$flat_id = isset($_GET['flat_id']) ? $_GET['flat_id'] : null;
}
?>

<?php
$con = mysqli_connect("localhost", "root", "", "flat_management");
if (!$con) die("Connection Error");
$user_id = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            $role = $user['role'];

            if ($role === 'admin') {
                header("Location: admin.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'agent') {
                header("Location: agent.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'landlord') {
                header("Location: landlord_dashboard.php?user_id=$user_id&flat_id=null");
            } else {
                header("Location: tenant.php?user_id=$user_id&flat_id=null");
            }
        }
    }
    $error = "Invalid email or password";

    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $flat_id = isset($_GET['flat_id']) ? $_GET['flat_id'] : null;
}
?>

<?php
$con = mysqli_connect("localhost", "root", "", "flat_management");
if (!$con) die("Connection Error");
$user_id = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            $role = $user['role'];

            if ($role === 'admin') {
                header("Location: admin.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'agent') {
                header("Location: agent.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'landlord') {
                header("Location: landlord_dashboard.php?user_id=$user_id&flat_id=null");
            } else {
                header("Location: tenant.php?user_id=$user_id&flat_id=null");
            }
        }
    }
    $error = "Invalid email or password";

    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $flat_id = isset($_GET['flat_id']) ? $_GET['flat_id'] : null;
}
?>

<?php
$con = mysqli_connect("localhost", "root", "", "flat_management");
if (!$con) die("Connection Error");
$user_id = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            $role = $user['role'];

            if ($role === 'admin') {
                header("Location: admin.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'agent') {
                header("Location: agent.php?user_id=$user_id&flat_id=null");
            } elseif ($role === 'landlord') {
                header("Location: landlord_dashboard.php?user_id=$user_id&flat_id=null");
            } else {
                header("Location: tenant.php?user_id=$user_id&flat_id=null");
            }
        }
    }
    $error = "Invalid email or password";

    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $flat_id = isset($_GET['flat_id']) ? $_GET['flat_id'] : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AURORA Properties</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    /* Same CSS from your second code (beautiful styling) */
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3a0ca3;
        --accent-color: #4cc9f0;
        --gold-color: #ffd700;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --glass-color: rgba(255, 255, 255, 0.15);
    }

    body {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                    url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Clear font family */
        color: var(--light-color);
        overflow: hidden;
    }

    .form-container {
        background: var(--glass-color);
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-width: 450px;
        width: 100%;
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
        z-index: 1;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Clear font family */
    }

    .form-container::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        z-index: -1;
        animation: rotate 8s linear infinite; /* Faster rotation */
    }

    .navbar {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            padding: 8px 0;  /* Reduced padding to make the navbar smaller */
    }

    .navbar-brand {
        font-family: 'Playfair Display', serif;
        font-weight: 600;
        letter-spacing: 1.5px;
        font-size: 1.8rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .navbar-brand::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--gold-color);
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.5s ease;
    }

    .navbar-brand:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }

    .btn-primary, .btn-secondary {
        border-radius: 50px;
        padding: 12px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.4s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
    }

    .btn-secondary {
        background: transparent;
        color: var(--light-color);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn-primary:hover, .btn-secondary:hover {
        transform: translateY(-3px);
    }

    .form-control {
        padding: 15px 20px;
        border-radius: 50px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-weight: 500;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Clear font family */
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 0 0.2rem rgba(76, 201, 240, 0.25);
        color: white;
    }

    h4 {
        color: white;
        font-family: 'Playfair Display', serif;
        font-weight: 600;
        margin-bottom: 2rem;
        text-align: center;
        font-size: 2rem;
    }

    h4::after {
        content: '';
        display: block;
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--gold-color), transparent);
        margin: 10px auto 0;
        border-radius: 3px;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: float infinite ease-in-out;
    }

    .particle:nth-child(odd) {
        background: rgba(76, 201, 240, 0.7);
    }

    .particle:nth-child(3n) {
        background: rgba(255, 215, 0, 0.7);
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

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    /* New ripple effect */
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: scale(0);
        animation: ripple 4s linear infinite;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* New twinkling stars */
    .twinkle {
        position: absolute;
        background: white;
        border-radius: 50%;
        animation: twinkle 2s infinite alternate;
    }

    @keyframes twinkle {
        0% {
            opacity: 0.2;
        }
        100% {
            opacity: 1;
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

<div class="form-container animate__animated animate__fadeInDown">
    <h4>Login</h4>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="post" class="mt-3">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
        <a href="register.php" class="btn btn-secondary w-100 mt-2">Register</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 60; // Increased particle count
    
    // Create floating particles
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        // Random properties
        const size = Math.random() * 8 + 2; // size between 2px to 10px
        const posX = Math.random() * window.innerWidth;
        const posY = Math.random() * window.innerHeight;
        const opacity = Math.random() * 0.7 + 0.3;
        const animationDuration = Math.random() * 6 + 4; // 4s to 10s (faster)
        const animationDelay = Math.random() * 5; // 0s to 5s
        const moveX = Math.random() * 100 - 50; // move between -50px and +50px
        const moveY = Math.random() * 100 - 50; // move between -50px and +50px

        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${posX}px`;
        particle.style.top = `${posY}px`;
        particle.style.opacity = opacity;
        particle.style.animation = `float ${animationDuration}s ease-in-out ${animationDelay}s infinite`;
        particle.style.setProperty('--move-x', `${moveX}px`);
        particle.style.setProperty('--move-y', `${moveY}px`);
        
        particlesContainer.appendChild(particle);
    }

    // Create ripple effects
    function createRipple() {
        const ripple = document.createElement('div');
        ripple.classList.add('ripple');
        
        const size = Math.random() * 200 + 100; // 100px to 300px
        const posX = Math.random() * window.innerWidth;
        const posY = Math.random() * window.innerHeight;
        
        ripple.style.width = `${size}px`;
        ripple.style.height = `${size}px`;
        ripple.style.left = `${posX}px`;
        ripple.style.top = `${posY}px`;
        ripple.style.animationDelay = `${Math.random() * 2}s`;
        
        particlesContainer.appendChild(ripple);
        
        // Remove ripple after animation completes
        setTimeout(() => {
            ripple.remove();
        }, 4000);
    }
    
    // Create initial ripples
    for (let i = 0; i < 5; i++) {
        createRipple();
    }
    
    // Create ripples periodically
    setInterval(createRipple, 2000);

    // Create twinkling stars
    for (let i = 0; i < 20; i++) {
        const star = document.createElement('div');
        star.classList.add('twinkle');
        
        const size = Math.random() * 3 + 1; // 1px to 4px
        const posX = Math.random() * window.innerWidth;
        const posY = Math.random() * window.innerHeight;
        const duration = Math.random() * 1 + 0.5; // 0.5s to 1.5s
        const delay = Math.random() * 5; // 0s to 5s
        
        star.style.width = `${size}px`;
        star.style.height = `${size}px`;
        star.style.left = `${posX}px`;
        star.style.top = `${posY}px`;
        star.style.animationDuration = `${duration}s`;
        star.style.animationDelay = `${delay}s`;
        
        particlesContainer.appendChild(star);
    }
});
</script>
</body>
</html>
