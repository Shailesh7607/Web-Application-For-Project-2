<?php include 'header.php'; ?>
<section class="messages">
    <h1 class="heading">Messages</h1>
    <div class="box-container">
        <?php
        $stmt = $pdo->query("SELECT * FROM Messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($messages as $msg) {
            echo "
            <div class='box'>
                <p>Name: <span>{$msg['name']}</span></p>
                <p>Number: <span>{$msg['phone']}</span></p>
                <p>Email: <span>{$msg['email']}</span></p>
                <p>Subject: <span>{$msg['subject']}</span></p>
                <p>Message: <span>{$msg['message']}</span></p>
                <a href='delete_message.php?id={$msg['id']}' class='btn btn-primary delete-btn' onclick='return confirm(\"Delete this message?\");'>Delete</a>
            </div>";
        }
        if (empty($messages)) {
            echo "<p>No messages found.</p>";
        }
        ?>
    </div>
</section>
<script src="/project-22/js/admin.js"></script>
</body>
</html>