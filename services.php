<?php
// services.php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // POST data
    $service = $_POST['service'];
    $username = $_POST['username'];
    $quantity = $_POST['quantity'];

    // Prepare API request data
    $postData = array(
        'key' => $api_key,
        'action' => 'add',
        'service' => $service,
        'link' => $username,
        'quantity' => $quantity
    );

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if(curl_errno($ch)){
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    // Decode API response
    $result = json_decode($response, true);

    if(isset($result['order'])) {
        echo "<h2>Order Placed Successfully!</h2>";
        echo "<p>Order ID: " . $result['order'] . "</p>";
        echo "<p>Service: " . htmlspecialchars($service) . "</p>";
        echo "<p>Quantity: " . htmlspecialchars($quantity) . "</p>";
        echo "<a href='services.html'>Back to Services</a>";
    } else {
        echo "<h2>Order Failed</h2>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        echo "<a href='order.html'>Try Again</a>";
    }
}
?>
