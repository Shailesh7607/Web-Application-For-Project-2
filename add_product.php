<?php
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $price = floatval($_POST['price']);
    $category = sanitize($_POST['category']);
    $image = $_FILES['image'];

    // Handle image upload
    $image_name = $image['name'];
    $image_tmp = $image['tmp_name'];
    $image_path = "../img/" . basename($image_name);
    
    if (move_uploaded_file($image_tmp, $image_path)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO Products (name, category, price, image, availability) VALUES (?, ?, ?, ?, TRUE)");
            $stmt->execute([$name, $category, $price, $image_path]);
            $success = "Product added successfully!";
        } catch (PDOException $e) {
            $error = "Failed to add product: " . $e->getMessage();
        }
    } else {
        $error = "Failed to upload image.";
    }
}
?>
<section class="add-products form-container">
    <form action="" method="POST" enctype="multipart/form-data" class="form">
        <h3>Add Product</h3>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <input type="text" name="name" class="box" maxlength="100" placeholder="Enter product name" required>
        <input type="number" name="price" class="box" placeholder="Enter product price" min="0" max="9999999999" required>
        <select name="category" class="box" required>
            <option value="" disabled selected>Select category</option>
            <option value="Snack Pack">Snack Pack</option>
            <option value="Momo">Momo</option>
            <option value="Chowmein">Chowmein</option>
            <option value="Khaja Set">Khaja Set</option>
            <option value="Kids Menu">Kids Menu</option>
            <option value="Drinks">Drinks</option>
        </select>
        <input type="file" name="image" class="box" accept="image/jpg,image/jpeg,image/png,image/webp" required>
        <input type="submit" name="add_product" value="Add Product" class="btn">
    </form>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>