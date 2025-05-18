<?php
include 'header.php';

$product = null;
if (isset($_GET['pid'])) {
    $pid = (int)$_GET['pid'];
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE id = ?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $pid = (int)$_POST['pid'];
    $name = sanitize($_POST['name']);
    $price = floatval($_POST['price']);
    $category = sanitize($_POST['category']);
    $old_image = sanitize($_POST['old_image']);
    $image = $_FILES['image']['name'] ? $_FILES['image'] : null;

    $image_path = $old_image;
    if ($image) {
        $image_name = $image['name'];
        $image_tmp = $image['tmp_name'];
        $image_path = "../img/" . basename($image_name);
        if (!move_uploaded_file($image_tmp, $image_path)) {
            $error = "Failed to upload image.";
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE Products SET name = ?, category = ?, price = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $category, $price, $image_path, $pid]);
        $success = "Product updated successfully!";
        header("Location: update_product.php");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to update product: " . $e->getMessage();
    }
}
?>
<section class="orders">
    <h1 class="heading">Update Product</h1>
    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if ($product): ?>
    <form action="" method="POST" enctype="multipart/form-data" class="form">
        <input type="hidden" name="pid" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="old_image" value="<?php echo $product['image']; ?>">
        <img src="<?php echo $product['image']; ?>" alt="" style="max-width: 200px; margin-bottom: 1rem;">
        <input type="text" required placeholder="Enter product name" name="name" maxlength="100" class="box" value="<?php echo $product['name']; ?>">
        <input type="number" min="0" max="9999999999" step="0.01" required placeholder="Enter product price" name="price" class="box" value="<?php echo $product['price']; ?>">
        <span>Update category</span>
        <select name="category" class="box" required>
            <option value="<?php echo $product['category']; ?>" selected><?php echo $product['category']; ?></option>
            <option value="Snack Pack">Snack Pack</option>
            <option value="Momo">Momo</option>
            <option value="Chowmein">Chowmein</option>
            <option value="Khaja Set">Khaja Set</option>
            <option value="Kids Menu">Kids Menu</option>
            <option value="Drinks">Drinks</option>
        </select>
        <span>Update image</span>
        <input type="file" name="image" class="box" accept="image/jpg,image/jpeg,image/png,image/webp">
        <div class="btn-container">
            <input type="submit" value="Update" class="btn btn-primary" name="update">
            <a href="update_product.php?delete=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
        </div>
    </form>
    <?php else: ?>
    <div class="box-container">
        <?php
        $stmt = $pdo->query("SELECT * FROM Products");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as $prod) {
            echo "
            <div class='box'>
                <img src='{$prod['image']}' alt='{$prod['name']}' style='max-width: 150px; margin-bottom: 1rem;'>
                <p>Name: <span>{$prod['name']}</span></p>
                <p>Price: <span>\${$prod['price']}</span></p>
                <p>Category: <span>{$prod['category']}</span></p>
                <div class='btn-container'>
                    <a href='update_product.php?pid={$prod['id']}' class='btn btn-primary'>Edit</a>
                    <a href='update_product.php?delete={$prod['id']}' class='delete-btn' onclick='return confirm(\"Delete this product?\");'>Delete</a>
                </div>
            </div>";
        }
        ?>
    </div>
    <?php endif; ?>
    <?php
    if (isset($_GET['delete'])) {
        $delete_id = (int)$_GET['delete'];
        try {
            $stmt = $pdo->prepare("DELETE FROM Products WHERE id = ?");
            $stmt->execute([$delete_id]);
            header("Location: update_product.php");
            exit();
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Failed to delete product: " . $e->getMessage() . "</p>";
        }
    }
    ?>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>