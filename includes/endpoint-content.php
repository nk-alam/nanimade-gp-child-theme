<?php
/**
 * Endpoint Content for Nanimade Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom endpoint content for Order Tracking
 */
function nanimade_order_tracking_endpoint_content() {
    $user_id = get_current_user_id();
    $orders = wc_get_orders(array(
        'customer_id' => $user_id,
        'limit' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    echo '<div class="order-tracking-container">';
    echo '<h2>' . __('Order Tracking', 'nanimade') . '</h2>';
    echo '<p>' . __('Track your orders and get real-time updates on delivery status.', 'nanimade') . '</p>';
    
    if (!empty($orders)) {
        foreach ($orders as $order) {
            $order_id = $order->get_id();
            $order_status = $order->get_status();
            $order_date = $order->get_date_created()->format('M d, Y');
            $order_total = $order->get_total();
            $tracking_number = get_post_meta($order_id, '_shiprocket_tracking_number', true);
            
            echo '<div class="order-tracking-item">';
            echo '<div class="order-header">';
            echo '<h3>Order #' . $order_id . '</h3>';
            echo '<div class="order-meta">';
            echo '<span class="order-date">' . $order_date . '</span>';
            echo '<span class="order-total">' . wc_price($order_total) . '</span>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="order-status">';
            echo '<span class="status-badge status-' . $order_status . '">' . wc_get_order_status_name($order_status) . '</span>';
            echo '</div>';
            
            if ($tracking_number) {
                echo '<div class="tracking-info">';
                echo '<p><strong>Tracking Number:</strong> ' . $tracking_number . '</p>';
                echo '<button class="track-order-btn" data-tracking="' . $tracking_number . '">Track Order</button>';
                echo '</div>';
                
                echo '<div class="tracking-timeline">';
                echo '<div class="tracking-step completed">';
                echo '<div class="step-icon">✓</div>';
                echo '<div class="step-content">';
                echo '<h4>Order Confirmed</h4>';
                echo '<p>Your order has been confirmed and is being processed</p>';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="tracking-step completed">';
                echo '<div class="step-icon">✓</div>';
                echo '<div class="step-content">';
                echo '<h4>Processing</h4>';
                echo '<p>Your pickles are being prepared and packaged</p>';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="tracking-step active">';
                echo '<div class="step-icon">📦</div>';
                echo '<div class="step-content">';
                echo '<h4>Shipped</h4>';
                echo '<p>Your order has been shipped and is on its way</p>';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="tracking-step pending">';
                echo '<div class="step-icon">🏠</div>';
                echo '<div class="step-content">';
                echo '<h4>Delivered</h4>';
                echo '<p>Your order will be delivered soon</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<p class="no-tracking">Tracking information will be available once your order is shipped.</p>';
            }
            
            echo '</div>';
        }
    } else {
        echo '<div class="no-orders">';
        echo '<p>' . __('You haven\'t placed any orders yet.', 'nanimade') . '</p>';
        echo '<a href="' . wc_get_page_permalink('shop') . '" class="button">Start Shopping</a>';
        echo '</div>';
    }
    
    echo '</div>';
}
add_action('woocommerce_account_order-tracking_endpoint', 'nanimade_order_tracking_endpoint_content');

/**
 * Custom endpoint content for Analytics
 */
function nanimade_analytics_endpoint_content() {
    $user_id = get_current_user_id();
    
    // Get user's order statistics
    $total_orders = wc_get_customer_order_count($user_id);
    $total_spent = wc_get_customer_total_spent($user_id);
    $recent_orders = wc_get_orders(array(
        'customer_id' => $user_id,
        'limit' => 5,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    // Get favorite products
    $favorite_products = nanimade_get_favorite_products($user_id);
    
    echo '<div class="analytics-container">';
    echo '<h2>' . __('Your Analytics', 'nanimade') . '</h2>';
    echo '<p>' . __('Track your shopping behavior and discover insights about your orders.', 'nanimade') . '</p>';
    
    // Statistics Cards
    echo '<div class="dashboard-stats">';
    echo '<div class="stat-card">';
    echo '<div class="stat-icon">📦</div>';
    echo '<div class="stat-number">' . $total_orders . '</div>';
    echo '<div class="stat-label">Total Orders</div>';
    echo '</div>';
    
    echo '<div class="stat-card">';
    echo '<div class="stat-icon">💰</div>';
    echo '<div class="stat-number">' . wc_price($total_spent) . '</div>';
    echo '<div class="stat-label">Total Spent</div>';
    echo '</div>';
    
    echo '<div class="stat-card">';
    echo '<div class="stat-icon">📊</div>';
    echo '<div class="stat-number">' . ($total_orders > 0 ? wc_price($total_spent / $total_orders) : wc_price(0)) . '</div>';
    echo '<div class="stat-label">Average Order</div>';
    echo '</div>';
    
    echo '<div class="stat-card">';
    echo '<div class="stat-icon">❤️</div>';
    echo '<div class="stat-number">' . count($favorite_products) . '</div>';
    echo '<div class="stat-label">Favorite Products</div>';
    echo '</div>';
    echo '</div>';
    
    // Charts Section
    echo '<div class="charts-section">';
    echo '<div class="chart-container">';
    echo '<h3 class="chart-title">Order History</h3>';
    echo '<div class="chart-controls">';
    echo '<button class="chart-period-btn active" data-period="month">Month</button>';
    echo '<button class="chart-period-btn" data-period="quarter">Quarter</button>';
    echo '<button class="chart-period-btn" data-period="year">Year</button>';
    echo '</div>';
    echo '<canvas id="orderChart" width="400" height="200"></canvas>';
    echo '</div>';
    
    echo '<div class="chart-container">';
    echo '<h3 class="chart-title">Spending Trend</h3>';
    echo '<canvas id="spendingChart" width="400" height="200"></canvas>';
    echo '</div>';
    echo '</div>';
    
    // Recent Orders
    echo '<div class="recent-orders-section">';
    echo '<h3>Recent Orders</h3>';
    if (!empty($recent_orders)) {
        echo '<div class="recent-orders-list">';
        foreach ($recent_orders as $order) {
            $order_id = $order->get_id();
            $order_date = $order->get_date_created()->format('M d, Y');
            $order_total = $order->get_total();
            $order_status = $order->get_status();
            
            echo '<div class="recent-order-item">';
            echo '<div class="order-info">';
            echo '<h4>Order #' . $order_id . '</h4>';
            echo '<p class="order-date">' . $order_date . '</p>';
            echo '</div>';
            echo '<div class="order-details">';
            echo '<span class="order-total">' . wc_price($order_total) . '</span>';
            echo '<span class="order-status status-' . $order_status . '">' . wc_get_order_status_name($order_status) . '</span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No recent orders found.</p>';
    }
    echo '</div>';
    
    // Favorite Products
    if (!empty($favorite_products)) {
        echo '<div class="favorite-products-section">';
        echo '<h3>Your Favorite Products</h3>';
        echo '<div class="favorite-products-grid">';
        foreach ($favorite_products as $product) {
            echo '<div class="favorite-product-item">';
            echo '<div class="product-image">';
            echo '<a href="' . get_permalink($product->get_id()) . '">';
            echo get_the_post_thumbnail($product->get_id(), 'thumbnail');
            echo '</a>';
            echo '</div>';
            echo '<div class="product-info">';
            echo '<h4><a href="' . get_permalink($product->get_id()) . '">' . $product->get_name() . '</a></h4>';
            echo '<div class="product-price">' . $product->get_price_html() . '</div>';
            echo '<button class="add-to-cart-btn" data-product-id="' . $product->get_id() . '">Add to Cart</button>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    
    // Add chart data
    $chart_data = nanimade_get_chart_data($user_id);
    wp_localize_script('nanimade-custom-js', 'chart_data', $chart_data);
}
add_action('woocommerce_account_analytics_endpoint', 'nanimade_analytics_endpoint_content');

/**
 * Custom endpoint content for One-Click Reorder
 */
function nanimade_one_click_reorder_endpoint_content() {
    $user_id = get_current_user_id();
    $orders = wc_get_orders(array(
        'customer_id' => $user_id,
        'limit' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    echo '<div class="one-click-reorder-container">';
    echo '<h2>' . __('Quick Reorder', 'nanimade') . '</h2>';
    echo '<p>' . __('Reorder your previous orders with one click. Perfect for your favorite pickles!', 'nanimade') . '</p>';
    
    if (!empty($orders)) {
        echo '<div class="reorder-grid">';
        foreach ($orders as $order) {
            $order_id = $order->get_id();
            $order_date = $order->get_date_created()->format('M d, Y');
            $order_total = $order->get_total();
            $order_items = $order->get_items();
            
            echo '<div class="reorder-item">';
            echo '<div class="reorder-header">';
            echo '<h3>Order #' . $order_id . '</h3>';
            echo '<span class="order-date">' . $order_date . '</span>';
            echo '</div>';
            
            echo '<div class="reorder-items">';
            $item_count = 0;
            foreach ($order_items as $item) {
                if ($item_count < 3) { // Show only first 3 items
                    $product = $item->get_product();
                    if ($product) {
                        echo '<div class="reorder-item-product">';
                        echo '<img src="' . wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'thumbnail')[0] . '" alt="' . $product->get_name() . '">';
                        echo '<span>' . $product->get_name() . '</span>';
                        echo '</div>';
                    }
                }
                $item_count++;
            }
            if (count($order_items) > 3) {
                echo '<div class="more-items">+' . (count($order_items) - 3) . ' more</div>';
            }
            echo '</div>';
            
            echo '<div class="reorder-footer">';
            echo '<div class="order-total">' . wc_price($order_total) . '</div>';
            echo '<button class="one-click-reorder" data-order-id="' . $order_id . '">';
            echo '<span class="reorder-icon">🔄</span>';
            echo __('Reorder All', 'nanimade');
            echo '</button>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        
        // Bulk reorder section
        echo '<div class="bulk-reorder-section">';
        echo '<h3>' . __('Bulk Reorder Options', 'nanimade') . '</h3>';
        echo '<div class="bulk-reorder-options">';
        echo '<button class="bulk-reorder-btn" data-period="month">Reorder Last Month</button>';
        echo '<button class="bulk-reorder-btn" data-period="quarter">Reorder Last Quarter</button>';
        echo '<button class="bulk-reorder-btn" data-period="year">Reorder Last Year</button>';
        echo '</div>';
        echo '</div>';
        
    } else {
        echo '<div class="no-orders">';
        echo '<div class="no-orders-icon">🛒</div>';
        echo '<h3>' . __('No Previous Orders', 'nanimade') . '</h3>';
        echo '<p>' . __('You haven\'t placed any orders yet. Start shopping to enable quick reorder!', 'nanimade') . '</p>';
        echo '<a href="' . wc_get_page_permalink('shop') . '" class="button">Start Shopping</a>';
        echo '</div>';
    }
    
    echo '</div>';
}
add_action('woocommerce_account_one-click-reorder_endpoint', 'nanimade_one_click_reorder_endpoint_content');

/**
 * Get chart data for analytics
 */
function nanimade_get_chart_data($user_id) {
    $orders = wc_get_orders(array(
        'customer_id' => $user_id,
        'limit' => 12,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    $labels = array();
    $data = array();
    
    foreach (array_reverse($orders) as $order) {
        $labels[] = $order->get_date_created()->format('M Y');
        $data[] = $order->get_total();
    }
    
    return array(
        'labels' => $labels,
        'data' => $data
    );
}

/**
 * Get user's favorite products
 */
function nanimade_get_favorite_products($user_id) {
    $wishlist = get_user_meta($user_id, 'nanimade_wishlist', true);
    $favorites = array();
    
    if (is_array($wishlist) && !empty($wishlist)) {
        foreach ($wishlist as $product_id) {
            $product = wc_get_product($product_id);
            if ($product && $product->is_visible()) {
                $favorites[] = $product;
            }
        }
    }
    
    return array_slice($favorites, 0, 6); // Return max 6 favorites
}

/**
 * Add custom CSS for endpoint pages
 */
function nanimade_endpoint_styles() {
    if (is_account_page()) {
        echo '<style>
            .order-tracking-container,
            .analytics-container,
            .one-click-reorder-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }
            
            .order-tracking-item {
                background: var(--white);
                border-radius: var(--border-radius);
                padding: 25px;
                margin-bottom: 20px;
                box-shadow: var(--shadow);
            }
            
            .order-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
            }
            
            .order-meta {
                display: flex;
                gap: 15px;
                color: #666;
            }
            
            .status-badge {
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 0.9rem;
                font-weight: 600;
            }
            
            .status-processing { background: #fff3cd; color: #856404; }
            .status-completed { background: #d4edda; color: #155724; }
            .status-on-hold { background: #f8d7da; color: #721c24; }
            
            .tracking-timeline {
                margin-top: 20px;
            }
            
            .tracking-step {
                display: flex;
                align-items: flex-start;
                margin-bottom: 20px;
                opacity: 0.5;
            }
            
            .tracking-step.completed,
            .tracking-step.active {
                opacity: 1;
            }
            
            .step-icon {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: var(--primary-color);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 15px;
                flex-shrink: 0;
            }
            
            .tracking-step.pending .step-icon {
                background: #ccc;
            }
            
            .step-content h4 {
                margin: 0 0 5px 0;
                font-size: 1rem;
            }
            
            .step-content p {
                margin: 0;
                color: #666;
                font-size: 0.9rem;
            }
            
            .reorder-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .reorder-item {
                background: var(--white);
                border-radius: var(--border-radius);
                padding: 20px;
                box-shadow: var(--shadow);
                transition: var(--transition);
            }
            
            .reorder-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            }
            
            .reorder-items {
                display: flex;
                gap: 10px;
                margin: 15px 0;
                flex-wrap: wrap;
            }
            
            .reorder-item-product {
                display: flex;
                align-items: center;
                gap: 8px;
                background: var(--light-bg);
                padding: 8px;
                border-radius: 6px;
                font-size: 0.9rem;
            }
            
            .reorder-item-product img {
                width: 30px;
                height: 30px;
                border-radius: 4px;
                object-fit: cover;
            }
            
            .reorder-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #eee;
            }
            
            .bulk-reorder-section {
                background: var(--light-bg);
                padding: 25px;
                border-radius: var(--border-radius);
                text-align: center;
            }
            
            .bulk-reorder-options {
                display: flex;
                gap: 15px;
                justify-content: center;
                margin-top: 15px;
                flex-wrap: wrap;
            }
            
            .bulk-reorder-btn {
                background: var(--secondary-color);
                color: var(--white);
                border: none;
                padding: 12px 20px;
                border-radius: var(--border-radius);
                cursor: pointer;
                transition: var(--transition);
            }
            
            .bulk-reorder-btn:hover {
                background: var(--primary-color);
                transform: translateY(-2px);
            }
            
            .no-orders {
                text-align: center;
                padding: 60px 20px;
            }
            
            .no-orders-icon {
                font-size: 4rem;
                margin-bottom: 20px;
            }
            
            .charts-section {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 30px;
                margin: 30px 0;
            }
            
            .chart-controls {
                display: flex;
                gap: 10px;
                margin-bottom: 20px;
            }
            
            .chart-period-btn {
                background: var(--light-bg);
                border: none;
                padding: 8px 15px;
                border-radius: 6px;
                cursor: pointer;
                transition: var(--transition);
            }
            
            .chart-period-btn.active {
                background: var(--primary-color);
                color: var(--white);
            }
            
            .favorite-products-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }
            
            .favorite-product-item {
                background: var(--white);
                border-radius: var(--border-radius);
                padding: 15px;
                text-align: center;
                box-shadow: var(--shadow);
                transition: var(--transition);
            }
            
            .favorite-product-item:hover {
                transform: translateY(-2px);
            }
            
            .favorite-product-item img {
                width: 100%;
                height: 150px;
                object-fit: cover;
                border-radius: 6px;
                margin-bottom: 10px;
            }
            
            @media (max-width: 768px) {
                .charts-section {
                    grid-template-columns: 1fr;
                }
                
                .reorder-grid {
                    grid-template-columns: 1fr;
                }
                
                .bulk-reorder-options {
                    flex-direction: column;
                }
            }
        </style>';
    }
}
add_action('wp_head', 'nanimade_endpoint_styles');
