<?php
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    try {
        $stmt = $pdo->prepare("SELECT password FROM Users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($old_pass, $user['password'])) {
            if ($new_pass === $confirm_pass) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE Users SET name = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $hashed_pass, $_SESSION['user_id']]);
                $_SESSION['user_name'] = $name;
                $success = "Profile updated successfully!";
            } else {
                $error = "New password and confirm password do not match.";
            }
        } else {
            $error = "Old password is incorrect.";
        }
    } catch (PDOException $e) {
        $error = "Failed to update profile: " . $e->getMessage();
    }
}
?>
<section class="form-container">
    <form action="" method="POST" class="form">
        <h3>Update Profile</h3>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Enter your name" value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
        <input type="password" name="old_pass" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Enter your old password">
        <input type="password" name="new_pass" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Enter your new password">
        <input type="password" name="confirm_pass" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="Confirm your new password">
        <input type="submit" value="Update Now" name="submit" class="btn btn-primary">
    </form>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>