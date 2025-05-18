<?php
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    if ($pass === $cpass) {
        try {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO Users (name, email, phone, password, user_type) VALUES (?, ?, ?, ?, 'admin')");
            $stmt->execute([$name, $name . '@admin.com', '0000000000', $hashed_pass]);
            $success = "Admin registered successfully!";
        } catch (PDOException $e) {
            $error = "Failed to register admin: " . $e->getMessage();
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>
<section class="form-container">
    <form action="" method="POST" class="form">
        <h3>Register New Admin</h3>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <input type="text" name="name" maxlength="20" required placeholder="Enter your username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="pass" maxlength="20" required placeholder="Enter your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="cpass" maxlength="20" required placeholder="Confirm your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="Register Now" name="submit" class="btn btn-primary">
    </form>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>