<?php
/**
 * Enhanced Checkout System for Nanimade Theme
 * Modern, stylish checkout with real-time tracking
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue checkout styles and scripts
 */
function nanimade_enqueue_checkout_assets() {
    if (is_checkout()) {
        // Enhanced checkout styles
        wp_enqueue_style('nanimade-checkout', get_stylesheet_directory_uri() . '/assets/css/enhanced-checkout.css', array(), '1.0.0');
        
        // Enhanced checkout JavaScript
        wp_enqueue_script('nanimade-checkout-js', get_stylesheet_directory_uri() . '/assets/js/enhanced-checkout.js', array('jquery'), '1.0.0', true);
        
        // Localize script
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
}
add_action('wp_enqueue_scripts', 'nanimade_enqueue_checkout_assets');

/**
 * Customize checkout fields
 */
function nanimade_customize_checkout_fields($fields) {
    // Add custom fields
    $fields['billing']['billing_phone']['priority'] = 15;
    $fields['billing']['billing_email']['priority'] = 20;
    
    // Add delivery instructions
    $fields['order']['order_comments']['label'] = 'Delivery Instructions';
    $fields['order']['order_comments']['placeholder'] = 'Any special delivery instructions, preferred delivery time, or notes for the delivery person...';
    $fields['order']['order_comments']['priority'] = 10;
    
    // Add pickup option
    $fields['billing']['pickup_option'] = array(
        'type' => 'checkbox',
        'label' => 'I prefer pickup from store',
        'required' => false,
        'priority' => 5,
        'class' => array('form-row-wide', 'pickup-option')
    );
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'nanimade_customize_checkout_fields');

/**
 * Add checkout progress indicator
 */
function nanimade_checkout_progress_indicator() {
    if (is_checkout()) {
        echo '<div class="checkout-progress">
            <div class="progress-step active" data-step="1">
                <div class="step-icon">🥒</div>
                <span class="step-text">Cart Review</span>
            </div>
            <div class="progress-step" data-step="2">
                <div class="step-icon">📝</div>
                <span class="step-text">Details</span>
            </div>
            <div class="progress-step" data-step="3">
                <div class="step-icon">💳</div>
                <span class="step-text">Payment</span>
            </div>
            <div class="progress-step" data-step="4">
                <div class="step-icon">✅</div>
                <span class="step-text">Complete</span>
            </div>
        </div>';
    }
}
add_action('woocommerce_before_checkout_form', 'nanimade_checkout_progress_indicator');

/**
 * Add order summary with animations
 */
function nanimade_enhanced_order_summary() {
    if (is_checkout()) {
        echo '<div class="enhanced-order-summary">
            <div class="summary-header">
                <h3>🥒 Your Pickle Order</h3>
                <div class="summary-toggle">📋</div>
            </div>
            <div class="summary-content">
                <div class="order-items"></div>
                <div class="order-totals"></div>
                <div class="delivery-estimate">
                    <div class="estimate-icon">🚚</div>
                    <div class="estimate-text">
                        <strong>Estimated Delivery:</strong>
                        <span class="delivery-date">2-3 business days</span>
                    </div>
                </div>
            </div>
        </div>';
    }
}
add_action('woocommerce_checkout_order_review', 'nanimade_enhanced_order_summary', 5);

/**
 * Add real-time order tracking widget
 */
function nanimade_order_tracking_widget() {
    if (is_checkout()) {
        echo '<div class="order-tracking-widget">
            <div class="tracking-header">
                <h4>📦 Track Your Order</h4>
                <div class="tracking-toggle">🔍</div>
            </div>
            <div class="tracking-content">
                <div class="tracking-status">
                    <div class="status-step active">
                        <div class="status-icon">📋</div>
                        <div class="status-text">
                            <strong>Order Placed</strong>
                            <span>Your order is confirmed</span>
                        </div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon">🥒</div>
                        <div class="status-text">
                            <strong>Pickling in Progress</strong>
                            <span>We\'re preparing your pickles</span>
                        </div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon">📦</div>
                        <div class="status-text">
                            <strong>Ready for Shipment</strong>
                            <span>Your order is packed</span>
                        </div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon">🚚</div>
                        <div class="status-text">
                            <strong>Out for Delivery</strong>
                            <span>On its way to you</span>
                        </div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon">✅</div>
                        <div class="status-text">
                            <strong>Delivered</strong>
                            <span>Enjoy your pickles!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
}
add_action('woocommerce_checkout_after_order_review', 'nanimade_order_tracking_widget');

/**
 * Add payment method styling
 */
function nanimade_enhance_payment_methods() {
    if (is_checkout()) {
        echo '<style>
            .wc_payment_methods {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border-radius: 15px;
                padding: 20px;
                margin: 20px 0;
            }
            .wc_payment_method {
                background: white;
                border-radius: 10px;
                padding: 15px;
                margin: 10px 0;
                border: 2px solid transparent;
                transition: all 0.3s ease;
            }
            .wc_payment_method:hover {
                border-color: var(--primary-color);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            .wc_payment_method input[type="radio"]:checked + label {
                color: var(--primary-color);
                font-weight: 600;
            }
        </style>';
    }
}
add_action('wp_head', 'nanimade_enhance_payment_methods');

/**
 * Add order confirmation enhancements
 */
function nanimade_enhance_order_confirmation($order_id) {
    $order = wc_get_order($order_id);
    if ($order) {
        echo '<div class="order-confirmation-enhanced">
            <div class="confirmation-header">
                <div class="confirmation-icon">🎉</div>
                <h2>Order Confirmed!</h2>
                <p>Thank you for choosing Nanimade Pickles</p>
            </div>
            
            <div class="order-details-enhanced">
                <div class="detail-card">
                    <div class="card-icon">📋</div>
                    <div class="card-content">
                        <h4>Order Number</h4>
                        <p>#' . $order->get_order_number() . '</p>
                    </div>
                </div>
                
                <div class="detail-card">
                    <div class="card-icon">📅</div>
                    <div class="card-content">
                        <h4>Order Date</h4>
                        <p>' . $order->get_date_created()->format('F j, Y') . '</p>
                    </div>
                </div>
                
                <div class="detail-card">
                    <div class="card-icon">💰</div>
                    <div class="card-content">
                        <h4>Total Amount</h4>
                        <p>' . $order->get_formatted_order_total() . '</p>
                    </div>
                </div>
            </div>
            
            <div class="tracking-section">
                <h3>📦 Track Your Order</h3>
                <div class="tracking-timeline">
                    <div class="timeline-step completed">
                        <div class="step-marker">✅</div>
                        <div class="step-content">
                            <h4>Order Placed</h4>
                            <p>Your order has been confirmed</p>
                            <span class="step-time">' . current_time('M j, g:i A') . '</span>
                        </div>
                    </div>
                    <div class="timeline-step active">
                        <div class="step-marker">🥒</div>
                        <div class="step-content">
                            <h4>Pickling in Progress</h4>
                            <p>We\'re preparing your fresh pickles</p>
                            <span class="step-time">Estimated: 1-2 hours</span>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-marker">📦</div>
                        <div class="step-content">
                            <h4>Ready for Shipment</h4>
                            <p>Your order will be packed and shipped</p>
                            <span class="step-time">Estimated: Today</span>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-marker">🚚</div>
                        <div class="step-content">
                            <h4>Out for Delivery</h4>
                            <p>Your pickles are on the way</p>
                            <span class="step-time">Estimated: 2-3 days</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="next-steps">
                <h3>What\'s Next?</h3>
                <div class="steps-grid">
                    <div class="step-item">
                        <div class="step-icon">📧</div>
                        <h4>Confirmation Email</h4>
                        <p>You\'ll receive an order confirmation email shortly</p>
                    </div>
                    <div class="step-item">
                        <div class="step-icon">📱</div>
                        <h4>SMS Updates</h4>
                        <p>Get real-time updates on your phone</p>
                    </div>
                    <div class="step-item">
                        <div class="step-icon">🔍</div>
                        <h4>Track Online</h4>
                        <p>Track your order in your account dashboard</p>
                    </div>
                </div>
            </div>
        </div>';
    }
}
add_action('woocommerce_thankyou', 'nanimade_enhance_order_confirmation', 10);

/**
 * Add Shiprocket tracking to order confirmation
 */
function nanimade_add_shiprocket_tracking($order_id) {
    $order = wc_get_order($order_id);
    if ($order) {
        $tracking_number = get_post_meta($order_id, '_shiprocket_tracking_number', true);
        $shipment_id = get_post_meta($order_id, '_shiprocket_shipment_id', true);
        
        if ($tracking_number) {
            echo '<div class="shiprocket-tracking">
                <div class="tracking-header">
                    <h3>🚚 Shiprocket Tracking</h3>
                    <div class="tracking-number">#' . $tracking_number . '</div>
                </div>
                <div class="tracking-actions">
                    <a href="https://shiprocket.co/tracking/' . $tracking_number . '" target="_blank" class="track-button">
                        🔍 Track Package
                    </a>
                    <button class="refresh-tracking" data-tracking="' . $tracking_number . '">
                        🔄 Refresh
                    </button>
                </div>
                <div class="tracking-details" id="tracking-details-' . $tracking_number . '">
                    <div class="loading">Loading tracking information...</div>
                </div>
            </div>';
        }
    }
}
add_action('woocommerce_thankyou', 'nanimade_add_shiprocket_tracking', 15);

/**
 * AJAX handler for real-time tracking updates
 */
function nanimade_get_real_time_tracking() {
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
add_action('wp_ajax_nanimade_get_tracking', 'nanimade_get_real_time_tracking');
add_action('wp_ajax_nopriv_nanimade_get_tracking', 'nanimade_get_real_time_tracking');

/**
 * Get Shiprocket tracking data
 */
function nanimade_get_shiprocket_tracking_data($tracking_number) {
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
 * Add checkout field validation
 */
function nanimade_checkout_field_validation() {
    if (isset($_POST['pickup_option']) && $_POST['pickup_option'] === 'on') {
        // If pickup is selected, make shipping address optional
        add_filter('woocommerce_checkout_fields', function($fields) {
            $fields['shipping']['shipping_address_1']['required'] = false;
            $fields['shipping']['shipping_city']['required'] = false;
            $fields['shipping']['shipping_postcode']['required'] = false;
            return $fields;
        });
    }
}
add_action('woocommerce_checkout_process', 'nanimade_checkout_field_validation');

/**
 * Save pickup option to order
 */
function nanimade_save_pickup_option($order_id) {
    if (isset($_POST['pickup_option']) && $_POST['pickup_option'] === 'on') {
        update_post_meta($order_id, '_pickup_option', 'yes');
        $order = wc_get_order($order_id);
        $order->add_order_note('Customer selected pickup option');
    }
}
add_action('woocommerce_checkout_update_order_meta', 'nanimade_save_pickup_option');
