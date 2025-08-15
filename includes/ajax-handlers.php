<?php
/**
 * AJAX Handlers for Nanimade Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for getting cart data
 */
function nanimade_get_cart_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    if (!function_exists('WC')) {
        wp_send_json_error('WooCommerce not active');
    }
    
    $cart_items = array();
    $cart = WC()->cart;
    
    if ($cart && !$cart->is_empty()) {
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $product_id = $cart_item['product_id'];
            
            $cart_items[] = array(
                'key' => $cart_item_key,
                'name' => $product->get_name(),
                'quantity' => $cart_item['quantity'],
                'price' => WC()->cart->get_product_price($product),
                'image' => wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'thumbnail')[0] ?? '',
                'url' => get_permalink($product_id)
            );
        }
    }
    
    wp_send_json_success(array(
        'items' => $cart_items,
        'total' => $cart ? $cart->get_cart_total() : '',
        'count' => $cart ? $cart->get_cart_contents_count() : 0
    ));
}
add_action('wp_ajax_nanimade_get_cart', 'nanimade_get_cart_ajax');
add_action('wp_ajax_nopriv_nanimade_get_cart', 'nanimade_get_cart_ajax');

/**
 * AJAX handler for adding to cart
 */
function nanimade_add_to_cart_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    if (!function_exists('WC')) {
        wp_send_json_error('WooCommerce not active');
    }
    
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']) ?: 1;
    
    $result = WC()->cart->add_to_cart($product_id, $quantity);
    
    if ($result) {
        wp_send_json_success(array(
            'message' => 'Product added to cart!',
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total()
        ));
    } else {
        wp_send_json_error('Failed to add product to cart');
    }
}
add_action('wp_ajax_nanimade_add_to_cart', 'nanimade_add_to_cart_ajax');
add_action('wp_ajax_nopriv_nanimade_add_to_cart', 'nanimade_add_to_cart_ajax');

/**
 * AJAX handler for one-click reorder
 */
function nanimade_one_click_reorder_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    if (!function_exists('WC')) {
        wp_send_json_error('WooCommerce not active');
    }
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please login to reorder');
    }
    
    $order_id = intval($_POST['order_id']);
    $user_id = get_current_user_id();
    
    // Verify order belongs to user
    $order = wc_get_order($order_id);
    if (!$order || $order->get_customer_id() != $user_id) {
        wp_send_json_error('Invalid order');
    }
    
    // Create new cart with order items
    WC()->cart->empty_cart();
    
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $quantity = $item->get_quantity();
        
        // Check if product still exists and is purchasable
        $product = wc_get_product($product_id);
        if ($product && $product->is_purchasable()) {
            WC()->cart->add_to_cart($product_id, $quantity);
        }
    }
    
    wp_send_json_success(array(
        'message' => 'Items added to cart successfully!',
        'cart_count' => WC()->cart->get_cart_contents_count()
    ));
}
add_action('wp_ajax_nanimade_one_click_reorder', 'nanimade_one_click_reorder_ajax');

/**
 * AJAX handler for order tracking
 */
function nanimade_track_order_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    $tracking_number = sanitize_text_field($_POST['tracking_number']);
    
    if (empty($tracking_number)) {
        wp_send_json_error('Tracking number is required');
    }
    
    // Get tracking data from Shiprocket API
    $tracking_data = nanimade_get_shiprocket_tracking($tracking_number);
    
    if ($tracking_data) {
        wp_send_json_success($tracking_data);
    } else {
        wp_send_json_error('Unable to track order');
    }
}
add_action('wp_ajax_nanimade_track_order', 'nanimade_track_order_ajax');
add_action('wp_ajax_nopriv_nanimade_track_order', 'nanimade_track_order_ajax');

/**
 * AJAX handler for search suggestions
 */
function nanimade_search_suggestions_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    $query = sanitize_text_field($_POST['query']);
    
    if (strlen($query) < 3) {
        wp_send_json_error('Query too short');
    }
    
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        's' => $query,
        'meta_query' => array(
            array(
                'key' => '_visibility',
                'value' => array('catalog', 'visible'),
                'compare' => 'IN'
            )
        )
    );
    
    $products = new WP_Query($args);
    $suggestions = array();
    
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            
            $suggestions[] = array(
                'name' => get_the_title(),
                'price' => $product->get_price_html(),
                'image' => wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail')[0] ?? '',
                'url' => get_permalink()
            );
        }
    }
    
    wp_reset_postdata();
    
    wp_send_json_success($suggestions);
}
add_action('wp_ajax_nanimade_search_suggestions', 'nanimade_search_suggestions_ajax');
add_action('wp_ajax_nopriv_nanimade_search_suggestions', 'nanimade_search_suggestions_ajax');

/**
 * AJAX handler for updating cart quantity
 */
function nanimade_update_cart_quantity_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity > 0) {
        WC()->cart->set_quantity($cart_item_key, $quantity);
    } else {
        WC()->cart->remove_cart_item($cart_item_key);
    }
    
    wp_send_json_success(array(
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_cart_total()
    ));
}
add_action('wp_ajax_nanimade_update_cart_quantity', 'nanimade_update_cart_quantity_ajax');
add_action('wp_ajax_nopriv_nanimade_update_cart_quantity', 'nanimade_update_cart_quantity_ajax');

/**
 * AJAX handler for removing cart item
 */
function nanimade_remove_cart_item_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    
    WC()->cart->remove_cart_item($cart_item_key);
    
    wp_send_json_success(array(
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_cart_total()
    ));
}
add_action('wp_ajax_nanimade_remove_cart_item', 'nanimade_remove_cart_item_ajax');
add_action('wp_ajax_nopriv_nanimade_remove_cart_item', 'nanimade_remove_cart_item_ajax');

/**
 * Get Shiprocket tracking data
 */
function nanimade_get_shiprocket_tracking($tracking_number) {
    $api_url = 'https://apiv2.shiprocket.in/v1/external/courier/track/shipment/' . $tracking_number;
    $token = get_option('nanimade_shiprocket_token');
    
    if (!$token) {
        return false;
    }
    
    $response = wp_remote_get($api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ),
        'timeout' => 30
    ));
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if ($data && isset($data['data'])) {
        $tracking_info = $data['data'];
        
        return array(
            'status' => $tracking_info['shipment_status'] ?? 'Unknown',
            'location' => $tracking_info['current_location'] ?? 'Unknown',
            'last_update' => $tracking_info['last_update'] ?? 'Unknown',
            'tracking_number' => $tracking_number,
            'courier' => $tracking_info['courier_name'] ?? 'Unknown'
        );
    }
    
    return false;
}

/**
 * AJAX handler for getting analytics data
 */
function nanimade_get_analytics_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please login to view analytics');
    }
    
    $user_id = get_current_user_id();
    $period = sanitize_text_field($_POST['period']) ?: 'month';
    
    $analytics_data = nanimade_get_user_analytics($user_id, $period);
    
    wp_send_json_success($analytics_data);
}
add_action('wp_ajax_nanimade_get_analytics', 'nanimade_get_analytics_ajax');

/**
 * Get user analytics data
 */
function nanimade_get_user_analytics($user_id, $period = 'month') {
    $end_date = current_time('Y-m-d');
    
    switch ($period) {
        case 'week':
            $start_date = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'month':
            $start_date = date('Y-m-d', strtotime('-30 days'));
            break;
        case 'year':
            $start_date = date('Y-m-d', strtotime('-1 year'));
            break;
        default:
            $start_date = date('Y-m-d', strtotime('-30 days'));
    }
    
    $orders = wc_get_orders(array(
        'customer_id' => $user_id,
        'date_created' => $start_date . '...' . $end_date,
        'limit' => -1
    ));
    
    $total_orders = count($orders);
    $total_spent = 0;
    $order_dates = array();
    $order_amounts = array();
    
    foreach ($orders as $order) {
        $total_spent += $order->get_total();
        $order_dates[] = $order->get_date_created()->format('M d');
        $order_amounts[] = $order->get_total();
    }
    
    return array(
        'total_orders' => $total_orders,
        'total_spent' => $total_spent,
        'average_order' => $total_orders > 0 ? $total_spent / $total_orders : 0,
        'order_dates' => $order_dates,
        'order_amounts' => $order_amounts,
        'period' => $period
    );
}

/**
 * AJAX handler for product quick view
 */
function nanimade_quick_view_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error('Product not found');
    }
    
    ob_start();
    
    echo '<div class="quick-view-content">';
    echo '<div class="quick-view-image">';
    echo get_the_post_thumbnail($product_id, 'medium');
    echo '</div>';
    echo '<div class="quick-view-details">';
    echo '<h3>' . $product->get_name() . '</h3>';
    echo '<div class="price">' . $product->get_price_html() . '</div>';
    echo '<div class="description">' . wp_trim_words($product->get_short_description(), 20) . '</div>';
    
    if ($product->is_in_stock()) {
        echo '<form class="quick-view-form">';
        echo '<input type="hidden" name="product_id" value="' . $product_id . '">';
        echo '<div class="quantity-wrapper">';
        echo '<label>Quantity:</label>';
        echo '<input type="number" name="quantity" value="1" min="1" max="' . $product->get_max_purchase_quantity() . '">';
        echo '</div>';
        echo '<button type="submit" class="add-to-cart-btn" data-product-id="' . $product_id . '">Add to Cart</button>';
        echo '</form>';
    } else {
        echo '<p class="out-of-stock">Out of Stock</p>';
    }
    
    echo '</div>';
    echo '</div>';
    
    $content = ob_get_clean();
    
    wp_send_json_success(array('content' => $content));
}
add_action('wp_ajax_nanimade_quick_view', 'nanimade_quick_view_ajax');
add_action('wp_ajax_nopriv_nanimade_quick_view', 'nanimade_quick_view_ajax');

/**
 * AJAX handler for wishlist functionality
 */
function nanimade_add_to_wishlist_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please login to add items to wishlist');
    }
    
    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();
    
    $wishlist = get_user_meta($user_id, 'nanimade_wishlist', true);
    if (!is_array($wishlist)) {
        $wishlist = array();
    }
    
    if (!in_array($product_id, $wishlist)) {
        $wishlist[] = $product_id;
        update_user_meta($user_id, 'nanimade_wishlist', $wishlist);
        wp_send_json_success('Product added to wishlist');
    } else {
        wp_send_json_error('Product already in wishlist');
    }
}
add_action('wp_ajax_nanimade_add_to_wishlist', 'nanimade_add_to_wishlist_ajax');

/**
 * AJAX handler for removing from wishlist
 */
function nanimade_remove_from_wishlist_ajax() {
    check_ajax_referer('nanimade_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please login to manage wishlist');
    }
    
    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();
    
    $wishlist = get_user_meta($user_id, 'nanimade_wishlist', true);
    if (is_array($wishlist)) {
        $wishlist = array_diff($wishlist, array($product_id));
        update_user_meta($user_id, 'nanimade_wishlist', $wishlist);
        wp_send_json_success('Product removed from wishlist');
    } else {
        wp_send_json_error('Wishlist not found');
    }
}
add_action('wp_ajax_nanimade_remove_from_wishlist', 'nanimade_remove_from_wishlist_ajax');

/**
 * AJAX handler for getting Shiprocket tracking data
 */
function nanimade_get_shiprocket_tracking_ajax() {
    check_ajax_referer('nanimade_checkout_nonce', 'nonce');
    
    $tracking_number = sanitize_text_field($_POST['tracking_number']);
    $order_id = intval($_POST['order_id']);
    
    if (empty($tracking_number)) {
        wp_send_json_error('Tracking number required');
    }
    
    // Get tracking data from Shiprocket
    $tracking_data = nanimade_get_shiprocket_tracking_data($tracking_number);
    
    if ($tracking_data) {
        wp_send_json_success($tracking_data);
    } else {
        wp_send_json_error('Unable to fetch tracking information');
    }
}
add_action('wp_ajax_nanimade_get_tracking', 'nanimade_get_shiprocket_tracking_ajax');
add_action('wp_ajax_nopriv_nanimade_get_tracking', 'nanimade_get_shiprocket_tracking_ajax');

/**
 * Get Shiprocket tracking data
 */
function nanimade_get_shiprocket_tracking_data($tracking_number) {
    $token = get_transient('shiprocket_token');
    
    if (!$token) {
        $email = get_option('nanimade_shiprocket_email');
        $password = get_option('nanimade_shiprocket_password');
        
        if (empty($email) || empty($password)) {
            return false;
        }
        
        $response = wp_remote_post('https://apiv2.shiprocket.in/v1/external/auth/login', array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode(array(
                'email' => $email,
                'password' => $password
            ))
        ));
        
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['token'])) {
                $token = $body['token'];
                set_transient('shiprocket_token', $token, 3600);
            }
        }
    }
    
    if ($token) {
        $response = wp_remote_get('https://apiv2.shiprocket.in/v1/external/courier/track/shipment/' . $tracking_number, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token
            )
        ));
        
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            return $body;
        }
    }
    
    return false;
}

/**
 * AJAX handler for testing Shiprocket connection
 */
function nanimade_test_shiprocket_connection_ajax() {
    check_ajax_referer('nanimade_test_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $email = get_option('nanimade_shiprocket_email');
    $password = get_option('nanimade_shiprocket_password');
    
    if (empty($email) || empty($password)) {
        wp_send_json_error('Please configure Shiprocket credentials first');
    }
    
    $response = wp_remote_post('https://apiv2.shiprocket.in/v1/external/auth/login', array(
        'headers' => array('Content-Type' => 'application/json'),
        'body' => json_encode(array(
            'email' => $email,
            'password' => $password
        ))
    ));
    
    if (is_wp_error($response)) {
        wp_send_json_error('Network error: ' . $response->get_error_message());
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (isset($body['token'])) {
        set_transient('shiprocket_token', $body['token'], 3600);
        wp_send_json_success(array('token' => $body['token']));
    } else {
        wp_send_json_error('Invalid credentials or API error');
    }
}
add_action('wp_ajax_nanimade_test_shiprocket_connection', 'nanimade_test_shiprocket_connection_ajax');

/**
 * AJAX handler for getting analytics data
 */
function nanimade_get_analytics_data_ajax() {
    check_ajax_referer('nanimade_analytics_nonce', 'nonce');
    
    if (!current_user_can('manage_woocommerce')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $date_range = intval($_POST['date_range']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    
    // Calculate date range
    if ($date_range === 'custom' && $start_date && $end_date) {
        $start = $start_date;
        $end = $end_date;
    } else {
        $end = current_time('Y-m-d');
        $start = date('Y-m-d', strtotime("-{$date_range} days"));
    }
    
    // Get analytics data
    $analytics_data = array(
        'metrics' => nanimade_get_metrics($start, $end),
        'charts' => nanimade_get_chart_data_admin($start, $end),
        'activity' => nanimade_get_recent_activity()
    );
    
    wp_send_json_success($analytics_data);
}
add_action('wp_ajax_nanimade_get_analytics', 'nanimade_get_analytics_data_ajax');

/**
 * Get metrics for analytics
 */
function nanimade_get_metrics($start_date, $end_date) {
    $orders = wc_get_orders(array(
        'date_created' => $start_date . '...' . $end_date,
        'status' => array('completed', 'processing'),
        'limit' => -1
    ));
    
    $revenue = 0;
    $order_count = count($orders);
    $products_sold = 0;
    
    foreach ($orders as $order) {
        $revenue += $order->get_total();
        $products_sold += $order->get_item_count();
    }
    
    // Get new customers
    $customers = get_users(array(
        'role' => 'customer',
        'date_query' => array(
            array(
                'after' => $start_date,
                'before' => $end_date,
                'inclusive' => true
            )
        )
    ));
    
    return array(
        'revenue' => $revenue,
        'orders' => $order_count,
        'customers' => count($customers),
        'products' => $products_sold,
        'revenue_change' => 15.5, // Calculate actual change
        'orders_change' => 8.2,
        'customers_change' => 12.1,
        'products_change' => 6.8
    );
}

/**
 * Get chart data for admin analytics
 */
function nanimade_get_chart_data_admin($start_date, $end_date) {
    // Generate sample data - replace with actual data
    return array(
        'trend' => array(
            'labels' => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'),
            'revenue' => array(12000, 15000, 18000, 22000, 25000, 28000)
        ),
        'products' => array(
            'labels' => array('Mango Pickle', 'Lemon Pickle', 'Mixed Pickle', 'Garlic Pickle'),
            'data' => array(35, 25, 20, 20)
        ),
        'category' => array(
            'labels' => array('Sweet', 'Spicy', 'Tangy', 'Mixed'),
            'data' => array(45, 30, 15, 10)
        ),
        'demographics' => array(
            'labels' => array('18-25', '26-35', '36-45', '45+'),
            'data' => array(20, 35, 30, 15)
        ),
        'status' => array(
            'labels' => array('Completed', 'Processing', 'Shipped', 'Cancelled'),
            'data' => array(60, 25, 10, 5)
        ),
        'payment' => array(
            'labels' => array('COD', 'Online', 'UPI', 'Card'),
            'data' => array(40, 30, 20, 10)
        )
    );
}

/**
 * Get recent activity
 */
function nanimade_get_recent_activity() {
    return array(
        array(
            'icon' => '📦',
            'title' => 'New order #1234 received',
            'time' => '2 minutes ago'
        ),
        array(
            'icon' => '🚚',
            'title' => 'Order #1233 shipped',
            'time' => '15 minutes ago'
        ),
        array(
            'icon' => '👤',
            'title' => 'New customer registered',
            'time' => '1 hour ago'
        ),
        array(
            'icon' => '💰',
            'title' => 'Payment received for order #1232',
            'time' => '2 hours ago'
        )
    );
}
