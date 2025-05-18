<?php
include '../config.php';

if (isset($_GET['id']) && isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'admin') {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Messages WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: messages.php");
        exit();
    } catch (PDOException $e) {
        echo "Failed to delete message: " . $e->getMessage();
    }
} else {
    header("Location: messages.php");
    exit();
}
?>