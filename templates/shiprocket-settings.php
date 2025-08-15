<?php
/**
 * Shiprocket Settings Template for Nanimade Plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🚚 Shiprocket Integration Settings</h1>
    
    <div class="nanimade-shiprocket-container">
        <div class="shiprocket-header">
            <div class="shiprocket-logo">🚚</div>
            <div class="shiprocket-title">
                <h2>Shiprocket Integration</h2>
                <p>Configure automatic shipping and tracking for your pickle orders</p>
            </div>
        </div>
        
        <div class="shiprocket-content">
            <div class="settings-grid">
                <!-- API Configuration -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>🔑 API Configuration</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_shiprocket_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Enable Shiprocket</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nanimade_shiprocket_enabled" value="1" <?php checked(get_option('nanimade_shiprocket_enabled'), '1'); ?>>
                                            Enable automatic order creation and tracking
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Shiprocket Email</th>
                                    <td>
                                        <input type="email" name="nanimade_shiprocket_email" value="<?php echo esc_attr(get_option('nanimade_shiprocket_email')); ?>" class="regular-text" placeholder="your-email@domain.com">
                                        <p class="description">Your Shiprocket account email</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Shiprocket Password</th>
                                    <td>
                                        <input type="password" name="nanimade_shiprocket_password" value="<?php echo esc_attr(get_option('nanimade_shiprocket_password')); ?>" class="regular-text" placeholder="Your password">
                                        <p class="description">Your Shiprocket account password</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save API Settings'); ?>
                        </form>
                    </div>
                </div>
                
                <!-- Shipping Configuration -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>📦 Shipping Configuration</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_shipping_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Pickup Location</th>
                                    <td>
                                        <input type="text" name="nanimade_pickup_address" value="<?php echo esc_attr(get_option('nanimade_pickup_address', '123 Pickle Street, Mumbai, Maharashtra')); ?>" class="regular-text">
                                        <p class="description">Your pickup address for shipments</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Pickup Pincode</th>
                                    <td>
                                        <input type="text" name="nanimade_pickup_pincode" value="<?php echo esc_attr(get_option('nanimade_pickup_pincode', '400001')); ?>" class="regular-text">
                                        <p class="description">Your pickup location pincode</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Pickup Phone</th>
                                    <td>
                                        <input type="tel" name="nanimade_pickup_phone" value="<?php echo esc_attr(get_option('nanimade_pickup_phone', '+91 1234567890')); ?>" class="regular-text">
                                        <p class="description">Contact number for pickup location</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Default Weight (grams)</th>
                                    <td>
                                        <input type="number" name="nanimade_default_weight" value="<?php echo esc_attr(get_option('nanimade_default_weight', '500')); ?>" class="regular-text">
                                        <p class="description">Default weight per pickle jar in grams</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save Shipping Settings'); ?>
                        </form>
                    </div>
                </div>
                
                <!-- Courier Preferences -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>🚛 Courier Preferences</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_courier_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Preferred Couriers</th>
                                    <td>
                                        <label><input type="checkbox" name="nanimade_couriers[]" value="DTDC" <?php checked(in_array('DTDC', get_option('nanimade_couriers', array()))); ?>> DTDC</label><br>
                                        <label><input type="checkbox" name="nanimade_couriers[]" value="Delhivery" <?php checked(in_array('Delhivery', get_option('nanimade_couriers', array()))); ?>> Delhivery</label><br>
                                        <label><input type="checkbox" name="nanimade_couriers[]" value="BlueDart" <?php checked(in_array('BlueDart', get_option('nanimade_couriers', array()))); ?>> BlueDart</label><br>
                                        <label><input type="checkbox" name="nanimade_couriers[]" value="FedEx" <?php checked(in_array('FedEx', get_option('nanimade_couriers', array()))); ?>> FedEx</label><br>
                                        <label><input type="checkbox" name="nanimade_couriers[]" value="Ecom Express" <?php checked(in_array('Ecom Express', get_option('nanimade_couriers', array()))); ?>> Ecom Express</label>
                                        <p class="description">Select preferred courier partners</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Auto-assign Courier</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nanimade_auto_assign_courier" value="1" <?php checked(get_option('nanimade_auto_assign_courier'), '1'); ?>>
                                            Automatically assign best courier based on location
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save Courier Settings'); ?>
                        </form>
                    </div>
                </div>
                
                <!-- Tracking Configuration -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>📱 Tracking Configuration</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_tracking_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Enable SMS Tracking</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nanimade_sms_tracking" value="1" <?php checked(get_option('nanimade_sms_tracking'), '1'); ?>>
                                            Send SMS updates to customers
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Enable Email Tracking</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nanimade_email_tracking" value="1" <?php checked(get_option('nanimade_email_tracking'), '1'); ?>>
                                            Send email updates to customers
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Tracking Page URL</th>
                                    <td>
                                        <code><?php echo home_url('/my-account/track-order/'); ?></code>
                                        <p class="description">Customer tracking page URL</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save Tracking Settings'); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- API Test Section -->
        <div class="shiprocket-test-section">
            <h3>🧪 Test Shiprocket Connection</h3>
            <div class="test-actions">
                <button type="button" class="button button-primary" id="test-shiprocket-connection">
                    🔗 Test API Connection
                </button>
                <button type="button" class="button button-secondary" id="test-shiprocket-tracking">
                    📦 Test Tracking API
                </button>
                <button type="button" class="button button-secondary" id="sync-shiprocket-orders">
                    🔄 Sync Recent Orders
                </button>
            </div>
            <div id="test-results" class="test-results"></div>
        </div>
        
        <!-- Shiprocket Statistics -->
        <div class="shiprocket-stats">
            <h3>📊 Shiprocket Statistics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-content">
                        <div class="stat-number" id="total-shipments">-</div>
                        <div class="stat-label">Total Shipments</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🚚</div>
                    <div class="stat-content">
                        <div class="stat-number" id="active-shipments">-</div>
                        <div class="stat-label">Active Shipments</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-content">
                        <div class="stat-number" id="delivered-shipments">-</div>
                        <div class="stat-label">Delivered</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <div class="stat-number" id="total-shipping-cost">-</div>
                        <div class="stat-label">Total Shipping Cost</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nanimade-shiprocket-container {
    max-width: 1200px;
    margin: 20px 0;
}

.shiprocket-header {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.shiprocket-logo {
    font-size: 3rem;
    margin-right: 20px;
}

.shiprocket-title h2 {
    margin: 0 0 10px 0;
    color: white;
}

.shiprocket-title p {
    margin: 0;
    opacity: 0.9;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.settings-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.card-header h3 {
    margin: 0;
    color: #1976d2;
}

.card-content {
    padding: 20px;
}

.shiprocket-test-section {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.shiprocket-test-section h3 {
    margin: 0 0 20px 0;
    color: #1976d2;
}

.test-actions {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.test-results {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    min-height: 100px;
    border-left: 4px solid #1976d2;
}

.test-results.success {
    border-left-color: #4caf50;
    background: #e8f5e8;
}

.test-results.error {
    border-left-color: #f44336;
    background: #ffebee;
}

.shiprocket-stats {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.shiprocket-stats h3 {
    margin: 0 0 20px 0;
    color: #1976d2;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-card {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid #1976d2;
}

.stat-icon {
    font-size: 2rem;
    margin-right: 15px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1976d2;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .shiprocket-header {
        flex-direction: column;
        text-align: center;
    }
    
    .shiprocket-logo {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .test-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Test Shiprocket API Connection
    $('#test-shiprocket-connection').on('click', function() {
        const button = $(this);
        const results = $('#test-results');
        
        button.prop('disabled', true).text('Testing...');
        results.html('<div class="loading">Testing API connection...</div>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_test_shiprocket_connection',
                nonce: '<?php echo wp_create_nonce("nanimade_test_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    results.html('<div class="success">✅ API connection successful! Token: ' + response.data.token.substring(0, 20) + '...</div>');
                    results.addClass('success');
                } else {
                    results.html('<div class="error">❌ API connection failed: ' + response.data + '</div>');
                    results.addClass('error');
                }
            },
            error: function() {
                results.html('<div class="error">❌ Network error occurred</div>');
                results.addClass('error');
            },
            complete: function() {
                button.prop('disabled', false).text('🔗 Test API Connection');
            }
        });
    });
    
    // Test Tracking API
    $('#test-shiprocket-tracking').on('click', function() {
        const button = $(this);
        const results = $('#test-results');
        
        button.prop('disabled', true).text('Testing...');
        results.html('<div class="loading">Testing tracking API...</div>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_test_shiprocket_tracking',
                nonce: '<?php echo wp_create_nonce("nanimade_test_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    results.html('<div class="success">✅ Tracking API working! Test tracking number: ' + response.data.tracking_number + '</div>');
                    results.addClass('success');
                } else {
                    results.html('<div class="error">❌ Tracking API failed: ' + response.data + '</div>');
                    results.addClass('error');
                }
            },
            error: function() {
                results.html('<div class="error">❌ Network error occurred</div>');
                results.addClass('error');
            },
            complete: function() {
                button.prop('disabled', false).text('📦 Test Tracking API');
            }
        });
    });
    
    // Sync Recent Orders
    $('#sync-shiprocket-orders').on('click', function() {
        const button = $(this);
        const results = $('#test-results');
        
        button.prop('disabled', true).text('Syncing...');
        results.html('<div class="loading">Syncing recent orders with Shiprocket...</div>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_sync_shiprocket_orders',
                nonce: '<?php echo wp_create_nonce("nanimade_test_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    results.html('<div class="success">✅ Sync completed! ' + response.data.count + ' orders synced.</div>');
                    results.addClass('success');
                    updateShiprocketStats();
                } else {
                    results.html('<div class="error">❌ Sync failed: ' + response.data + '</div>');
                    results.addClass('error');
                }
            },
            error: function() {
                results.html('<div class="error">❌ Network error occurred</div>');
                results.addClass('error');
            },
            complete: function() {
                button.prop('disabled', false).text('🔄 Sync Recent Orders');
            }
        });
    });
    
    // Update Shiprocket Statistics
    function updateShiprocketStats() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_get_shiprocket_stats',
                nonce: '<?php echo wp_create_nonce("nanimade_test_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#total-shipments').text(response.data.total_shipments || '0');
                    $('#active-shipments').text(response.data.active_shipments || '0');
                    $('#delivered-shipments').text(response.data.delivered_shipments || '0');
                    $('#total-shipping-cost').text(response.data.total_cost || '₹0');
                }
            }
        });
    }
    
    // Load stats on page load
    updateShiprocketStats();
});
</script>
