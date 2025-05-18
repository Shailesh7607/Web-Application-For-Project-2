<?php
include 'config.php';

if (!isset($_POST['paypal_order_id']) || !isset($_SESSION['pending_order'])) {
    header("Location: order.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pending_order = $_SESSION['pending_order'];
$cart_items = $pending_order['cart_items'];
$pickup_time = $pending_order['pickup_time'];
$is_now = $pending_order['is_now'];
$customer_name = $pending_order['customer_name'];

// PayPal API credentials
//  Paste your PayPal Client ID here in place of YOUR_PAYPAL_CLIENT_ID
$paypal_client_id = 'AdpjjArn3L9FsTQy5UzUGy7-GoxBDh2aij7-87WvBydMxVhkoPt0fNojhVpwoBX0xxb-kcxrg_apPLK4';
// Paste your PayPal Secret here in place of YOUR_PAYPAL_SECRET
$paypal_secret = 'EJAWw5tIRnohW5uqf3RzWJqcKpAM1vCWa_tGGv9vTsoCUS_xpxXguBEBqbRwmzs0Mwy0PBawCYsAtBRi';
$paypal_url = 'https://api-m.paypal.com';//'https://api-m.sandbox.paypal.com'; // Use https://api-m.paypal.com for live

try {
    // Get PayPal access token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$paypal_url/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_USERPWD, "$paypal_client_id:$paypal_secret");
    $response = curl_exec($ch);
    curl_close($ch);
    
    $token_data = json_decode($response, true);
    if (!isset($token_data['access_token'])) {
        throw new Exception('Failed to obtain PayPal access token');
    }
    $access_token = $token_data['access_token'];

    // Capture PayPal order
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$paypal_url/v2/checkout/orders/{$_POST['paypal_order_id']}/capture");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $access_token"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $order_data = json_decode($response, true);
    if ($order_data['status'] !== 'COMPLETED') {
        throw new Exception('Payment not completed');
    }

    // Save order to database
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO Orders (user_id, total, status, pickup_time, is_now, customer_name) VALUES (?, ?, 'completed', ?, ?, ?)");
    $stmt->execute([$user_id, $pending_order['total'], $pickup_time, $is_now, $customer_name]);
    $order_id = $pdo->lastInsertId();

    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO Order_Items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }

    $stmt = $pdo->prepare("INSERT INTO Payment (order_id, amount, payment_method, status, paypal_transaction_id) VALUES (?, ?, ?, 'completed', ?)");
    $stmt->execute([$order_id, $pending_order['total'], 'paypal', $order_data['id']]);

    $stmt = $pdo->prepare("DELETE FROM Cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();

    unset($_SESSION['pending_order']);
    echo json_encode(['success' => true]);
    exit();
} catch (Exception $e) {
    $pdo->rollback();
    $error = "Payment error: " . $e->getMessage();
    echo json_encode(['success' => false, 'error' => $error]);
    exit();
}
?>