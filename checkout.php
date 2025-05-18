<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pending_order = $_SESSION['pending_order'];
$cart_items = $pending_order['cart_items'];
$total = $pending_order['total']; // Total in AUD
$pickup_time = $pending_order['pickup_time'];
$is_now = $pending_order['is_now'];
$customer_name = $pending_order['customer_name'];

// Format pickup time for display
$pickup_time_display = $is_now ? "Now" : DateTime::createFromFormat('H:i', $pickup_time)->format('g:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Canberra Momo House</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="css/hover-min.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <!-- PayPal JavaScript SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=AdpjjArn3L9FsTQy5UzUGy7-GoxBDh2aij7-87WvBydMxVhkoPt0fNojhVpwoBX0xxb-kcxrg_apPLK4&currency=AUD"></script>
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
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<section class="checkout">
    <div class="container">
        <h2 class="text-center">Secure Checkout</h2>
        <div class="heading-border"></div>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <div class="checkout-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                <table class="cart-table">
                    <tr>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td><strong>$<?php echo number_format($pending_order['total'], 2); ?></strong></td>
                    </tr>
                </table>
                <p><strong>Pickup Time:</strong> <?php echo $is_now ? "Now" : $pickup_time_display; ?></p>
            </div>
            <div class="payment-form">
                <h3>Pay with PayPal</h3>
                <div id="paypal-button-container"></div>
                <div id="paypal-errors" role="alert"></div>
            </div>
        </div>
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
<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?php echo number_format($pending_order['total'], 2); ?>',
                    currency_code: 'AUD'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Send PayPal order ID to server
            fetch('complete_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ paypal_order_id: data.orderID })
            }).then(response => response.json()).then(result => {
                if (result.success) {
                    window.location.href = 'order_success.php';
                } else {
                    document.getElementById('paypal-errors').textContent = result.error;
                }
            }).catch(error => {
                document.getElementById('paypal-errors').textContent = 'An error occurred. Please try again.';
            });
        });
    },
    onError: function(err) {
        document.getElementById('paypal-errors').textContent = 'Payment failed. Please try again.';
    }
}).render('#paypal-button-container');
</script>
</body>
</html>