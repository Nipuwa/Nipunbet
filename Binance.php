<?php
/*
Plugin Name: Binance Payment Gateway (Manual Verify)
Description: Binance Internal Transfer payment gateway with Order ID verification
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

/* ===============================
   DATABASE TABLE
================================ */
register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table = $wpdb->prefix . "binance_payments";
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        amount FLOAT,
        order_id VARCHAR(255),
        status VARCHAR(20) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

/* ===============================
   SHORTCODE – USER PAYMENT FORM
   [binance_payment amount="10"]
================================ */
add_shortcode('binance_payment', function ($atts) {
    global $wpdb;
    $atts = shortcode_atts(['amount' => 10], $atts);
    $amount = floatval($atts['amount']);

    if (!is_user_logged_in()) {
        return "<p>Please login to make payment.</p>";
    }

    if (isset($_POST['submit_binance_payment'])) {
        $wpdb->insert(
            $wpdb->prefix . "binance_payments",
            [
                'user_id' => get_current_user_id(),
                'amount' => $amount,
                'order_id' => sanitize_text_field($_POST['order_id']),
                'status' => 'pending'
            ]
        );
        return "<p>✅ Payment submitted. Waiting for admin verification.</p>";
    }

    ob_start(); ?>
    <h3>Binance Payment</h3>
    <p><b>Amount:</b> <?php echo esc_html($amount); ?> USDT</p>
    <p><b>Send To Binance ID:</b> <b>1171771235</b></p>

    <form method="post">
        <input type="text" name="order_id" placeholder="Enter Binance Order ID" required>
        <br><br>
        <button type="submit" name="submit_binance_payment">Submit Payment</button>
    </form>
    <?php
    return ob_get_clean();
});

/* ===============================
   ADMIN MENU
================================ */
add_action('admin_menu', function () {
    add_menu_page(
        'Binance Payments',
        'Binance Payments',
        'manage_options',
        'binance-payments',
        'binance_admin_page'
    );
});

/* ===============================
   ADMIN PAGE – VERIFY PAYMENTS
================================ */
function binance_admin_page() {
    global $wpdb;
    $table = $wpdb->prefix . "binance_payments";

    if (isset($_GET['approve'])) {
        $wpdb->update($table, ['status' => 'completed'], ['id' => intval($_GET['approve'])]);
        echo "<div class='updated'><p>Payment Approved</p></div>";
    }

    $payments = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>
    <div class="wrap">
        <h2>Binance Payments</h2>
        <table class="widefat">
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Amount</th>
                <th>Order ID</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td><?php echo $p->id; ?></td>
                <td><?php echo $p->user_id; ?></td>
                <td><?php echo $p->amount; ?></td>
                <td><?php echo esc_html($p->order_id); ?></td>
                <td><?php echo $p->status; ?></td>
                <td>
                    <?php if ($p->status === 'pending'): ?>
                        <a href="?page=binance-payments&approve=<?php echo $p->id; ?>" class="button">Approve</a>
                    <?php else: ?>
                        ✔ Completed
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
}
