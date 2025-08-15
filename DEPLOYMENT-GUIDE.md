# 🚀 Nanimade Pickles - Deployment Guide

Complete guide for deploying your Nanimade Pickles e-commerce website to production.

## 📋 Pre-Deployment Checklist

### ✅ Required Plugins
- [ ] WooCommerce (Latest version)
- [ ] GeneratePress Premium
- [ ] Elementor Pro
- [ ] ACF Pro (Advanced Custom Fields)
- [ ] Premium Addons Pro For Elementor

### ✅ Theme Setup
- [ ] Nanimade Child Theme uploaded and activated
- [ ] Custom plugin installed and activated
- [ ] All custom files in correct directories

### ✅ Configuration
- [ ] WooCommerce setup completed
- [ ] Payment methods configured
- [ ] Shipping zones set up
- [ ] Tax settings configured (if applicable)
- [ ] Shiprocket integration configured

## 🌐 Production Deployment

### Step 1: Server Requirements
```
PHP: 7.4 or higher
MySQL: 5.6 or higher
WordPress: 5.0 or higher
Memory Limit: 256MB minimum (512MB recommended)
Max Execution Time: 300 seconds
```

### Step 2: File Upload
1. **Upload Theme Files**
   ```bash
   /wp-content/themes/nanimade-gp-child-theme/
   ```

2. **Upload Plugin File**
   ```bash
   /wp-content/plugins/nanimade-pickles-plugin.php
   ```

3. **Set File Permissions**
   ```bash
   chmod 755 wp-content/themes/nanimade-gp-child-theme/
   chmod 644 wp-content/themes/nanimade-gp-child-theme/*
   chmod 755 wp-content/themes/nanimade-gp-child-theme/assets/
   chmod 644 wp-content/plugins/nanimade-pickles-plugin.php
   ```

### Step 3: Database Configuration
1. **Import Sample Data** (if available)
2. **Configure WooCommerce Settings**
3. **Set up Product Categories**
4. **Create Sample Products**

### Step 4: Security Configuration
1. **Change Default Login URL**
   - Access: `yoursite.com/secure-access/`
   - Default wp-login.php is disabled

2. **Configure Security Settings**
   ```php
   // Add to wp-config.php
   define('DISALLOW_FILE_EDIT', true);
   define('WP_DEBUG', false);
   define('WP_DEBUG_LOG', false);
   ```

3. **Set Strong Passwords**
   - Admin accounts
   - Database passwords
   - FTP/SSH access

## ⚙️ Configuration Steps

### WooCommerce Configuration
1. **General Settings**
   ```
   Store Address: Your business address
   Currency: INR (Indian Rupee)
   Currency Position: Left with space (₹ 99.00)
   ```

2. **Payment Methods**
   - Enable Cash on Delivery (COD)
   - Configure online payment gateways
   - Set up UPI payments (for India)

3. **Shipping Configuration**
   ```
   Free Shipping: Orders above ₹500
   Local Delivery: Same city delivery
   Standard Shipping: Pan-India delivery
   ```

### Shiprocket Integration
1. **API Configuration**
   ```
   Email: your-shiprocket-email@domain.com
   Password: your-shiprocket-password
   ```

2. **Pickup Location**
   ```
   Address: Your business address
   Pincode: Your area pincode
   Phone: Contact number
   ```

3. **Test Integration**
   - Go to Nanimade > Shiprocket
   - Click "Test API Connection"
   - Verify successful connection

### Analytics Setup
1. **Google Analytics 4**
   - Get GA4 Measurement ID
   - Add to Nanimade > Settings
   - Verify tracking is working

2. **Facebook Pixel** (Optional)
   - Get Facebook Pixel ID
   - Add to analytics settings
   - Test pixel firing

## 🎨 Design Customization

### Logo and Branding
1. **Upload Logo**
   - Go to Appearance > Customize
   - Upload your logo (recommended: 200x60px)
   - Set favicon (32x32px)

2. **Color Scheme**
   - Primary Color: #2d5a27 (Dark Green)
   - Secondary Color: #8bc34a (Light Green)
   - Accent Color: #ff6b35 (Orange)

### Homepage Design
1. **Create Homepage**
   - Create new page "Home"
   - Set as homepage in Settings > Reading

2. **Design with Elementor**
   - Use Elementor to design homepage
   - Add hero section with call-to-action
   - Include product showcase
   - Add testimonials and features

3. **Product Display**
   - Use shortcode: `[nanimade_products category="featured" limit="8"]`
   - Customize product grid layout
   - Add category filters

## 📱 Mobile Optimization

### Responsive Testing
1. **Test on Devices**
   - iPhone (Safari)
   - Android (Chrome)
   - iPad (Safari)
   - Desktop browsers

2. **Performance Testing**
   - Google PageSpeed Insights
   - GTmetrix
   - Pingdom

### Mobile-Specific Features
- Touch-friendly buttons
- Swipe gestures for product gallery
- Mobile-optimized checkout
- One-thumb navigation

## 🔒 Security Hardening

### WordPress Security
1. **Hide WordPress Version**
   ```php
   // Already implemented in theme
   remove_action('wp_head', 'wp_generator');
   ```

2. **Disable XML-RPC**
   ```php
   // Already implemented
   add_filter('xmlrpc_enabled', '__return_false');
   ```

3. **Security Headers**
   ```apache
   # Add to .htaccess
   Header always set X-Content-Type-Options nosniff
   Header always set X-Frame-Options SAMEORIGIN
   Header always set X-XSS-Protection "1; mode=block"
   ```

### File Security
1. **Protect wp-config.php**
   ```apache
   <files wp-config.php>
   order allow,deny
   deny from all
   </files>
   ```

2. **Disable Directory Browsing**
   ```apache
   Options -Indexes
   ```

## 🚀 Performance Optimization

### Caching Setup
1. **Install Caching Plugin**
   - WP Rocket (Premium)
   - W3 Total Cache (Free)
   - WP Super Cache (Free)

2. **Configure Caching**
   ```
   Page Caching: Enabled
   Browser Caching: Enabled
   GZIP Compression: Enabled
   Minification: CSS and JS
   ```

### Image Optimization
1. **Install Image Plugin**
   - Smush (Free/Premium)
   - ShortPixel (Premium)
   - Optimole (Premium)

2. **Configure Settings**
   ```
   Compression: 85% quality
   WebP Format: Enabled
   Lazy Loading: Enabled
   Resize Large Images: Enabled
   ```

### CDN Setup
1. **Cloudflare Setup**
   - Create Cloudflare account
   - Add your domain
   - Update nameservers
   - Configure caching rules

2. **CDN Configuration**
   ```
   Cache Level: Standard
   Browser Cache TTL: 4 hours
   Always Online: Enabled
   Minification: CSS, JS, HTML
   ```

## 📊 Analytics and Tracking

### E-commerce Tracking
1. **Enhanced E-commerce**
   - Purchase tracking
   - Add to cart events
   - Product impressions
   - Checkout funnel

2. **Custom Events**
   ```javascript
   // Already implemented in theme
   gtag('event', 'add_to_cart', {
     currency: 'INR',
     value: 99.00,
     items: [...]
   });
   ```

### Conversion Tracking
1. **Goal Setup**
   - Purchase completion
   - Newsletter signup
   - Contact form submission
   - Account registration

2. **Facebook Pixel Events**
   - ViewContent
   - AddToCart
   - InitiateCheckout
   - Purchase

## 🧪 Testing Procedures

### Functionality Testing
1. **User Registration**
   - [ ] Registration form works
   - [ ] Email validation
   - [ ] Password strength check
   - [ ] Account creation successful

2. **Product Browsing**
   - [ ] Product listing displays correctly
   - [ ] Product details page functional
   - [ ] Image gallery works
   - [ ] Add to cart functionality

3. **Checkout Process**
   - [ ] Cart updates correctly
   - [ ] Checkout form validation
   - [ ] Payment methods work
   - [ ] Order confirmation

4. **Order Management**
   - [ ] Order tracking works
   - [ ] Shiprocket integration
   - [ ] Email notifications
   - [ ] Admin order management

### Performance Testing
1. **Speed Tests**
   - [ ] Homepage loads under 3 seconds
   - [ ] Product pages load quickly
   - [ ] Checkout process is fast
   - [ ] Mobile performance optimized

2. **Load Testing**
   - [ ] Site handles concurrent users
   - [ ] Database performance
   - [ ] Server response times
   - [ ] CDN effectiveness

## 🔧 Maintenance

### Regular Updates
1. **Weekly Tasks**
   - [ ] Check for plugin updates
   - [ ] Review security logs
   - [ ] Monitor site performance
   - [ ] Backup database

2. **Monthly Tasks**
   - [ ] Update WordPress core
   - [ ] Review analytics data
   - [ ] Check broken links
   - [ ] Optimize database

### Backup Strategy
1. **Automated Backups**
   - Daily database backups
   - Weekly full site backups
   - Store backups off-site
   - Test backup restoration

2. **Manual Backups**
   - Before major updates
   - Before design changes
   - Before plugin installations
   - Before going live

## 🆘 Troubleshooting

### Common Issues

**Issue: White Screen of Death**
```php
// Add to wp-config.php for debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Issue: Plugin Conflicts**
1. Deactivate all plugins
2. Activate one by one
3. Test functionality after each activation
4. Identify conflicting plugin

**Issue: Theme Not Loading**
1. Check file permissions
2. Verify parent theme is installed
3. Check for PHP errors
4. Switch to default theme temporarily

**Issue: Shiprocket Not Working**
1. Verify API credentials
2. Check internet connectivity
3. Test API endpoints manually
4. Review error logs

### Emergency Procedures
1. **Site Down**
   - Enable maintenance mode
   - Check server status
   - Review error logs
   - Contact hosting provider

2. **Security Breach**
   - Change all passwords
   - Update all plugins/themes
   - Scan for malware
   - Restore from clean backup

## 📞 Support Resources

### Documentation
- WordPress Codex
- WooCommerce Documentation
- GeneratePress Documentation
- Elementor Documentation

### Community Support
- WordPress Support Forums
- WooCommerce Community
- Stack Overflow
- GitHub Issues

### Professional Support
- WordPress Developers
- WooCommerce Experts
- Security Specialists
- Performance Consultants

---

## 🎉 Go Live Checklist

### Final Steps
- [ ] All testing completed
- [ ] Backups created
- [ ] DNS configured
- [ ] SSL certificate installed
- [ ] Analytics tracking verified
- [ ] Search engines notified
- [ ] Social media updated
- [ ] Team trained on admin panel

### Post-Launch
- [ ] Monitor site performance
- [ ] Check for errors
- [ ] Review analytics data
- [ ] Gather user feedback
- [ ] Plan future enhancements

**🚀 Your Nanimade Pickles website is now ready for the world! 🥒**

Good luck with your pickle empire! 🎉