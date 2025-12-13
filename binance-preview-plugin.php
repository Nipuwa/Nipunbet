
<?php
/*
Plugin Name: Binance API Preview (Read Only)
Description: Preview Binance account balance using Read-Only API
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

// === SETTINGS PAGE ===
add_action('admin_menu', function () {
    add_menu_page(
        'Binance Preview',
        'Binance Preview',
        'manage_options',
        'binance-preview',
        'binance_preview_settings'
    );
});

function binance_preview_settings() {
    if (isset($_POST['save_keys'])) {
        update_option('binance_api_key', sanitize_text_field($_POST['api_key']));
        update_option('binance_secret_key', sanitize_text_field($_POST['secret_key']));
        echo "<div class='updated'><p>Saved</p></div>";
    }

    $api = get_option('binance_api_key');
    $secret = get_option('binance_secret_key');
    ?>
    <div class="wrap">
        <h2>Binance API Preview (Read Only)</h2>
        <form method="post">
            <input type="text" name="api_key" placeholder="API KEY" value="<?php echo esc_attr($api); ?>" style="width:400px"><br><br>
            <input type="password" name="secret_key" placeholder="SECRET KEY" value="<?php echo esc_attr($secret); ?>" style="width:400px"><br><br>
            <button name="save_keys" class="button button-primary">Save</button>
        </form>
    </div>
    <?php
}

// === BINANCE REQUEST ===
function binance_request($endpoint) {
    $apiKey = get_option('binance_api_key');
    $secret = get_option('binance_secret_key');

    if (!$apiKey || !$secret) return false;

    $timestamp = round(microtime(true) * 1000);
    $query = "timestamp=$timestamp";
    $signature = hash_hmac('sha256', $query, $secret);

    $url = "https://api.binance.com$endpoint?$query&signature=$signature";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-MBX-APIKEY: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

// === SHORTCODE ===
add_shortcode('binance_preview', function () {
    $data = binance_request('/api/v3/account');
    if (!$data || isset($data['code'])) return "API Error";

    foreach ($data['balances'] as $b) {
        if ($b['asset'] === 'USDT') {
            return "<b>USDT Balance:</b> " . $b['free'];
        }
    }
    return "USDT not found";
});
