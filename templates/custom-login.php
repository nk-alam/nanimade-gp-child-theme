<?php
/**
 * Custom Login Template for Nanimade
 * Secure login page with modern design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access - Nanimade Pickles</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #2d5a27, #8bc34a);
            font-family: "Inter", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: slideInUp 0.6s ease-out;
        }
        
        .login-logo {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d5a27;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        
        .login-form {
            text-align: left;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2d5a27;
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group.checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .form-group.checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        
        .form-group.checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
        }
        
        .login-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #2d5a27, #8bc34a);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 90, 39, 0.3);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .login-links {
            margin-top: 20px;
            text-align: center;
        }
        
        .login-links a {
            color: #2d5a27;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .login-links a:hover {
            color: #8bc34a;
            text-decoration: underline;
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
            animation: shake 0.5s ease-in-out;
        }
        
        .success-message {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }
        
        .security-notice {
            background: #e3f2fd;
            color: #1976d2;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 0.9rem;
            border-left: 4px solid #2196f3;
        }
        
        .security-notice strong {
            display: block;
            margin-bottom: 5px;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .login-logo {
                font-size: 3rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">🥒</div>
        <h1 class="login-title">Nanimade Admin</h1>
        <p class="login-subtitle">Secure access to your pickle empire</p>
        
        <?php if (isset($_GET["error"])): ?>
            <div class="error-message">
                <?php 
                $error = $_GET["error"];
                switch($error) {
                    case "invalid":
                        echo "Invalid credentials. Please check your username and password.";
                        break;
                    case "empty":
                        echo "Please fill in all required fields.";
                        break;
                    case "blocked":
                        echo "Too many login attempts. Please try again later.";
                        break;
                    default:
                        echo "An error occurred. Please try again.";
                }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET["success"])): ?>
            <div class="success-message">
                <?php 
                $success = $_GET["success"];
                switch($success) {
                    case "logout":
                        echo "You have been successfully logged out.";
                        break;
                    default:
                        echo "Operation completed successfully.";
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form class="login-form" method="post" action="">
            <?php wp_nonce_field("nanimade_login", "nanimade_login_nonce"); ?>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me for 30 days</label>
            </div>
            
            <button type="submit" name="nanimade_login" class="login-button">
                🔐 Secure Login
            </button>
        </form>
        
        <div class="login-links">
            <a href="<?php echo home_url('/register/'); ?>">Create Account</a>
            <a href="<?php echo home_url(); ?>">← Back to Site</a>
        </div>
        
        <div class="security-notice">
            <strong>🔒 Security Notice</strong>
            This is a secure login portal. All access attempts are logged and monitored for security purposes.
        </div>
    </div>
    
    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.login-form');
            const inputs = form.querySelectorAll('input');
            
            // Add focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value.trim();
                
                if (!username || !password) {
                    e.preventDefault();
                    showError('Please fill in all required fields.');
                    return false;
                }
            });
            
            function showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = message;
                
                const existingError = document.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                form.insertBefore(errorDiv, form.firstChild);
                
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            }
        });
    </script>
</body>
</html>
