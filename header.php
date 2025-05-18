<?php
include '../config.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['userRole'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Canberra Momo House</title>
    <link rel="stylesheet" href="/project-22/css/admin style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/project-22/css/font-awesome/css/font-awesome.css">
</head>
<body>
<header class="header">
    <div class="flex">
        <a href="admin_dashboard.php" class="logo"></a>
        <nav class="navbar">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="orders.php">Orders</a>
            <a href="add_product.php">Add Product</a>
            <a href="update_product.php">Update Product</a>
            <a href="admins.php">Admins</a>
            <a href="messages.php">Messages</a>
        </nav>
        <div class="icons">
            <div id="menu-btn" class="fa fa-bars"></div>
            <div id="user-btn" class="fa fa-user"></div>
        </div>
        <div class="profile">
            <div class="user-btn">
                <p><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></p>
                <i class="fa fa-user"></i>
            </div>
            <div class="dropdown">
                <a href="profile_update.php" class="btn">Update Profile</a>
                <a href="../logout.php" class="delete-btn">Logout</a>
            </div>
        </div>
    </div>
</header>