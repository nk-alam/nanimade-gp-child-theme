<?php
/**
 * Plugin Name: Nanimade Pickles - Advanced E-commerce Features
 * Plugin URI: https://nanimade.com
 * Description: Advanced e-commerce features for Nanimade Pickles website including Shiprocket integration, advanced analytics, and custom functionality.
 * Version: 1.0.0
 * Author: Nanimade
 * Author URI: https://nanimade.com
 * Text Domain: nanimade-pickles
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('NANIMADE_PLUGIN_VERSION', '1.0.0');
define('NANIMADE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NANIMADE_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main Nanimade Pickles Plugin Class
 */
class NanimadePicklesPlugin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('nanimade-pickles', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize features
        $this->init_shiprocket_integration();
        $this->init_advanced_analytics();
        $this->init_custom_features();
    }
    
    /**
     * Initialize Shiprocket integration
     */
    private function init_shiprocket_integration() {
        // Add Shiprocket settings
        add_action('admin_init', array($this, 'shiprocket_settings'));
        
        // Add order tracking
        add_action('woocommerce_order_status_changed', array($this, 'create_shiprocket_order'), 10, 4);
        
        // Add tracking to order emails
        add_action('woocommerce_email_order_details', array($this, 'add_tracking_to_email'), 10, 4);
        
        // Register Shiprocket settings
        add_action('admin_init', array($this, 'register_shiprocket_settings'));
    }
    
    /**
     * Register Shiprocket settings
     */
    public function register_shiprocket_settings() {
        register_setting('nanimade_shiprocket_settings', 'nanimade_shiprocket_enabled');
        register_setting('nanimade_shiprocket_settings', 'nanimade_shiprocket_email');
        register_setting('nanimade_shiprocket_settings', 'nanimade_shiprocket_password');
        register_setting('nanimade_shipping_settings', 'nanimade_pickup_address');
        register_setting('nanimade_shipping_settings', 'nanimade_pickup_pincode');
        register_setting('nanimade_shipping_settings', 'nanimade_pickup_phone');
        register_setting('nanimade_shipping_settings', 'nanimade_default_weight');
        register_setting('nanimade_courier_settings', 'nanimade_couriers');
        register_setting('nanimade_courier_settings', 'nanimade_auto_assign_courier');
        register_setting('nanimade_tracking_settings', 'nanimade_sms_tracking');
        register_setting('nanimade_tracking_settings', 'nanimade_email_tracking');
    }
    
    /**
     * Initialize advanced analytics
     */
    private function init_advanced_analytics() {
        // Add analytics tracking
        add_action('wp_footer', array($this, 'add_analytics_tracking'));
        
        // Add conversion tracking
        add_action('woocommerce_thankyou', array($this, 'track_conversion'));
    }
    
    /**
     * Initialize custom features
     */
    private function init_custom_features() {
        // Add custom product fields
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_custom_product_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_product_fields'));
        
        // Add custom order status
        add_action('init', array($this, 'register_custom_order_status'));
        add_filter('wc_order_statuses', array($this, 'add_custom_order_status'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create necessary database tables
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up if needed
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Analytics table
        $analytics_table = $wpdb->prefix . 'nanimade_analytics';
        $sql = "CREATE TABLE $analytics_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data longtext,
            user_id bigint(20),
            session_id varchar(100),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        add_option('nanimade_shiprocket_enabled', 'no');
        add_option('nanimade_shiprocket_email', '');
        add_option('nanimade_shiprocket_password', '');
        add_option('nanimade_analytics_enabled', 'yes');
        add_option('nanimade_custom_branding', 'yes');
    }
    
    /**
     * Admin menu
     */
    public function admin_menu() {
        add_menu_page(
            'Nanimade Pickles',
            'Nanimade',
            'manage_options',
            'nanimade-settings',
            array($this, 'admin_page'),
            'dashicons-admin-generic',
            56
        );
        
        add_submenu_page(
            'nanimade-settings',
            'Settings',
            'Settings',
            'manage_options',
            'nanimade-settings',
            array($this, 'admin_page')
        );
        
        add_submenu_page(
            'nanimade-settings',
            'Shiprocket',
            'Shiprocket',
            'manage_options',
            'nanimade-shiprocket',
            array($this, 'shiprocket_page')
        );
        
        add_submenu_page(
            'nanimade-settings',
            'Analytics',
            'Analytics',
            'manage_options',
            'nanimade-analytics',
            array($this, 'analytics_page')
        );
    }
    
    /**
     * Admin scripts
     */
    public function admin_scripts($hook) {
        if (strpos($hook, 'nanimade') !== false) {
            wp_enqueue_script('nanimade-admin', NANIMADE_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), NANIMADE_PLUGIN_VERSION, true);
            wp_enqueue_style('nanimade-admin', NANIMADE_PLUGIN_URL . 'assets/css/admin.css', array(), NANIMADE_PLUGIN_VERSION);
        }
    }
    
    /**
     * Frontend scripts
     */
    public function frontend_scripts() {
        wp_enqueue_script('nanimade-frontend', NANIMADE_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), NANIMADE_PLUGIN_VERSION, true);
        wp_enqueue_style('nanimade-frontend', NANIMADE_PLUGIN_URL . 'assets/css/frontend.css', array(), NANIMADE_PLUGIN_VERSION);
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        include NANIMADE_PLUGIN_PATH . 'templates/admin-settings.php';
    }
    
    /**
     * Shiprocket page
     */
    public function shiprocket_page() {
        include NANIMADE_PLUGIN_PATH . 'templates/shiprocket-settings.php';
    }
    
    /**
     * Analytics page
     */
    public function analytics_page() {
        include NANIMADE_PLUGIN_PATH . 'templates/analytics-dashboard.php';
    }
    
    /**
     * Shiprocket settings
     */
    public function shiprocket_settings() {
        register_setting('nanimade_shiprocket', 'nanimade_shiprocket_enabled');
        register_setting('nanimade_shiprocket', 'nanimade_shiprocket_email');
        register_setting('nanimade_shiprocket', 'nanimade_shiprocket_password');
    }
    
    /**
     * Create Shiprocket order
     */
    public function create_shiprocket_order($order_id, $old_status, $new_status, $order) {
        if ($new_status === 'processing' && get_option('nanimade_shiprocket_enabled') === 'yes') {
            $this->create_shipment($order);
        }
    }
    
    /**
     * Create shipment in Shiprocket
     */
    private function create_shipment($order) {
        $email = get_option('nanimade_shiprocket_email');
        $password = get_option('nanimade_shiprocket_password');
        
        if (empty($email) || empty($password)) {
            return false;
        }
        
        // Get order data
        $order_data = array(
            'order_id' => $order->get_id(),
            'order_date' => $order->get_date_created()->format('Y-m-d'),
            'pickup_location' => 'Primary',
            'delivery_location' => $order->get_shipping_city(),
            'order_type' => 'cod',
            'cod' => $order->get_total(),
            'weight' => $this->calculate_order_weight($order),
            'length' => 10,
            'breadth' => 10,
            'height' => 10,
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'customer_email' => $order->get_billing_email(),
            'customer_phone' => $order->get_billing_phone(),
            'customer_address' => $order->get_billing_address_1(),
            'customer_city' => $order->get_billing_city(),
            'customer_pincode' => $order->get_billing_postcode(),
            'customer_state' => $order->get_billing_state(),
            'customer_country' => $order->get_billing_country()
        );
        
        // Make API call to Shiprocket
        $response = wp_remote_post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->get_shiprocket_token()
            ),
            'body' => json_encode($order_data)
        ));
        
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['shipment_id'])) {
                update_post_meta($order->get_id(), '_shiprocket_shipment_id', $body['shipment_id']);
                update_post_meta($order->get_id(), '_shiprocket_tracking_number', $body['tracking_number']);
            }
        }
    }
    
    /**
     * Get Shiprocket token
     */
    private function get_shiprocket_token() {
        $token = get_transient('shiprocket_token');
        
        if (!$token) {
            $email = get_option('nanimade_shiprocket_email');
            $password = get_option('nanimade_shiprocket_password');
            
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
                    set_transient('shiprocket_token', $token, 3600); // 1 hour
                }
            }
        }
        
        return $token;
    }
    
    /**
     * Calculate order weight
     */
    private function calculate_order_weight($order) {
        $weight = 0;
        
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product) {
                $product_weight = $product->get_weight();
                if ($product_weight) {
                    $weight += $product_weight * $item->get_quantity();
                }
            }
        }
        
        return $weight ?: 0.5; // Default weight if not set
    }
    
    /**
     * Add tracking to email
     */
    public function add_tracking_to_email($order, $sent_to_admin, $plain_text, $email) {
        $tracking_number = get_post_meta($order->get_id(), '_shiprocket_tracking_number', true);
        
        if ($tracking_number) {
            echo '<p><strong>Tracking Number:</strong> ' . $tracking_number . '</p>';
            echo '<p><strong>Track Your Order:</strong> <a href="https://shiprocket.co/tracking/' . $tracking_number . '">Click Here</a></p>';
        }
    }
    
    /**
     * Add analytics tracking
     */
    public function add_analytics_tracking() {
        if (get_option('nanimade_analytics_enabled') === 'yes') {
            echo '<script>
                // Custom analytics tracking
                window.nanimadeAnalytics = {
                    track: function(event, data) {
                        jQuery.post("' . admin_url('admin-ajax.php') . '", {
                            action: "nanimade_track_event",
                            event: event,
                            data: data,
                            nonce: "' . wp_create_nonce('nanimade_analytics') . '"
                        });
                    }
                };
            </script>';
        }
    }
    
    /**
     * Track conversion
     */
    public function track_conversion($order_id) {
        if (get_option('nanimade_analytics_enabled') === 'yes') {
            $order = wc_get_order($order_id);
            if ($order) {
                $this->track_event('purchase', array(
                    'order_id' => $order_id,
                    'total' => $order->get_total(),
                    'currency' => $order->get_currency()
                ));
            }
        }
    }
    
    /**
     * Track event
     */
    private function track_event($event, $data = array()) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'nanimade_analytics',
            array(
                'event_type' => $event,
                'event_data' => json_encode($data),
                'user_id' => get_current_user_id(),
                'session_id' => session_id()
            )
        );
    }
    
    /**
     * Add custom product fields
     */
    public function add_custom_product_fields() {
        echo '<div class="options_group">';
        
        woocommerce_wp_textarea_input(array(
            'id' => '_pickle_ingredients',
            'label' => 'Ingredients',
            'placeholder' => 'List the ingredients for this pickle...',
            'desc_tip' => true,
            'description' => 'Enter the ingredients list for this pickle product.'
        ));
        
        woocommerce_wp_select(array(
            'id' => '_pickle_spice_level',
            'label' => 'Spice Level',
            'options' => array(
                '' => 'Select Spice Level',
                'mild' => 'Mild',
                'medium' => 'Medium',
                'hot' => 'Hot',
                'extra_hot' => 'Extra Hot'
            )
        ));
        
        woocommerce_wp_text_input(array(
            'id' => '_pickle_shelf_life',
            'label' => 'Shelf Life',
            'placeholder' => 'e.g., 6 months, 1 year',
            'desc_tip' => true,
            'description' => 'Enter the shelf life of this pickle product.'
        ));
        
        echo '</div>';
    }
    
    /**
     * Save custom product fields
     */
    public function save_custom_product_fields($post_id) {
        $ingredients = $_POST['_pickle_ingredients'] ?? '';
        $spice_level = $_POST['_pickle_spice_level'] ?? '';
        $shelf_life = $_POST['_pickle_shelf_life'] ?? '';
        
        update_post_meta($post_id, '_pickle_ingredients', sanitize_textarea_field($ingredients));
        update_post_meta($post_id, '_pickle_spice_level', sanitize_text_field($spice_level));
        update_post_meta($post_id, '_pickle_shelf_life', sanitize_text_field($shelf_life));
    }
    
    /**
     * Register custom order status
     */
    public function register_custom_order_status() {
        register_post_status('wc-pickling', array(
            'label' => _x('Pickling', 'Order status', 'nanimade-pickles'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Pickling <span class="count">(%s)</span>', 'Pickling <span class="count">(%s)</span>', 'nanimade-pickles')
        ));
    }
    
    /**
     * Add custom order status
     */
    public function add_custom_order_status($order_statuses) {
        $order_statuses['wc-pickling'] = _x('Pickling', 'Order status', 'nanimade-pickles');
        return $order_statuses;
    }
}

// Initialize plugin
new NanimadePicklesPlugin();

/**
 * AJAX handler for analytics tracking
 */
function nanimade_track_event_ajax() {
    check_ajax_referer('nanimade_analytics', 'nonce');
    
    $event = sanitize_text_field($_POST['event']);
    $data = $_POST['data'] ?? array();
    
    global $wpdb;
    
    $wpdb->insert(
        $wpdb->prefix . 'nanimade_analytics',
        array(
            'event_type' => $event,
            'event_data' => json_encode($data),
            'user_id' => get_current_user_id(),
            'session_id' => session_id()
        )
    );
    
    wp_send_json_success();
}
add_action('wp_ajax_nanimade_track_event', 'nanimade_track_event_ajax');
add_action('wp_ajax_nopriv_nanimade_track_event', 'nanimade_track_event_ajax');
