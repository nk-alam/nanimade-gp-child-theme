<?php
/**
 * Security Enhancements for Nanimade Theme
 * Advanced security features and customizations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Change WordPress login URL
 */
function nanimade_custom_login_url() {
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        return;
    }
    
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false && !is_user_logged_in()) {
        wp_redirect(home_url('/secure-access/'));
        exit();
    }
}
add_action('init', 'nanimade_custom_login_url');

/**
 * Create custom login page
 */
function nanimade_custom_login_page() {
    if (strpos($_SERVER['REQUEST_URI'], 'secure-access') !== false) {
        include get_stylesheet_directory() . '/templates/custom-login.php';
        exit();
    }
}
add_action('template_redirect', 'nanimade_custom_login_page');

/**
 * Handle custom login form submission
 */
function nanimade_handle_custom_login() {
    if (isset($_POST['nanimade_login']) && wp_verify_nonce($_POST['nanimade_login_nonce'], 'nanimade_login')) {
        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);
        
        $user = wp_authenticate($username, $password);
        
        if (is_wp_error($user)) {
            wp_redirect(home_url('/secure-access/?error=invalid'));
            exit();
        } else {
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, $remember);
            wp_redirect(admin_url());
            exit();
        }
    }
}
add_action('init', 'nanimade_handle_custom_login');

/**
 * Remove WordPress version from head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Remove WordPress version from admin footer
 */
function nanimade_remove_version_footer() {
    return '';
}
add_filter('admin_footer_text', 'nanimade_remove_version_footer');

/**
 * Remove WordPress version from RSS feeds
 */
function nanimade_remove_version_rss() {
    return '';
}
add_filter('the_generator', 'nanimade_remove_version_rss');

/**
 * Hide theme information
 */
function nanimade_hide_theme_info() {
    // Remove theme name from body class
    add_filter('body_class', function($classes) {
        $classes = array_filter($classes, function($class) {
            return !preg_match('/^(theme-|generatepress|nanimade)/', $class);
        });
        return $classes;
    });
    
    // Remove theme name from HTML
    add_filter('wp_head', function() {
        ob_start(function($html) {
            $html = preg_replace('/<meta name="generator" content="[^"]*"/', '', $html);
            $html = preg_replace('/<meta name="theme-color" content="[^"]*"/', '', $html);
            return $html;
        });
    });
}
add_action('init', 'nanimade_hide_theme_info');

/**
 * Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Hide login errors
 */
function nanimade_hide_login_errors() {
    return 'Invalid credentials. Please try again.';
}
add_filter('login_errors', 'nanimade_hide_login_errors');

/**
 * Disable file editing in admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Remove unnecessary WordPress features
 */
function nanimade_remove_unnecessary_features() {
    // Remove emoji support
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    
    // Remove Windows Live Writer manifest
    remove_action('wp_head', 'wlwmanifest_link');
    
    // Remove RSD link
    remove_action('wp_head', 'rsd_link');
    
    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
    
    // Remove REST API link
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    
    // Remove adjacent posts links
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
}
add_action('init', 'nanimade_remove_unnecessary_features');

/**
 * Add security headers
 */
function nanimade_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
}
add_action('send_headers', 'nanimade_security_headers');

/**
 * Block suspicious user agents
 */
function nanimade_block_suspicious_agents() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $blocked_agents = array(
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java',
        'masscan', 'nmap', 'sqlmap', 'nikto', 'dirbuster', 'gobuster'
    );
    
    foreach ($blocked_agents as $agent) {
        if (stripos($user_agent, $agent) !== false) {
            wp_die('Access denied', 'Security', array('response' => 403));
        }
    }
}
add_action('init', 'nanimade_block_suspicious_agents');

/**
 * Limit login attempts
 */
function nanimade_limit_login_attempts() {
    if (isset($_POST['nanimade_login'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts = get_transient('login_attempts_' . $ip);
        
        if ($attempts && $attempts >= 5) {
            wp_die('Too many login attempts. Please try again later.', 'Security', array('response' => 429));
        }
        
        set_transient('login_attempts_' . $ip, ($attempts ? $attempts + 1 : 1), 300);
    }
}
add_action('init', 'nanimade_limit_login_attempts');

/**
 * Clear login attempts on successful login
 */
function nanimade_clear_login_attempts($user_login, $user) {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('login_attempts_' . $ip);
}
add_action('wp_login', 'nanimade_clear_login_attempts', 10, 2);

/**
 * Add custom admin branding
 */
function nanimade_admin_branding() {
    echo '<style>
        #wpadminbar .ab-item:before {
            content: "🥒";
        }
        #wpadminbar .ab-item {
            font-weight: bold;
        }
        .wp-admin #wpcontent {
            background: linear-gradient(135deg, #2d5a27, #8bc34a);
        }
        .wp-admin #adminmenu {
            background: #1a1a1a;
        }
        .wp-admin #adminmenu li a {
            color: #fff;
        }
        .wp-admin #adminmenu li.current a {
            background: #2d5a27;
        }
    </style>';
}
add_action('admin_head', 'nanimade_admin_branding');

/**
 * Change admin footer text
 */
function nanimade_admin_footer_text() {
    return 'Powered by Nanimade Pickles 🥒';
}
add_filter('admin_footer_text', 'nanimade_admin_footer_text');

/**
 * Add custom login page styles
 */
function nanimade_login_styles() {
    if (isset($_GET['page']) && $_GET['page'] === 'secure-access') {
        echo '<style>
            body {
                background: linear-gradient(135deg, #2d5a27, #8bc34a);
                font-family: "Inter", sans-serif;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                padding: 40px;
                width: 100%;
                max-width: 400px;
                text-align: center;
            }
            .login-logo {
                font-size: 3rem;
                margin-bottom: 20px;
            }
            .login-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #2d5a27;
                margin-bottom: 30px;
            }
            .login-form input[type="text"],
            .login-form input[type="password"] {
                width: 100%;
                padding: 15px;
                border: 2px solid #eee;
                border-radius: 8px;
                font-size: 1rem;
                margin-bottom: 15px;
                transition: border-color 0.3s ease;
            }
            .login-form input:focus {
                outline: none;
                border-color: #2d5a27;
            }
            .login-form button {
                width: 100%;
                padding: 15px;
                background: #2d5a27;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            .login-form button:hover {
                background: #8bc34a;
            }
            .login-links {
                margin-top: 20px;
            }
            .login-links a {
                color: #2d5a27;
                text-decoration: none;
                margin: 0 10px;
            }
            .login-links a:hover {
                text-decoration: underline;
            }
            .error-message {
                background: #f8d7da;
                color: #721c24;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
        </style>';
    }
}
add_action('wp_head', 'nanimade_login_styles');

/**
 * Add custom admin menu icon
 */
function nanimade_admin_menu_icon() {
    echo '<style>
        #adminmenu .dashicons-admin-generic:before {
            content: "🥒";
        }
    </style>';
}
add_action('admin_head', 'nanimade_admin_menu_icon');

/**
 * Disable theme and plugin editor
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Add custom login page template
 */
function nanimade_create_login_template() {
    $template_path = get_stylesheet_directory() . '/templates/custom-login.php';
    
    if (!file_exists($template_path)) {
        $template_content = '<?php
/**
 * Custom Login Template for Nanimade
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access - Nanimade</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-logo">🥒</div>
        <h1 class="login-title">Nanimade Admin</h1>
        
        <?php if (isset($_GET["error"])): ?>
            <div class="error-message">
                <?php echo $_GET["error"] === "invalid" ? "Invalid credentials. Please try again." : "An error occurred."; ?>
            </div>
        <?php endif; ?>
        
        <form class="login-form" method="post" action="">
            <?php wp_nonce_field("nanimade_login", "nanimade_login_nonce"); ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <label>
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <button type="submit" name="nanimade_login">Login</button>
        </form>
        
        <div class="login-links">
            <a href="<?php echo home_url(); ?>">← Back to Site</a>
        </div>
    </div>
</body>
</html>';
        
        // Create templates directory if it doesn't exist
        if (!is_dir(dirname($template_path))) {
            mkdir(dirname($template_path), 0755, true);
        }
        
        file_put_contents($template_path, $template_content);
    }
}
add_action('after_switch_theme', 'nanimade_create_login_template');

/**
 * Initialize security features
 */
function nanimade_init_security() {
    nanimade_create_login_template();
}
add_action('init', 'nanimade_init_security');
