<?php
/**
 * Custom Registration System for Nanimade Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create custom registration page
 */
function nanimade_custom_registration_page() {
    if (strpos($_SERVER['REQUEST_URI'], 'register') !== false) {
        include get_stylesheet_directory() . '/templates/custom-registration.php';
        exit();
    }
}
add_action('template_redirect', 'nanimade_custom_registration_page');

/**
 * Handle custom registration form submission
 */
function nanimade_handle_custom_registration() {
    if (isset($_POST['nanimade_register']) && wp_verify_nonce($_POST['nanimade_register_nonce'], 'nanimade_register')) {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $phone = sanitize_text_field($_POST['phone']);
        
        // Validation
        $errors = array();
        
        if (empty($username)) {
            $errors[] = 'Username is required';
        }
        
        if (username_exists($username)) {
            $errors[] = 'Username already exists';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        }
        
        if (!is_email($email)) {
            $errors[] = 'Invalid email address';
        }
        
        if (email_exists($email)) {
            $errors[] = 'Email already exists';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        }
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($first_name)) {
            $errors[] = 'First name is required';
        }
        
        if (empty($last_name)) {
            $errors[] = 'Last name is required';
        }
        
        // If no errors, create user
        if (empty($errors)) {
            $user_id = wp_create_user($username, $password, $email);
            
            if (!is_wp_error($user_id)) {
                // Update user meta
                wp_update_user(array(
                    'ID' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $first_name . ' ' . $last_name
                ));
                
                update_user_meta($user_id, 'phone', $phone);
                
                // Set user role
                $user = new WP_User($user_id);
                $user->set_role('customer');
                
                // Auto login
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                
                // Redirect to account page
                wp_redirect(wc_get_page_permalink('myaccount'));
                exit();
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
        
        // If there are errors, redirect back with errors
        if (!empty($errors)) {
            $error_string = implode(', ', $errors);
            wp_redirect(home_url('/register/?error=' . urlencode($error_string)));
            exit();
        }
    }
}
add_action('init', 'nanimade_handle_custom_registration');

/**
 * Add registration page styles
 */
function nanimade_registration_styles() {
    if (isset($_GET['page']) && $_GET['page'] === 'register') {
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
            .registration-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                padding: 40px;
                width: 100%;
                max-width: 500px;
                text-align: center;
            }
            .registration-logo {
                font-size: 3rem;
                margin-bottom: 20px;
            }
            .registration-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #2d5a27;
                margin-bottom: 30px;
            }
            .registration-form {
                text-align: left;
            }
            .form-group {
                margin-bottom: 20px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: 600;
                color: #333;
            }
            .form-group input {
                width: 100%;
                padding: 15px;
                border: 2px solid #eee;
                border-radius: 8px;
                font-size: 1rem;
                transition: border-color 0.3s ease;
                box-sizing: border-box;
            }
            .form-group input:focus {
                outline: none;
                border-color: #2d5a27;
            }
            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
            .registration-form button {
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
                margin-top: 20px;
            }
            .registration-form button:hover {
                background: #8bc34a;
            }
            .registration-links {
                margin-top: 20px;
                text-align: center;
            }
            .registration-links a {
                color: #2d5a27;
                text-decoration: none;
                margin: 0 10px;
            }
            .registration-links a:hover {
                text-decoration: underline;
            }
            .error-message {
                background: #f8d7da;
                color: #721c24;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .success-message {
                background: #d4edda;
                color: #155724;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .password-strength {
                margin-top: 5px;
                font-size: 0.9rem;
            }
            .strength-weak { color: #dc3545; }
            .strength-medium { color: #ffc107; }
            .strength-strong { color: #28a745; }
            @media (max-width: 600px) {
                .form-row {
                    grid-template-columns: 1fr;
                }
                .registration-container {
                    margin: 20px;
                    padding: 20px;
                }
            }
        </style>';
    }
}
add_action('wp_head', 'nanimade_registration_styles');

/**
 * Create registration template
 */
function nanimade_create_registration_template() {
    $template_path = get_stylesheet_directory() . '/templates/custom-registration.php';
    
    if (!file_exists($template_path)) {
        $template_content = '<?php
/**
 * Custom Registration Template for Nanimade
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Nanimade</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="registration-container">
        <div class="registration-logo">🥒</div>
        <h1 class="registration-title">Create Your Account</h1>
        
        <?php if (isset($_GET["error"])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars(urldecode($_GET["error"])); ?>
            </div>
        <?php endif; ?>
        
        <form class="registration-form" method="post" action="">
            <?php wp_nonce_field("nanimade_register", "nanimade_register_nonce"); ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                <div class="password-strength" id="password-strength"></div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" name="nanimade_register">Create Account</button>
        </form>
        
        <div class="registration-links">
            <a href="<?php echo home_url("/secure-access/"); ?>">Already have an account? Login</a>
            <br>
            <a href="<?php echo home_url(); ?>">← Back to Site</a>
        </div>
    </div>
    
    <script>
        // Password strength checker
        document.getElementById("password").addEventListener("input", function() {
            const password = this.value;
            const strengthDiv = document.getElementById("password-strength");
            let strength = 0;
            let message = "";
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            if (strength < 3) {
                message = "Weak password";
                strengthDiv.className = "password-strength strength-weak";
            } else if (strength < 5) {
                message = "Medium strength password";
                strengthDiv.className = "password-strength strength-medium";
            } else {
                message = "Strong password";
                strengthDiv.className = "password-strength strength-strong";
            }
            
            strengthDiv.textContent = message;
        });
        
        // Password confirmation checker
        document.getElementById("confirm_password").addEventListener("input", function() {
            const password = document.getElementById("password").value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.style.borderColor = "#dc3545";
            } else {
                this.style.borderColor = "#28a745";
            }
        });
    </script>
</body>
</html>';
        
        // Create templates directory if it doesn't exist
        if (!is_dir(dirname($template_path))) {
            mkdir(dirname($template_path), 0755, true);
        }
        
        file_put_contents($template_path, $template_content);
    }
}
add_action('after_switch_theme', 'nanimade_create_registration_template');

/**
 * Add registration link to login page
 */
function nanimade_add_registration_link() {
    if (isset($_GET['page']) && $_GET['page'] === 'secure-access') {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                const loginLinks = document.querySelector(".login-links");
                if (loginLinks) {
                    const registerLink = document.createElement("a");
                    registerLink.href = "' . home_url('/register/') . '";
                    registerLink.textContent = "Create Account";
                    registerLink.style.marginLeft = "10px";
                    loginLinks.appendChild(registerLink);
                }
            });
        </script>';
    }
}
add_action('wp_footer', 'nanimade_add_registration_link');

/**
 * Initialize registration features
 */
function nanimade_init_registration() {
    nanimade_create_registration_template();
}
add_action('init', 'nanimade_init_registration');
