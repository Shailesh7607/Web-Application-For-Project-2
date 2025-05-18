<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'admin') {
    $username = sanitize($_POST['username']);
    try {
        $stmt = $pdo->prepare("SELECT id FROM Users WHERE name = ? AND user_type = 'admin'");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin && $admin['id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM Users WHERE id = ?");
            $stmt->execute([$admin['id']]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Admin not found or cannot delete self.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Unauthorized access.']);
}
?>