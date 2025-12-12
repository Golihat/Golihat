<?php
/**
 * Plugin Name: Order Panel Plugin
 * Description: Custom order management for AE.
 * Author: OpenAI Codex
 */

// Register custom post types
function opp_register_post_types() {
    register_post_type('ae_customer', array(
        'labels' => array(
            'name' => 'Customers',
            'singular_name' => 'Customer'
        ),
        'public' => false,
        'show_ui' => true,
        'supports' => array('title'),
    ));

    register_post_type('ae_product', array(
        'labels' => array(
            'name' => 'Products',
            'singular_name' => 'Product'
        ),
        'public' => false,
        'show_ui' => true,
        'supports' => array('title'),
        'capabilities' => array(
            'edit_post' => 'manage_options',
            'edit_posts' => 'manage_options',
            'publish_posts' => 'manage_options',
            'read_post' => 'read',
            'read_private_posts' => 'manage_options',
            'delete_posts' => 'manage_options',
        ),
        'map_meta_cap' => true,
    ));

    register_post_type('ae_order', array(
        'labels' => array(
            'name' => 'Orders',
            'singular_name' => 'Order'
        ),
        'public' => false,
        'show_ui' => true,
        'supports' => array('title'),
    ));
}
add_action('init', 'opp_register_post_types');

// Check if user has role
function opp_current_user_has_role($role) {
    $user = wp_get_current_user();
    return in_array($role, (array) $user->roles);
}

// Add admin menu
function opp_register_menu() {
    if (opp_current_user_has_role('kryddsäljare')) {
        add_menu_page('Order Panel', 'Order Panel', 'read', 'opp-order-panel', 'opp_order_panel_page');
    }
}
add_action('admin_menu', 'opp_register_menu');

function opp_order_panel_page() {
    echo '<div class="wrap"><h1>Order Panel</h1><div id="opp-order-root"></div></div>';
}

// Hide product admin from non admins
function opp_hide_product_admin() {
    if (!current_user_can('manage_options')) {
        remove_menu_page('edit.php?post_type=ae_product');
    }
}
add_action('admin_menu', 'opp_hide_product_admin', 99);

// AJAX to save order
function opp_ajax_save_order() {
    if (!opp_current_user_has_role('kryddsäljare')) {
        wp_send_json_error('Not allowed');
    }

    $customer = sanitize_text_field($_POST['customer']);
    $items = json_decode(stripslashes($_POST['items']), true);
    $seller = wp_get_current_user();

    $order_id = wp_insert_post(array(
        'post_type' => 'ae_order',
        'post_title' => 'Order ' . current_time('Y-m-d H:i'),
        'post_status' => 'publish'
    ));

    if ($order_id && !is_wp_error($order_id)) {
        update_post_meta($order_id, 'customer', $customer);
        update_post_meta($order_id, 'items', $items);

        $message = "New order from {$seller->display_name}\n";
        $message .= "Customer: {$customer}\n";
        foreach ($items as $item) {
            $message .= "Product {$item['product_id']} x {$item['qty']} (discount {$item['discount']})\n";
        }

        $headers = array('Content-Type: text/plain; charset=UTF-8');
        wp_mail('order@assioengvall.se', 'New Order', $message, $headers);
        wp_mail($seller->user_email, 'Order Confirmation', $message, $headers);

        wp_send_json_success();
    }

    wp_send_json_error('Failed');
}
add_action('wp_ajax_opp_save_order', 'opp_ajax_save_order');
