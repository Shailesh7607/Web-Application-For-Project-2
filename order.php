<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cartItems = [];
$total = 0;
$total_quantity = 0;

$stmt = $pdo->prepare("SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.image FROM Cart c JOIN Products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
    $total_quantity += $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = sanitize($_POST['payment_method']);
    $pickup_option = sanitize($_POST['pickup_option']);
    $pickup_time = ($pickup_option === 'now') ? date('H:i:s') : sanitize($_POST['pickup_time']);
    $is_now = ($pickup_option === 'now') ? 1 : 0;
    $customer_name = sanitize($_POST['customer_name']);

    // Validate custom pickup time (between 7:00 AM and 10:00 PM)
    if ($pickup_option === 'custom') {
        $time = DateTime::createFromFormat('H:i', $pickup_time);
        if (!$time || $pickup_time < '07:00' || $pickup_time > '22:00') {
            $error = "Please select a pickup time between 7:00 AM and 10:00 PM.";
        }
    }

    // Validate customer name
    if (empty($customer_name)) {
        $error = "Please provide your name.";
    }

    if (!isset($error)) {
        if ($payment_method === 'cash') {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO Orders (user_id, total, status, pickup_time, is_now, customer_name) VALUES (?, ?, 'pending', ?, ?, ?)");
                $stmt->execute([$user_id, $total, $pickup_time, $is_now, $customer_name]);
                $order_id = $pdo->lastInsertId();

                foreach ($cartItems as $item) {
                    $stmt = $pdo->prepare("INSERT INTO Order_Items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                }

                $stmt = $pdo->prepare("INSERT INTO Payment (order_id, amount, payment_method, status) VALUES (?, ?, ?, 'pending')");
                $stmt->execute([$order_id, $total, $payment_method]);

                $stmt = $pdo->prepare("DELETE FROM Cart WHERE user_id = ?");
                $stmt->execute([$user_id]);

                $pdo->commit();
                $success = "Order placed successfully!";
            } catch (PDOException $e) {
                $pdo->rollback();
                $error = "Failed to place order: " . $e->getMessage();
            }
        } elseif ($payment_method === 'credit_card') {
            // Store cart items, total, pickup time, is_now, and customer_name in session for checkout
            $_SESSION['pending_order'] = [
                'cart_items' => $cartItems,
                'total' => $total,
                'pickup_time' => $pickup_time,
                'is_now' => $is_now,
                'customer_name' => $customer_name
            ];
            header("Location: checkout.php");
            exit();
        } else {
            $error = "Invalid payment method.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order - Canberra Momo House</title>
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
                    <li><a href="logout.php">Logout</a></li>
                    <?php if ($_SESSION['userRole'] === 'admin'): ?>
                        <li id="admin-link"><a href="admin/admin_dashboard.php">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a id="shopping-cart" class="shopping-cart">
                            <span>🛒(<?php echo count($cartItems); ?>)</span>
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
                                if ($cartItems) {
                                    foreach ($cartItems as $item) {
                                        $item_total = $item['price'] * $item['quantity'];
                                        echo "
                                        <tr>
                                            <td><img src='{$item['image']}' alt='{$item['name']}'></td>
                                            <td>{$item['name']}</td>
                                            <td>\${$item['price']}</td>
                                            <td>{$item['quantity']}</td>
                                            <td>\${$item_total}</td>
                                            <td><a href='#' class='btn-delete' data-cart-id='{$item['id']}'>×</a></td>
                                        </tr>";
                                    }
                                } else {
                                    echo '<tr class="empty-cart"><td colspan="6">Cart is empty</td></tr>';
                                }
                                ?>
                            </table>
                            <div class="cart-summary">
                                <p><strong>Total Quantity:</strong> <?php echo $total_quantity; ?></p>
                                <p><strong>Total Price:</strong> $<?php echo number_format($total, 2); ?></p>
                            </div>
                            <a href="order.php" class="btn-primary">Confirm Order</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<section class="order">
    <div class="container">
        <h2 class="text-center">Confirm Your Order</h2>
        <div class="heading-border"></div>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <?php if ($cartItems): ?>
            <form method="POST" action="" class="form" id="order-form">
                <h3>Pickup Details</h3>
                <div class="form-group">
                    <label for="customer_name">Name</label>
                    <input type="text" name="customer_name" id="customer_name" required>
                </div>
                <div class="form-group">
                    <label>Pickup Time (7:00 AM - 10:00 PM)</label><br>
                    <label><input type="radio" name="pickup_option" value="now" checked> Now</label>
                    <label><input type="radio" name="pickup_option" value="custom"> Custom Time</label>
                    <input type="time" name="pickup_time" id="pickup_time" min="07:00" max="22:00" disabled>
                </div>
                <h3>Payment Methods</h3>
                <div class="form-group">
                    <select name="payment_method" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="cash">Cash (Pay at Counter)</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" value="Place Order" class="btn-primary">
                </div>
            </form>
        <?php else: ?>
            <p>Your cart is empty. <a href="menu.php">Browse menu</a></p>
        <?php endif; ?>
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
<script>
document.getElementById('order-form').addEventListener('submit', function(event) {
    const pickupOption = document.querySelector('input[name="pickup_option"]:checked').value;
    const pickupTime = document.getElementById('pickup_time').value;
    if (pickupOption === 'custom' && (pickupTime < '07:00' || pickupTime > '22:00')) {
        event.preventDefault();
        alert('Please select a pickup time between 7:00 AM and 10:00 PM.');
    }
});

document.querySelectorAll('input[name="pickup_option"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        const pickupTimeInput = document.getElementById('pickup_time');
        pickupTimeInput.disabled = (this.value === 'now');
        if (this.value === 'custom') {
            pickupTimeInput.required = true;
        } else {
            pickupTimeInput.required = false;
        }
    });
});
</script>
</body>
</html>