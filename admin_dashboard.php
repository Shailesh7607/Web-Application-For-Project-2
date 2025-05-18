<?php include 'header.php'; ?>
<section class="dashboard">
    <h1 class="heading">Admin Dashboard</h1>
    <div class="box-container">
        <div class="box">
            <h3>Welcome!</h3>
            <p>Profile Details</p>
            <a href="profile_update.php" class="btn btn-primary">Update Profile</a>
        </div>
        <div class="box">
            <p>Orders</p>
            <a href="orders.php" class="btn btn-primary">See Orders</a>
        </div>
        <div class="box">
            <p>Add Products</p>
            <a href="add_product.php" class="btn btn-primary">Add</a>
        </div>
        <div class="box">
            <p>Update Product</p>
            <a href="update_product.php" class="btn btn-primary">Update</a>
        </div>
        <div class="box">
            <p>Admins</p>
            <a href="admins.php" class="btn btn-primary">See Admins</a>
        </div>
        <div class="box">
            <p>New Messages</p>
            <a href="messages.php" class="btn btn-primary">See Messages</a>
        </div>
    </div>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>