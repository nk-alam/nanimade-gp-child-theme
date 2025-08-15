<?php
/**
 * Nanimade Child Theme Functions
 * Custom functionality for pickle selling website
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function nanimade_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style('generatepress-style', get_template_directory_uri() . '/style.css');
    
    // Child theme style
    wp_enqueue_style('nanimade-child-style', get_stylesheet_directory_uri() . '/style.css', array('generatepress-style'), '1.0.0');
    
    // Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    // Chart.js for analytics
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
    
    // Enhanced checkout styles and scripts
    if (is_checkout()) {
        wp_enqueue_style('nanimade-checkout', get_stylesheet_directory_uri() . '/assets/css/enhanced-checkout.css', array(), '1.0.0');
        wp_enqueue_script('nanimade-checkout-js', get_stylesheet_directory_uri() . '/assets/js/enhanced-checkout.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('nanimade-checkout-js', 'nanimade_checkout', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nanimade_checkout_nonce'),
            'currency' => get_woocommerce_currency_symbol(),
            'is_logged_in' => is_user_logged_in(),
            'user_email' => wp_get_current_user()->user_email ?? '',
            'user_first_name' => wp_get_current_user()->first_name ?? '',
            'user_last_name' => wp_get_current_user()->last_name ?? ''
        ));
    }
    
    // Custom JavaScript
    wp_enqueue_script('nanimade-custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('nanimade-custom-js', 'nanimade_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('nanimade_nonce'),
        'currency' => get_woocommerce_currency_symbol()
    ));
}
add_action('wp_enqueue_scripts', 'nanimade_enqueue_styles');

/**
 * Remove Downloads tab from My Account
 */
function nanimade_remove_downloads_tab($items) {
    unset($items['downloads']);
    return $items;
}
add_filter('woocommerce_account_menu_items', 'nanimade_remove_downloads_tab');

/**
 * Add custom endpoints to My Account
 */
function nanimade_add_custom_endpoints() {
    add_rewrite_endpoint('order-tracking', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('analytics', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('one-click-reorder', EP_ROOT | EP_PAGES);
}
add_action('init', 'nanimade_add_custom_endpoints');

/**
 * Add custom menu items to My Account
 */
function nanimade_add_custom_menu_items($items) {
    $new_items = array();
    
    foreach ($items as $key => $item) {
        $new_items[$key] = $item;
        
        if ($key === 'orders') {
            $new_items['order-tracking'] = __('Order Tracking', 'nanimade');
        }
    }
    
    $new_items['analytics'] = __('Analytics', 'nanimade');
    $new_items['one-click-reorder'] = __('Quick Reorder', 'nanimade');
    
    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'nanimade_add_custom_menu_items');

/**
 * Add theme support
 */
function nanimade_theme_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Add custom image sizes
    add_image_size('nanimade-product', 400, 400, true);
    add_image_size('nanimade-hero', 1200, 600, true);
}
add_action('after_setup_theme', 'nanimade_theme_setup');

/**
 * Include additional functionality files
 */
require_once get_stylesheet_directory() . '/includes/ajax-handlers.php';
require_once get_stylesheet_directory() . '/includes/endpoint-content.php';
require_once get_stylesheet_directory() . '/includes/admin-dashboard.php';
require_once get_stylesheet_directory() . '/includes/security-enhancements.php';
require_once get_stylesheet_directory() . '/includes/custom-registration.php';
require_once get_stylesheet_directory() . '/includes/enhanced-checkout.php';

// Include debug script for development
if (defined('WP_DEBUG') && WP_DEBUG) {
    require_once get_stylesheet_directory() . '/debug-check.php';
}

/**
 * Add floating cart to footer
 */
function nanimade_add_floating_cart() {
    if (is_woocommerce() || is_cart() || is_checkout()) {
        $cart_total = '';
        if (function_exists('WC') && WC()->cart) {
            $cart_total = WC()->cart->get_cart_total();
        }
        
        echo '<div id="floating-cart" class="floating-cart">';
        echo '<div class="cart-header">';
        echo '<span class="cart-title">Shopping Cart</span>';
        echo '<button class="cart-close">&times;</button>';
        echo '</div>';
        echo '<div class="cart-items"></div>';
        echo '<div class="cart-total">Total: <span class="cart-total-amount">' . $cart_total . '</span></div>';
        echo '<div class="cart-actions">';
        echo '<a href="' . wc_get_cart_url() . '" class="view-cart-btn">View Cart</a>';
        echo '<a href="' . wc_get_checkout_url() . '" class="checkout-btn">Checkout</a>';
        echo '</div>';
        echo '</div>';
    }
}
add_action('wp_footer', 'nanimade_add_floating_cart');

/**
 * Add notification system
 */
function nanimade_add_notification_system() {
    echo '<div id="notification" class="notification"></div>';
}
add_action('wp_footer', 'nanimade_add_notification_system');

/**
 * Customize WooCommerce product loop
 */
function nanimade_customize_product_loop() {
    // Add custom product card structure
    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
    
    add_action('woocommerce_before_shop_loop_item', 'nanimade_product_card_start', 10);
    add_action('woocommerce_after_shop_loop_item', 'nanimade_product_card_end', 5);
}
add_action('init', 'nanimade_customize_product_loop');

function nanimade_product_card_start() {
    echo '<div class="product-card">';
    echo '<div class="product-image">';
    echo '<a href="' . get_permalink() . '">';
}

function nanimade_product_card_end() {
    echo '</a>';
    echo '</div>';
    echo '<div class="product-content">';
    echo '<h3 class="product-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
    
    // Get product price safely
    global $product;
    if ($product) {
        echo '<div class="product-price">' . $product->get_price_html() . '</div>';
    }
    
    echo '<button class="add-to-cart-btn" data-product-id="' . get_the_ID() . '">Add to Cart</button>';
    echo '</div>';
    echo '</div>';
}

/**
 * Add custom product fields with ACF
 */
function nanimade_add_product_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_nanimade_product',
            'title' => 'Nanimade Product Fields',
            'fields' => array(
                array(
                    'key' => 'field_product_ingredients',
                    'label' => 'Ingredients',
                    'name' => 'ingredients',
                    'type' => 'textarea',
                    'instructions' => 'List the ingredients for this pickle product',
                ),
                array(
                    'key' => 'field_product_spice_level',
                    'label' => 'Spice Level',
                    'name' => 'spice_level',
                    'type' => 'select',
                    'choices' => array(
                        'mild' => 'Mild',
                        'medium' => 'Medium',
                        'hot' => 'Hot',
                        'extra_hot' => 'Extra Hot'
                    ),
                ),
                array(
                    'key' => 'field_product_shelf_life',
                    'label' => 'Shelf Life',
                    'name' => 'shelf_life',
                    'type' => 'text',
                    'instructions' => 'e.g., 6 months, 1 year',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
        ));
    }
}
add_action('acf/init', 'nanimade_add_product_fields');

/**
 * Add custom product tabs
 */
function nanimade_custom_product_tabs($tabs) {
    // Add ingredients tab
    $tabs['ingredients'] = array(
        'title' => __('Ingredients', 'nanimade'),
        'priority' => 20,
        'callback' => 'nanimade_ingredients_tab_content'
    );
    
    // Add spice level tab
    $tabs['spice_level'] = array(
        'title' => __('Spice Level', 'nanimade'),
        'priority' => 25,
        'callback' => 'nanimade_spice_level_tab_content'
    );
    
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'nanimade_custom_product_tabs');

function nanimade_ingredients_tab_content() {
    global $product;
    $ingredients = get_field('ingredients', $product->get_id());
    
    if ($ingredients) {
        echo '<h2>' . __('Ingredients', 'nanimade') . '</h2>';
        echo '<p>' . nl2br(esc_html($ingredients)) . '</p>';
    }
}

function nanimade_spice_level_tab_content() {
    global $product;
    $spice_level = get_field('spice_level', $product->get_id());
    
    if ($spice_level) {
        echo '<h2>' . __('Spice Level', 'nanimade') . '</h2>';
        echo '<div class="spice-level-indicator">';
        echo '<span class="spice-level-' . $spice_level . '">' . ucfirst($spice_level) . '</span>';
        echo '</div>';
    }
}

/**
 * Add custom checkout fields
 */
function nanimade_custom_checkout_fields($fields) {
    $fields['billing']['billing_delivery_instructions'] = array(
        'label' => __('Delivery Instructions', 'nanimade'),
        'placeholder' => __('Any special delivery instructions...', 'nanimade'),
        'required' => false,
        'class' => array('form-row-wide'),
        'clear' => true,
        'type' => 'textarea'
    );
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'nanimade_custom_checkout_fields');

/**
 * Save custom checkout fields
 */
function nanimade_save_checkout_fields($order_id) {
    if (!empty($_POST['billing_delivery_instructions'])) {
        update_post_meta($order_id, '_billing_delivery_instructions', sanitize_textarea_field($_POST['billing_delivery_instructions']));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'nanimade_save_checkout_fields');

/**
 * Add custom order status
 */
function nanimade_add_custom_order_status() {
    register_post_status('wc-pickling', array(
        'label' => _x('Pickling', 'Order status', 'nanimade'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Pickling <span class="count">(%s)</span>', 'Pickling <span class="count">(%s)</span>', 'nanimade')
    ));
}
add_action('init', 'nanimade_add_custom_order_status');

/**
 * Add custom order status to WooCommerce
 */
function nanimade_add_custom_order_status_to_wc($order_statuses) {
    $order_statuses['wc-pickling'] = _x('Pickling', 'Order status', 'nanimade');
    return $order_statuses;
}
add_filter('wc_order_statuses', 'nanimade_add_custom_order_status_to_wc');

/**
 * Custom shortcode for product showcase
 */
function nanimade_product_showcase_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'limit' => 6,
        'columns' => 3
    ), $atts);
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $atts['limit'],
        'meta_query' => array(
            array(
                'key' => '_visibility',
                'value' => array('catalog', 'visible'),
                'compare' => 'IN'
            )
        )
    );
    
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $atts['category']
            )
        );
    }
    
    $products = new WP_Query($args);
    
    ob_start();
    
    if ($products->have_posts()) {
        echo '<div class="nanimade-product-showcase" style="display: grid; grid-template-columns: repeat(' . $atts['columns'] . ', 1fr); gap: 20px;">';
        
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            
            echo '<div class="product-card">';
            echo '<div class="product-image">';
            echo '<a href="' . get_permalink() . '">';
            echo get_the_post_thumbnail(get_the_ID(), 'medium');
            echo '</a>';
            echo '</div>';
            echo '<div class="product-content">';
            echo '<h3 class="product-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            echo '<div class="product-price">' . $product->get_price_html() . '</div>';
            echo '<button class="add-to-cart-btn" data-product-id="' . get_the_ID() . '">Add to Cart</button>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('nanimade_products', 'nanimade_product_showcase_shortcode');

/**
 * Performance optimizations
 */
function nanimade_performance_optimizations() {
    // Remove unnecessary scripts
    if (!is_admin()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
    }
    
    // Add preload for critical resources
    add_action('wp_head', function() {
        echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/style.css" as="style">';
        echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">';
    });
}
add_action('init', 'nanimade_performance_optimizations');

/**
 * Security enhancements
 */
function nanimade_security_enhancements() {
    // Remove WordPress version
    remove_action('wp_head', 'wp_generator');
    
    // Disable XML-RPC
    add_filter('xmlrpc_enabled', '__return_false');
    
    // Hide login errors
    add_filter('login_errors', function() {
        return 'Something went wrong!';
    });
}
add_action('init', 'nanimade_security_enhancements');
