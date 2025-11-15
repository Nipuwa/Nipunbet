<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== REGISTER =====
    if(isset($_POST['register'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $postData = array(
            'key' => $api_key,
            'action' => 'create_user',  // API provider user creation
            'username' => $username,
            'email' => $email,
            'password' => $password
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if(isset($result['success']) && $result['success'] == true){
            // Registration success → redirect to login
            header("Location: login.html");
            exit();
        } else {
            echo "<h2>Registration Failed</h2>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
            echo "<a href='register.html'>Try Again</a>";
        }
    }

    // ===== LOGIN =====
    if(isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $postData = array(
            'key' => $api_key,
            'action' => 'login',  // API login
            'username' => $username,
            'password' => $password
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if(isset($result['success']) && $result['success'] == true){
            // Login success → start session
            $_SESSION['username'] = $username;
            header("Location: services.html");
            exit();
        } else {
            echo "<h2>Login Failed</h2>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
            echo "<a href='login.html'>Try Again</a>";
        }
    }

    // ===== ORDER =====
    if(isset($_POST['service'])){
        // Check if user logged in
        if(!isset($_SESSION['username'])){
            header("Location: login.html");
            exit();
        }

        $service = $_POST['service'];
        $username_link = $_POST['username'];
        $quantity = $_POST['quantity'];

        $postData = array(
            'key' => $api_key,
            'action' => 'add',
            'service' => $service,
            'link' => $username_link,
            'quantity' => $quantity
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if(isset($result['order'])){
            header("Location: order-success.html");
            exit();
        } else {
            echo "<h2>Order Failed</h2>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
            echo "<a href='order.html'>Try Again</a>";
        }
    }
}

// ===== LOGOUT =====
if(isset($_GET['logout'])){
    session_start();
    session_destroy();
    header("Location: login.html");
    exit();
}
?>
