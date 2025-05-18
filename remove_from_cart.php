<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = (int)$_POST['cart_id'];
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_count = $stmt->fetchColumn();

        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>