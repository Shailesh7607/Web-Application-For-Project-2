<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);

    try {
        $stmt = $pdo->prepare("INSERT INTO Messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        $success = "Message sent successfully!";
    } catch (PDOException $e) {
        $error = "Failed to send message: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Canberra Momo House</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="css/hover-min.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php">Logout</a></li>
                        <?php if ($_SESSION['userRole'] === 'admin'): ?>
                            <li id="admin-link"><a href="admin/admin_dashboard.php">Admin Dashboard</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a id="shopping-cart" class="shopping-cart">
                            <span>🛒(<?php
                                $cartCount = 0;
                                if (isset($_SESSION['user_id'])) {
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Cart WHERE user_id = ?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $cartCount = $stmt->fetchColumn();
                                }
                                echo $cartCount;
                            ?>)</span>
                        </a>
                        <div id="cart-content" class="cart-content">
                            <h3 class="text-center">Shopping Cart</h3>
                            <table class="cart-table" border="0">
                                <tr>
                                    <th>Food</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                                <?php
                                if (isset($_SESSION['user_id'])) {
                                    $stmt = $pdo->prepare("SELECT c.id, p.name, p.price, p.image, c.quantity FROM Cart c JOIN Products p ON c.product_id = p.id WHERE c.user_id = ?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if ($cartItems) {
                                        foreach ($cartItems as $item) {
                                            $total = $item['price'] * $item['quantity'];
                                            echo "
                                            <tr>
                                                <td><img src='{$item['image']}' alt='{$item['name']}'></td>
                                                <td>{$item['name']}</td>
                                                <td>\${$item['price']}</td>
                                                <td>{$item['quantity']}</td>
                                                <td>\${$total}</td>
                                                <td><a href='#' class='btn-delete' data-cart-id='{$item['id']}'>×</a></td>
                                            </tr>";
                                        }
                                    } else {
                                        echo '<tr class="empty-cart"><td colspan="6">Cart is empty</td></tr>';
                                    }
                                } else {
                                    echo '<tr class="empty-cart"><td colspan="6">Cart is empty</td></tr>';
                                }
                                ?>
                            </table>
                            <a href="order.php" class="btn-primary">Confirm Order</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<section class="contact">
    <div class="container">
        <h2 class="text-center">Contact Us</h2>
        <div class="heading-border"></div>
        <form method="POST" action="" class="form">
            <h3>Send us a message</h3>
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
                <input type="text" name="subject" placeholder="Subject" required>
            </div>
            <div class="form-group">
                <textarea name="message" placeholder="Your Message" required></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="Send Message" class="btn-primary">
            </div>
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