<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'admin') {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['payment_status']);
    try {
        $stmt = $pdo->prepare("UPDATE Orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        echo "Failed to update order: " . $e->getMessage();
    }
}

if (isset($_GET['delete']) && isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'admin') {
    $order_id = (int)$_GET['delete'];
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM Order_Items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $stmt = $pdo->prepare("DELETE FROM Payment WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $stmt = $pdo->prepare("DELETE FROM Orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $pdo->commit();
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo "Failed to delete order: " . $e->getMessage();
    }
}

header("Location: orders.php");
exit();
?>