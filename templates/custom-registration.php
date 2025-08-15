<?php
/**
 * Custom Registration Template for Nanimade
 * Modern registration page with validation
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
    <title>Create Account - Nanimade Pickles</title>
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
        
        .registration-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            animation: slideInUp 0.6s ease-out;
        }
        
        .registration-logo {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        .registration-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d5a27;
            margin-bottom: 10px;
        }
        
        .registration-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        
        .registration-form {
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
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        
        .registration-button {
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
            margin-top: 20px;
        }
        
        .registration-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 90, 39, 0.3);
        }
        
        .registration-button:active {
            transform: translateY(0);
        }
        
        .registration-links {
            margin-top: 20px;
            text-align: center;
        }
        
        .registration-links a {
            color: #2d5a27;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .registration-links a:hover {
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
        
        .benefits-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .benefits-section h4 {
            color: #2d5a27;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .benefits-list {
            list-style: none;
        }
        
        .benefits-list li {
            margin-bottom: 8px;
            padding-left: 25px;
            position: relative;
        }
        
        .benefits-list li:before {
            content: "✅";
            position: absolute;
            left: 0;
            color: #4caf50;
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
        
        @media (max-width: 600px) {
            .registration-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .registration-logo {
                font-size: 3rem;
            }
            
            .registration-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-logo">🥒</div>
        <h1 class="registration-title">Join Nanimade Pickles</h1>
        <p class="registration-subtitle">Create your account and start your pickle journey</p>
        
        <?php if (isset($_GET["error"])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars(urldecode($_GET["error"])); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET["success"])): ?>
            <div class="success-message">
                Account created successfully! Welcome to Nanimade Pickles.
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
                <input type="tel" id="phone" name="phone" placeholder="+91 1234567890">
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
            
            <button type="submit" name="nanimade_register" class="registration-button">
                🥒 Create Account
            </button>
        </form>
        
        <div class="benefits-section">
            <h4>🎁 Benefits of Creating an Account</h4>
            <ul class="benefits-list">
                <li>Track your orders in real-time</li>
                <li>Save your favorite pickles</li>
                <li>Quick reorder functionality</li>
                <li>Exclusive member discounts</li>
                <li>Personalized recommendations</li>
                <li>Faster checkout process</li>
            </ul>
        </div>
        
        <div class="registration-links">
            <a href="<?php echo home_url("/secure-access/"); ?>">Already have an account? Login</a>
            <br>
            <a href="<?php echo home_url(); ?>">← Back to Site</a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.registration-form');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const strengthDiv = document.getElementById('password-strength');
            
            // Password strength checker
            password.addEventListener('input', function() {
                const value = this.value;
                let strength = 0;
                let message = "";
                
                if (value.length >= 8) strength++;
                if (/[a-z]/.test(value)) strength++;
                if (/[A-Z]/.test(value)) strength++;
                if (/[0-9]/.test(value)) strength++;
                if (/[^A-Za-z0-9]/.test(value)) strength++;
                
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
            confirmPassword.addEventListener('input', function() {
                const passwordValue = password.value;
                const confirmValue = this.value;
                
                if (confirmValue && passwordValue !== confirmValue) {
                    this.style.borderColor = "#dc3545";
                    this.style.boxShadow = "0 0 0 3px rgba(220, 53, 69, 0.1)";
                } else {
                    this.style.borderColor = "#28a745";
                    this.style.boxShadow = "0 0 0 3px rgba(40, 167, 69, 0.1)";
                }
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('input[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = "#dc3545";
                        isValid = false;
                    } else {
                        field.style.borderColor = "#e9ecef";
                    }
                });
                
                // Check password match
                if (password.value !== confirmPassword.value) {
                    confirmPassword.style.borderColor = "#dc3545";
                    isValid = false;
                }
                
                // Check email format
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.value && !emailRegex.test(email.value)) {
                    email.style.borderColor = "#dc3545";
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    showError('Please fill in all required fields correctly.');
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
            
            // Add focus effects
            const inputs = form.querySelectorAll('input');
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
        });
    </script>
</body>
</html>
