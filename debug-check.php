<?php
/**
 * Nanimade Debug Check Script
 * Run this script to verify all functionality is working correctly
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If not in WordPress, create a simple check
    echo "<h1>Nanimade Debug Check</h1>";
    echo "<p>This script should be run from within WordPress admin.</p>";
    echo "<p>Go to: WordPress Admin > Tools > Site Health > Info</p>";
    exit;
}

/**
 * Debug check function
 */
function nanimade_debug_check() {
    $issues = array();
    $warnings = array();
    $success = array();
    
    echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ddd;">';
    echo '<h2>Nanimade Debug Check Results</h2>';
    
    // Check WordPress version
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        $issues[] = 'WordPress version is below 5.0. Current: ' . get_bloginfo('version');
    } else {
        $success[] = 'WordPress version OK: ' . get_bloginfo('version');
    }
    
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        $issues[] = 'PHP version is below 7.4. Current: ' . PHP_VERSION;
    } else {
        $success[] = 'PHP version OK: ' . PHP_VERSION;
    }
    
    // Check required plugins
    $required_plugins = array(
        'woocommerce/woocommerce.php' => 'WooCommerce',
        'elementor/elementor.php' => 'Elementor',
        'advanced-custom-fields-pro/acf.php' => 'ACF Pro',
        'premium-addons-for-elementor/premium-addons-for-elementor.php' => 'Premium Addons Pro'
    );
    
    foreach ($required_plugins as $plugin_path => $plugin_name) {
        if (!is_plugin_active($plugin_path)) {
            $issues[] = $plugin_name . ' is not active';
        } else {
            $success[] = $plugin_name . ' is active';
        }
    }
    
    // Check theme
    $current_theme = wp_get_theme();
    if ($current_theme->get('Name') !== 'Nanimade Child Theme') {
        $issues[] = 'Nanimade Child Theme is not active. Current: ' . $current_theme->get('Name');
    } else {
        $success[] = 'Nanimade Child Theme is active';
    }
    
    // Check WooCommerce functions
    if (!function_exists('WC')) {
        $issues[] = 'WooCommerce is not loaded';
    } else {
        $success[] = 'WooCommerce is loaded';
        
        // Check WooCommerce settings
        if (!wc_get_page_id('shop')) {
            $warnings[] = 'WooCommerce shop page not set';
        }
        
        if (!wc_get_page_id('cart')) {
            $warnings[] = 'WooCommerce cart page not set';
        }
        
        if (!wc_get_page_id('checkout')) {
            $warnings[] = 'WooCommerce checkout page not set';
        }
        
        if (!wc_get_page_id('myaccount')) {
            $warnings[] = 'WooCommerce my account page not set';
        }
    }
    
    // Check custom endpoints
    $endpoints = array('order-tracking', 'analytics', 'one-click-reorder');
    foreach ($endpoints as $endpoint) {
        if (!get_option('woocommerce_myaccount_' . $endpoint . '_endpoint')) {
            $warnings[] = 'Custom endpoint "' . $endpoint . '" not registered';
        }
    }
    
    // Check file permissions
    $theme_dir = get_stylesheet_directory();
    $files_to_check = array(
        $theme_dir . '/style.css',
        $theme_dir . '/functions.php',
        $theme_dir . '/assets/js/custom.js',
        $theme_dir . '/includes/ajax-handlers.php'
    );
    
    foreach ($files_to_check as $file) {
        if (!file_exists($file)) {
            $issues[] = 'File missing: ' . basename($file);
        } elseif (!is_readable($file)) {
            $issues[] = 'File not readable: ' . basename($file);
        } else {
            $success[] = 'File OK: ' . basename($file);
        }
    }
    
    // Check AJAX endpoints
    $ajax_actions = array(
        'nanimade_get_cart',
        'nanimade_add_to_cart',
        'nanimade_one_click_reorder',
        'nanimade_track_order'
    );
    
    foreach ($ajax_actions as $action) {
        if (!has_action('wp_ajax_' . $action)) {
            $issues[] = 'AJAX action "' . $action . '" not registered';
        } else {
            $success[] = 'AJAX action OK: ' . $action;
        }
    }
    
    // Check security features
    if (function_exists('nanimade_custom_login_url')) {
        $success[] = 'Custom login URL function exists';
    } else {
        $issues[] = 'Custom login URL function missing';
    }
    
    // Check admin dashboard
    if (function_exists('nanimade_add_dashboard_widgets')) {
        $success[] = 'Admin dashboard widgets function exists';
    } else {
        $warnings[] = 'Admin dashboard widgets function missing';
    }
    
    // Display results
    if (!empty($issues)) {
        echo '<div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">';
        echo '<h3>Critical Issues:</h3>';
        echo '<ul>';
        foreach ($issues as $issue) {
            echo '<li>' . esc_html($issue) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    if (!empty($warnings)) {
        echo '<div style="background: #fff3cd; color: #856404; padding: 10px; margin: 10px 0; border: 1px solid #ffeaa7;">';
        echo '<h3>Warnings:</h3>';
        echo '<ul>';
        foreach ($warnings as $warning) {
            echo '<li>' . esc_html($warning) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    if (!empty($success)) {
        echo '<div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;">';
        echo '<h3>Success:</h3>';
        echo '<ul>';
        foreach ($success as $item) {
            echo '<li>' . esc_html($item) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    // Summary
    $total_checks = count($issues) + count($warnings) + count($success);
    $success_rate = round((count($success) / $total_checks) * 100, 1);
    
    echo '<div style="background: #e2e3e5; padding: 15px; margin: 20px 0; text-align: center;">';
    echo '<h3>Summary</h3>';
    echo '<p>Total checks: ' . $total_checks . '</p>';
    echo '<p>Success rate: ' . $success_rate . '%</p>';
    
    if (empty($issues)) {
        echo '<p style="color: #155724; font-weight: bold;">✅ All critical checks passed!</p>';
    } else {
        echo '<p style="color: #721c24; font-weight: bold;">❌ Critical issues found. Please fix them before going live.</p>';
    }
    echo '</div>';
    
    echo '</div>';
}

// Add to admin menu
function nanimade_add_debug_menu() {
    add_submenu_page(
        'tools.php',
        'Nanimade Debug',
        'Nanimade Debug',
        'manage_options',
        'nanimade-debug',
        'nanimade_debug_check'
    );
}
add_action('admin_menu', 'nanimade_add_debug_menu');

// Auto-run on theme activation
function nanimade_activation_check() {
    if (isset($_GET['activated']) && $_GET['activated'] === 'true') {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>Nanimade Theme Activated!</strong> <a href="' . admin_url('tools.php?page=nanimade-debug') . '">Run debug check</a> to verify everything is working correctly.</p>';
            echo '</div>';
        });
    }
}
add_action('admin_init', 'nanimade_activation_check');
