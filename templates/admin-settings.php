<?php
/**
 * Admin Settings Template for Nanimade Plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🥒 Nanimade Pickles Settings</h1>
    
    <div class="nanimade-admin-container">
        <div class="nanimade-admin-header">
            <div class="admin-logo">🥒</div>
            <div class="admin-title">
                <h2>Nanimade Pickles Configuration</h2>
                <p>Configure your pickle empire settings</p>
            </div>
        </div>
        
        <div class="nanimade-admin-content">
            <div class="settings-grid">
                <!-- General Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>⚙️ General Settings</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_general_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Site Branding</th>
                                    <td>
                                        <input type="text" name="nanimade_site_title" value="<?php echo esc_attr(get_option('nanimade_site_title', 'Nanimade Pickles')); ?>" class="regular-text">
                                        <p class="description">Your site title for branding</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Contact Email</th>
                                    <td>
                                        <input type="email" name="nanimade_contact_email" value="<?php echo esc_attr(get_option('nanimade_contact_email')); ?>" class="regular-text">
                                        <p class="description">Primary contact email</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Phone Number</th>
                                    <td>
                                        <input type="tel" name="nanimade_phone" value="<?php echo esc_attr(get_option('nanimade_phone')); ?>" class="regular-text">
                                        <p class="description">Business phone number</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save General Settings'); ?>
                        </form>
                    </div>
                </div>
                
                <!-- E-commerce Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>🛒 E-commerce Settings</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_ecommerce_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Currency</th>
                                    <td>
                                        <select name="nanimade_currency">
                                            <option value="INR" <?php selected(get_option('nanimade_currency'), 'INR'); ?>>Indian Rupee (₹)</option>
                                            <option value="USD" <?php selected(get_option('nanimade_currency'), 'USD'); ?>>US Dollar ($)</option>
                                            <option value="EUR" <?php selected(get_option('nanimade_currency'), 'EUR'); ?>>Euro (€)</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Free Shipping Threshold</th>
                                    <td>
                                        <input type="number" name="nanimade_free_shipping_threshold" value="<?php echo esc_attr(get_option('nanimade_free_shipping_threshold', '500')); ?>" class="regular-text">
                                        <p class="description">Minimum order amount for free shipping</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Default Pickle Category</th>
                                    <td>
                                        <?php
                                        $categories = get_terms(array(
                                            'taxonomy' => 'product_cat',
                                            'hide_empty' => false,
                                        ));
                                        ?>
                                        <select name="nanimade_default_category">
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category->term_id; ?>" <?php selected(get_option('nanimade_default_category'), $category->term_id); ?>>
                                                    <?php echo $category->name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save E-commerce Settings'); ?>
                        </form>
                    </div>
                </div>
                
                <!-- Analytics Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>📊 Analytics Settings</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_analytics_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Enable Analytics</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nanimade_analytics_enabled" value="1" <?php checked(get_option('nanimade_analytics_enabled'), '1'); ?>>
                                            Track user behavior and sales analytics
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Google Analytics ID</th>
                                    <td>
                                        <input type="text" name="nanimade_ga_id" value="<?php echo esc_attr(get_option('nanimade_ga_id')); ?>" class="regular-text" placeholder="G-XXXXXXXXXX">
                                        <p class="description">Google Analytics 4 Measurement ID</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Facebook Pixel ID</th>
                                    <td>
                                        <input type="text" name="nanimade_fb_pixel" value="<?php echo esc_attr(get_option('nanimade_fb_pixel')); ?>" class="regular-text" placeholder="XXXXXXXXXX">
                                        <p class="description">Facebook Pixel ID for tracking</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save Analytics Settings'); ?>
                        </form>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <h3>🔒 Security Settings</h3>
                    </div>
                    <div class="card-content">
                        <form method="post" action="options.php">
                            <?php settings_fields('nanimade_security_settings'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Custom Login URL</th>
                                    <td>
                                        <code><?php echo home_url('/secure-access/'); ?></code>
                                        <p class="description">Your custom login URL (default login is disabled)</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Hide WordPress Version</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nanimade_hide_version" value="1" <?php checked(get_option('nanimade_hide_version'), '1'); ?>>
                                            Remove WordPress version from headers
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Disable XML-RPC</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nanimade_disable_xmlrpc" value="1" <?php checked(get_option('nanimade_disable_xmlrpc'), '1'); ?>>
                                            Disable XML-RPC for security
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php submit_button('Save Security Settings'); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="nanimade-quick-stats">
            <h3>📈 Quick Statistics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo wc_get_total_orders(); ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo wc_price(wc_get_total_sales()); ?></div>
                        <div class="stat-label">Total Sales</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo count_users()['total_users']; ?></div>
                        <div class="stat-label">Total Customers</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🥒</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo wp_count_posts('product')->publish; ?></div>
                        <div class="stat-label">Active Products</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nanimade-admin-container {
    max-width: 1200px;
    margin: 20px 0;
}

.nanimade-admin-header {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #2d5a27, #8bc34a);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.admin-logo {
    font-size: 3rem;
    margin-right: 20px;
}

.admin-title h2 {
    margin: 0 0 10px 0;
    color: white;
}

.admin-title p {
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
    color: #2d5a27;
}

.card-content {
    padding: 20px;
}

.nanimade-quick-stats {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.nanimade-quick-stats h3 {
    margin: 0 0 20px 0;
    color: #2d5a27;
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
    border-left: 4px solid #2d5a27;
}

.stat-icon {
    font-size: 2rem;
    margin-right: 15px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d5a27;
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
    
    .nanimade-admin-header {
        flex-direction: column;
        text-align: center;
    }
    
    .admin-logo {
        margin-right: 0;
        margin-bottom: 15px;
    }
}
</style>
