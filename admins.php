<?php include 'header.php'; ?>
<section class="accounts form-container">
    <h1 class="heading">Admins Account</h1>
    <div class="box-container">
        <div class="box">
            <p>Register New Admin</p>
            <a href="register_admin.php" class="btn btn-primary">Register</a>
        </div>
        <div class="box">
            <p>Delete Admin Account</p>
            <form id="admin-delete-form" action="delete_admin.php" method="POST" class="form">
                <input type="text" name="username" placeholder="Enter Username" required class="box">
                <input type="submit" name="delete_admin" value="Delete Account" class="btn btn-primary delete-btn" id="delete-btn" onclick="return confirm('Are you sure you want to delete this admin?');">
            </form>
            <p id="message" style="display: none; color: green;">Admin deleted successfully!</p>
        </div>
    </div>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>