<?php include 'header.php'; ?>
<section class="orders">
    <h1 class="heading">Placed Orders</h1>
    <div class="box-container">
        <?php
        $stmt = $pdo->query("SELECT o.*, u.email as user_email FROM Orders o LEFT JOIN Users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($orders as $order) {
            $stmt = $pdo->prepare("SELECT p.name, oi.quantity, oi.price FROM Order_Items oi JOIN Products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $stmt->execute([$order['id']]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $products_list = array_map(function($item) { return "{$item['name']} (x{$item['quantity']})"; }, $items);
            // Format pickup time for display
            $pickup_time_display = $order['is_now'] ? "Now (" . DateTime::createFromFormat('H:i:s', $order['pickup_time'])->format('g:i A') . ")" : DateTime::createFromFormat('H:i:s', $order['pickup_time'])->format('g:i A');
            echo "
            <div class='box'>
                <p>User ID: <span>{$order['user_id']}</span></p>
                <p>Email: <span>{$order['user_email']}</span></p>
                <p>Customer Name: <span>{$order['customer_name']}</span></p>
                <p>Pickup Time: <span>{$pickup_time_display}</span></p>
                <p>Total Products: <span>" . implode(', ', $products_list) . "</span></p>
                <p>Total Price: <span>\${$order['total']}</span></p>
                <form action='update_order.php' method='POST' class='form'>
                    <input type='hidden' name='order_id' value='{$order['id']}'>
                    <select name='payment_status' class='drop-down'>
                        <option value='' disabled " . ($order['status'] == '' ? 'selected' : '') . ">Select status</option>
                        <option value='pending' " . ($order['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                        <option value='completed' " . ($order['status'] == 'completed' ? 'selected' : '') . ">Completed</option>
                    </select>
                    <div class='btn-container'>
                        <input type='submit' name='update_payment' value='Update' class='btn btn-primary'>
                        <a href='update_order.php?delete={$order['id']}' class='delete-btn' onclick='return confirm(\"Delete this order?\");'>Delete</a>
                    </div>
                </form>
            </div>";
        }
        if (empty($orders)) {
            echo "<p>No orders found.</p>";
        }
        ?>
    </div>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>