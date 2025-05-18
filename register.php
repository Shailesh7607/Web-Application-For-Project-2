<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $address = sanitize($_POST['address']);

    if ($password === $cpassword) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Email already exists. Please use a different email.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO Users (name, email, phone, password, address, user_type) VALUES (?, ?, ?, ?, ?, 'customer')");
                $stmt->execute([$name, $email, $phone, $hashed_password, $address]);
                $success = "Registration successful! <a href='login.php'>Click here to login</a>.";
            }
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Canberra Momo House</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="css/hover-min.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <script>
    function validateForm() {
        const password = document.querySelector('input[name="password"]').value;
        const cpassword = document.querySelector('input[name="cpassword"]').value;
        if (password !== cpassword) {
            alert("Passwords do not match!");
            return false;
        }
        if (password.length < 6) {
            alert("Password must be at least 6 characters long!");
            return false;
        }
        return true;
    }
    </script>
</head>
<body>
<header class="navbar">
    <nav id="site-top-nav" class="navbar-menu navbar-fixed-top">
        <div class="container">
            <div class="logo">
                <a href="index.php" title="Logo">
                    <img src="img/logo.jpg" alt="Logo" class="img-responsive">
                </a>
            </div>
            <div class="menu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Categories</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<section class="form-container">
    <div class="container">
        <h2 class="text-center">Register</h2>
        <div class="heading-border"></div>
        <form method="POST" action="" onsubmit="return validateForm()" class="form">
            <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
            <div class="form-group">
                <input type="text" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
                <input type="text" name="phone" placeholder="Your Phone" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Your Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="cpassword" placeholder="Confirm Password" required>
            </div>
            <div class="form-group">
                <textarea name="address" placeholder="Your Address" required></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="Register" class="btn-primary">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</section>
<section class="footer">
    <div class="container">
        <div class="grid-3">
            <div class="footer-item text-center">
                <h3>About Us</h3>
                <p>Savor the taste of excellence with every bite. We bring you the finest culinary delights, crafted with passion and delivered with care.</p>
            </div>
            <div class="footer-item text-center">
                <h3>Site Map</h3>
                <div class="site-links">
                    <a href="index.php">Home</a>
                    <a href="menu.php">Categories</a>
                    <a href="contact.php">Contact</a>
                    <a href="login.php">Login</a>
                </div>
            </div>
            <div class="footer-item text-center">
                <h3>Contact Us</h3>
                <div class="social-links">
                    <ul>
                        <li><a href="#"><img src="https://img.icons8.com/color/48/null/facebook-new.png" alt="Facebook"/></a></li>
                        <li><a href="#"><img src="https://img.icons8.com/fluency/48/null/instagram-new.png" alt="Instagram"/></a></li>
                    </ul>
                </div>
                <div class="contact-details">
                    <p>Contact: <span>0462413853</span></p>
                    <p>Email: <a href="mailto:info@canberramomohouse.com">info@canberramomohouse.com</a></p>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p>All rights reserved © 2025 <a href="#">Group 1</a></p>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script src="js/custom.js"></script>
</body>
</html>