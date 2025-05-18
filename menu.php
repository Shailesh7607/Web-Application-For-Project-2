<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Canberra Momo House</title>
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

<section class="food-search text-center">
    <div class="overlay"></div>
    <div class="container">
        <h2>Find Your Favorite Dish!</h2>
        <ul class="food-search-highlights">
            <li>🍴 Fresh and authentic Nepalese cuisine</li>
            <li>🌟 Highly rated by food lovers</li>
        </ul>
        <form id="search-form">
            <input id="search-input" type="search" placeholder="Search for food..." required>
            <input type="submit" value="Search" class="btn-primary">
        </form>
        <p id="search-message" class="text-center" style="color: white; margin-top: 10px;"></p>
    </div>
</section>
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>
        <div class="heading-border"></div>
        <?php
        $categories = ['Snack Pack', 'Momo', 'Chowmein', 'Khaja Set', 'Kids Menu', 'Drinks'];
        foreach ($categories as $category) {
            $stmt = $pdo->prepare("SELECT * FROM Products WHERE category = ? AND availability = TRUE");
            $stmt->execute([$category]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($products) {
                echo "<div id='" . strtolower(str_replace(' ', '-', $category)) . "' class='menu-section'>";
                echo "<h3>$category</h3>";
                echo "<div class='grid-3'>";
                foreach ($products as $product) {
                    echo "
                    <div class='food-menu-box'>
                        <form class='add-to-cart-form' data-product-id='{$product['id']}'>
                            <div class='food-menu-img'>
                                <img src='{$product['image']}' alt='{$product['name']}' class='img-responsive img-curve'>
                            </div>
                            <div class='food-menu-desc'>
                                <h4>{$product['name']}</h4>
                                <p class='food-price'>\${$product['price']}</p>
                                <p class='food-details'>{$product['name']}</p>
                                <input type='number' class='quantity-input' value='1' min='1'>
                                <input type='submit' class='btn-primary' value='Add To Cart'>
                            </div>
                        </form>
                    </div>";
                }
                echo "</div></div>";
            }
        }
        ?>
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