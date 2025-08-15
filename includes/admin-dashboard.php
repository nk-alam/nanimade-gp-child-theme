<?php
/**
 * Admin Dashboard Customization for Nanimade Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize admin dashboard
 */
function nanimade_customize_admin_dashboard() {
    if (current_user_can('manage_woocommerce')) {
        add_action('wp_dashboard_setup', 'nanimade_add_dashboard_widgets');
        add_action('admin_enqueue_scripts', 'nanimade_admin_scripts');
    }
}
add_action('admin_init', 'nanimade_customize_admin_dashboard');

/**
 * Add dashboard widgets
 */
function nanimade_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'nanimade_sales_widget',
        'Nanimade Sales Analytics',
        'nanimade_sales_widget_content'
    );
    
    wp_add_dashboard_widget(
        'nanimade_hot_products_widget',
        'Hot Selling Products',
        'nanimade_hot_products_widget_content'
    );
    
    wp_add_dashboard_widget(
        'nanimade_recent_orders_widget',
        'Recent Orders',
        'nanimade_recent_orders_widget_content'
    );
    
    wp_add_dashboard_widget(
        'nanimade_quick_actions_widget',
        'Quick Actions',
        'nanimade_quick_actions_widget_content'
    );
}

/**
 * Sales analytics widget content
 */
function nanimade_sales_widget_content() {
    if (!function_exists('WC')) {
        echo '<p>WooCommerce is not active.</p>';
        return;
    }
    
    // Get sales data
    $total_sales = wc_price(wc_get_total_sales());
    $total_orders = wc_get_total_orders();
    $today_sales = nanimade_get_today_sales();
    $month_sales = nanimade_get_month_sales();
    
    echo '<div class="nanimade-dashboard-widget">';
    
    // Statistics Cards
    echo '<div class="stats-row">';
    echo '<div class="stat">';
    echo '<h3>Total Sales</h3>';
    echo '<p class="stat-number">' . $total_sales . '</p>';
    echo '</div>';
    echo '<div class="stat">';
    echo '<h3>Total Orders</h3>';
    echo '<p class="stat-number">' . $total_orders . '</p>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="stats-row">';
    echo '<div class="stat">';
    echo '<h3>Today\'s Sales</h3>';
    echo '<p class="stat-number">' . wc_price($today_sales) . '</p>';
    echo '</div>';
    echo '<div class="stat">';
    echo '<h3>This Month</h3>';
    echo '<p class="stat-number">' . wc_price($month_sales) . '</p>';
    echo '</div>';
    echo '</div>';
    
    // Sales Chart
    echo '<div class="sales-chart-container">';
    echo '<h3>Sales Trend (Last 7 Days)</h3>';
    echo '<canvas id="salesChart" width="400" height="200"></canvas>';
    echo '</div>';
    
    echo '</div>';
    
    // Add chart data
    $chart_data = nanimade_get_sales_chart_data();
    wp_localize_script('nanimade-admin-js', 'sales_chart_data', $chart_data);
}

/**
 * Hot selling products widget content
 */
function nanimade_hot_products_widget_content() {
    $hot_products = nanimade_get_hot_selling_products();
    
    echo '<div class="nanimade-dashboard-widget">';
    echo '<h3>Top Selling Products</h3>';
    
    if (!empty($hot_products)) {
        echo '<div class="hot-products-list">';
        foreach ($hot_products as $product) {
            $product_id = $product->get_id();
            $total_sales = $product->get_total_sales();
            $revenue = $total_sales * $product->get_price();
            
            echo '<div class="hot-product-item">';
            echo '<div class="product-image">';
            echo get_the_post_thumbnail($product_id, 'thumbnail');
            echo '</div>';
            echo '<div class="product-info">';
            echo '<h4>' . $product->get_name() . '</h4>';
            echo '<p class="product-sales">' . $total_sales . ' units sold</p>';
            echo '<p class="product-revenue">' . wc_price($revenue) . ' revenue</p>';
            echo '</div>';
            echo '<div class="product-actions">';
            echo '<a href="' . get_edit_post_link($product_id) . '" class="button button-small">Edit</a>';
            echo '<a href="' . get_permalink($product_id) . '" class="button button-small" target="_blank">View</a>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No sales data available yet.</p>';
    }
    
    echo '</div>';
}

/**
 * Recent orders widget content
 */
function nanimade_recent_orders_widget_content() {
    $recent_orders = wc_get_orders(array('limit' => 5));
    
    echo '<div class="nanimade-dashboard-widget">';
    echo '<h3>Recent Orders</h3>';
    
    if (!empty($recent_orders)) {
        echo '<div class="recent-orders-list">';
        foreach ($recent_orders as $order) {
            $order_id = $order->get_id();
            $order_status = $order->get_status();
            $order_total = $order->get_total();
            $order_date = $order->get_date_created()->format('M d, Y H:i');
            $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
            
            echo '<div class="recent-order-item">';
            echo '<div class="order-info">';
            echo '<h4>Order #' . $order_id . '</h4>';
            echo '<p class="customer-name">' . $customer_name . '</p>';
            echo '<p class="order-date">' . $order_date . '</p>';
            echo '</div>';
            echo '<div class="order-details">';
            echo '<span class="order-total">' . wc_price($order_total) . '</span>';
            echo '<span class="order-status status-' . $order_status . '">' . wc_get_order_status_name($order_status) . '</span>';
            echo '</div>';
            echo '<div class="order-actions">';
            echo '<a href="' . get_edit_post_link($order_id) . '" class="button button-small">View</a>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<div class="widget-footer">';
        echo '<a href="' . admin_url('edit.php?post_type=shop_order') . '" class="button">View All Orders</a>';
        echo '</div>';
    } else {
        echo '<p>No recent orders found.</p>';
    }
    
    echo '</div>';
}

/**
 * Quick actions widget content
 */
function nanimade_quick_actions_widget_content() {
    echo '<div class="nanimade-dashboard-widget">';
    echo '<h3>Quick Actions</h3>';
    
    echo '<div class="quick-actions-grid">';
    
    echo '<a href="' . admin_url('post-new.php?post_type=product') . '" class="quick-action-item">';
    echo '<div class="action-icon">➕</div>';
    echo '<span>Add New Product</span>';
    echo '</a>';
    
    echo '<a href="' . admin_url('edit.php?post_type=product') . '" class="quick-action-item">';
    echo '<div class="action-icon">📦</div>';
    echo '<span>Manage Products</span>';
    echo '</a>';
    
    echo '<a href="' . admin_url('edit.php?post_type=shop_order') . '" class="quick-action-item">';
    echo '<div class="action-icon">📋</div>';
    echo '<span>View Orders</span>';
    echo '</a>';
    
    echo '<a href="' . admin_url('admin.php?page=wc-reports') . '" class="quick-action-item">';
    echo '<div class="action-icon">📊</div>';
    echo '<span>Sales Reports</span>';
    echo '</a>';
    
    echo '<a href="' . admin_url('admin.php?page=wc-settings') . '" class="quick-action-item">';
    echo '<div class="action-icon">⚙️</div>';
    echo '<span>Store Settings</span>';
    echo '</a>';
    
    echo '<a href="' . home_url() . '" class="quick-action-item" target="_blank">';
    echo '<div class="action-icon">🏠</div>';
    echo '<span>Visit Store</span>';
    echo '</a>';
    
    echo '</div>';
    echo '</div>';
}

/**
 * Get today's sales
 */
function nanimade_get_today_sales() {
    $today = current_time('Y-m-d');
    $orders = wc_get_orders(array(
        'date_created' => $today,
        'status' => array('completed', 'processing'),
        'limit' => -1
    ));
    
    $total = 0;
    foreach ($orders as $order) {
        $total += $order->get_total();
    }
    
    return $total;
}

/**
 * Get this month's sales
 */
function nanimade_get_month_sales() {
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
    
    $orders = wc_get_orders(array(
        'date_created' => $start_date . '...' . $end_date,
        'status' => array('completed', 'processing'),
        'limit' => -1
    ));
    
    $total = 0;
    foreach ($orders as $order) {
        $total += $order->get_total();
    }
    
    return $total;
}

/**
 * Get hot selling products
 */
function nanimade_get_hot_selling_products($limit = 5) {
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    );
    
    $products = get_posts($args);
    $hot_products = array();
    
    foreach ($products as $product) {
        $wc_product = wc_get_product($product->ID);
        if ($wc_product && $wc_product->get_total_sales() > 0) {
            $hot_products[] = $wc_product;
        }
    }
    
    return $hot_products;
}

/**
 * Get sales chart data
 */
function nanimade_get_sales_chart_data() {
    $labels = array();
    $data = array();
    
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $day_name = date('D', strtotime($date));
        
        $orders = wc_get_orders(array(
            'date_created' => $date,
            'status' => array('completed', 'processing'),
            'limit' => -1
        ));
        
        $day_total = 0;
        foreach ($orders as $order) {
            $day_total += $order->get_total();
        }
        
        $labels[] = $day_name;
        $data[] = $day_total;
    }
    
    return array(
        'labels' => $labels,
        'data' => $data
    );
}

/**
 * Enqueue admin scripts
 */
function nanimade_admin_scripts($hook) {
    if ($hook === 'index.php') {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
        wp_enqueue_script('nanimade-admin-js', get_stylesheet_directory_uri() . '/assets/js/admin.js', array('jquery', 'chartjs'), '1.0.0', true);
        
        // Localize admin script
        wp_localize_script('nanimade-admin-js', 'nanimade_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nanimade_admin_nonce')
        ));
    }
}

/**
 * Add custom admin styles
 */
function nanimade_admin_styles() {
    if (get_current_screen()->id === 'dashboard') {
        echo '<style>
            .nanimade-dashboard-widget {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                margin-bottom: 20px;
            }
            
            .nanimade-dashboard-widget h3 {
                margin: 0 0 20px 0;
                color: #333;
                font-size: 1.2rem;
                border-bottom: 2px solid #2d5a27;
                padding-bottom: 10px;
            }
            
            .stats-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }
            
            .stat {
                text-align: center;
                padding: 15px;
                background: #f9f9f9;
                border-radius: 6px;
                border-left: 4px solid #2d5a27;
            }
            
            .stat h3 {
                margin: 0 0 10px 0;
                color: #666;
                font-size: 0.9rem;
                text-transform: uppercase;
                border: none;
                padding: 0;
            }
            
            .stat-number {
                margin: 0;
                font-size: 1.5rem;
                font-weight: bold;
                color: #2d5a27;
            }
            
            .sales-chart-container {
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #eee;
            }
            
            .hot-products-list {
                max-height: 400px;
                overflow-y: auto;
            }
            
            .hot-product-item {
                display: flex;
                align-items: center;
                padding: 15px;
                border-bottom: 1px solid #eee;
                transition: background-color 0.3s ease;
            }
            
            .hot-product-item:hover {
                background-color: #f9f9f9;
            }
            
            .hot-product-item:last-child {
                border-bottom: none;
            }
            
            .product-image {
                width: 50px;
                height: 50px;
                margin-right: 15px;
                border-radius: 4px;
                overflow: hidden;
            }
            
            .product-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .product-info {
                flex: 1;
            }
            
            .product-info h4 {
                margin: 0 0 5px 0;
                font-size: 1rem;
                color: #333;
            }
            
            .product-sales,
            .product-revenue {
                margin: 0;
                font-size: 0.9rem;
                color: #666;
            }
            
            .product-actions {
                display: flex;
                gap: 5px;
            }
            
            .recent-orders-list {
                max-height: 400px;
                overflow-y: auto;
            }
            
            .recent-order-item {
                display: flex;
                align-items: center;
                padding: 15px;
                border-bottom: 1px solid #eee;
                transition: background-color 0.3s ease;
            }
            
            .recent-order-item:hover {
                background-color: #f9f9f9;
            }
            
            .recent-order-item:last-child {
                border-bottom: none;
            }
            
            .order-info {
                flex: 1;
            }
            
            .order-info h4 {
                margin: 0 0 5px 0;
                font-size: 1rem;
                color: #333;
            }
            
            .customer-name,
            .order-date {
                margin: 0;
                font-size: 0.9rem;
                color: #666;
            }
            
            .order-details {
                text-align: right;
                margin-right: 15px;
            }
            
            .order-total {
                display: block;
                font-weight: bold;
                color: #2d5a27;
                margin-bottom: 5px;
            }
            
            .order-status {
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            
            .status-completed { background: #d4edda; color: #155724; }
            .status-processing { background: #fff3cd; color: #856404; }
            .status-on-hold { background: #f8d7da; color: #721c24; }
            
            .widget-footer {
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #eee;
                text-align: center;
            }
            
            .quick-actions-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .quick-action-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 6px;
                text-decoration: none;
                color: #333;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }
            
            .quick-action-item:hover {
                background: #2d5a27;
                color: white;
                transform: translateY(-2px);
                border-color: #2d5a27;
            }
            
            .action-icon {
                font-size: 2rem;
                margin-bottom: 10px;
            }
            
            .quick-action-item span {
                font-weight: 600;
                text-align: center;
            }
            
            @media (max-width: 1200px) {
                .stats-row {
                    grid-template-columns: 1fr;
                }
                
                .quick-actions-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>';
    }
}
add_action('admin_head', 'nanimade_admin_styles');

/**
 * Add custom admin menu
 */
function nanimade_add_admin_menu() {
    add_menu_page(
        'Nanimade Analytics',
        'Nanimade',
        'manage_woocommerce',
        'nanimade-analytics',
        'nanimade_analytics_page',
        'dashicons-chart-area',
        56
    );
    
    add_submenu_page(
        'nanimade-analytics',
        'Sales Analytics',
        'Sales Analytics',
        'manage_woocommerce',
        'nanimade-analytics',
        'nanimade_analytics_page'
    );
    
    add_submenu_page(
        'nanimade-analytics',
        'Product Performance',
        'Product Performance',
        'manage_woocommerce',
        'nanimade-products',
        'nanimade_products_page'
    );
    
    add_submenu_page(
        'nanimade-analytics',
        'Customer Insights',
        'Customer Insights',
        'manage_woocommerce',
        'nanimade-customers',
        'nanimade_customers_page'
    );
}
add_action('admin_menu', 'nanimade_add_admin_menu');

/**
 * Analytics page content
 */
function nanimade_analytics_page() {
    echo '<div class="wrap">';
    echo '<h1>Nanimade Sales Analytics</h1>';
    
    // Add comprehensive analytics content here
    echo '<div class="analytics-dashboard">';
    echo '<p>Comprehensive sales analytics and insights for your pickle business.</p>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Products page content
 */
function nanimade_products_page() {
    echo '<div class="wrap">';
    echo '<h1>Product Performance</h1>';
    
    // Add product performance content here
    echo '<div class="products-dashboard">';
    echo '<p>Track the performance of your pickle products.</p>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Customers page content
 */
function nanimade_customers_page() {
    echo '<div class="wrap">';
    echo '<h1>Customer Insights</h1>';
    
    // Add customer insights content here
    echo '<div class="customers-dashboard">';
    echo '<p>Understand your customers and their preferences.</p>';
    echo '</div>';
    
    echo '</div>';
}
