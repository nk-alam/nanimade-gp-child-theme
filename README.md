# 🥒 Nanimade Pickles - Complete E-commerce Solution

A modern, feature-rich WordPress e-commerce website for pickle selling business built with GeneratePress Premium child theme and custom functionality.

## 🌟 Features

### 🎨 Modern Design
- **Responsive Design** - Works perfectly on all devices
- **Modern UI/UX** - Clean, professional interface inspired by farmdidi.com
- **Smooth Animations** - CSS3 animations and micro-interactions
- **No-Refresh Experience** - AJAX-powered smooth interactions

### 🛒 E-commerce Features
- **Enhanced Product Display** - Beautiful product cards with hover effects
- **Floating Cart** - Animated floating cart with real-time updates
- **One-Click Reorder** - Quick reorder functionality for returning customers
- **Custom Product Fields** - Ingredients, spice level, shelf life
- **Advanced Checkout** - Multi-step checkout with progress indicator

### 📦 Order Management
- **Real-time Order Tracking** - Shiprocket API integration
- **Custom Order Status** - "Pickling" status for orders
- **Order Timeline** - Visual order progress tracking
- **Email & SMS Notifications** - Automated customer updates

### 👤 Customer Experience
- **Enhanced My Account** - Professional dashboard with analytics
- **Order Analytics** - Personal spending and order insights
- **Quick Reorder** - One-click reorder from order history
- **Wishlist Functionality** - Save favorite products
- **Custom Registration** - Modern registration with validation

### 🔒 Security Features
- **Custom Login URL** - Hidden admin access at `/secure-access/`
- **Enhanced Security** - Multiple security layers
- **Input Sanitization** - All user inputs properly sanitized
- **Brute Force Protection** - Login attempt limiting

### 📊 Admin Dashboard
- **Sales Analytics** - Comprehensive business insights
- **Interactive Charts** - Chart.js powered visualizations
- **Hot Products** - Best-selling product tracking
- **Real-time Updates** - Live dashboard updates
- **Export Functionality** - CSV/PDF export capabilities

## 🚀 Installation

### Prerequisites
- WordPress 5.0+
- PHP 7.4+
- WooCommerce plugin
- GeneratePress Premium theme
- Elementor Pro
- ACF Pro
- Premium Addons Pro For Elementor

### Step 1: Install Required Plugins
1. Install and activate WooCommerce
2. Install and activate GeneratePress Premium
3. Install and activate Elementor Pro
4. Install and activate ACF Pro
5. Install and activate Premium Addons Pro For Elementor

### Step 2: Install Child Theme
1. Upload the `nanimade-gp-child-theme` folder to `/wp-content/themes/`
2. Activate the "Nanimade Child Theme" from WordPress admin

### Step 3: Install Custom Plugin
1. Upload the `nanimade-pickles-plugin.php` to `/wp-content/plugins/`
2. Activate the "Nanimade Pickles - Advanced E-commerce Features" plugin

### Step 4: Configure Settings
1. Go to **Nanimade > Settings** in WordPress admin
2. Configure general settings, e-commerce options, and analytics
3. Set up Shiprocket integration in **Nanimade > Shiprocket**
4. Configure security settings

## ⚙️ Configuration

### WooCommerce Setup
1. Complete WooCommerce setup wizard
2. Configure payment methods (COD, Online payments)
3. Set up shipping zones and methods
4. Configure tax settings if applicable

### Shiprocket Integration
1. Create a Shiprocket account at [shiprocket.in](https://shiprocket.in)
2. Get your API credentials
3. Go to **Nanimade > Shiprocket** in WordPress admin
4. Enter your Shiprocket email and password
5. Test the API connection
6. Configure pickup location and preferences

### Custom Fields Setup
The theme automatically creates custom fields for products:
- **Ingredients** - List of ingredients
- **Spice Level** - Mild, Medium, Hot, Extra Hot
- **Shelf Life** - Product shelf life information

## 🎨 Customization

### Colors and Branding
Edit the CSS variables in `style.css`:
```css
:root {
  --primary-color: #2d5a27;    /* Main brand color */
  --secondary-color: #8bc34a;  /* Secondary brand color */
  --accent-color: #ff6b35;     /* Accent color */
}
```

### Logo and Branding
1. Upload your logo through **Appearance > Customize**
2. Update site title and tagline
3. Configure favicon and site icon

### Homepage Design
1. Create a new page and set as homepage
2. Use Elementor to design your homepage
3. Use the included shortcode `[nanimade_products]` to display products

## 📱 Mobile Optimization

The theme is fully responsive and optimized for mobile devices:
- Touch-friendly interface
- Mobile-optimized checkout
- Responsive navigation
- Fast loading on mobile networks

## 🔧 Development

### File Structure
```
nanimade-gp-child-theme/
├── style.css                 # Main stylesheet
├── functions.php             # Core functionality
├── assets/
│   ├── css/                  # Additional stylesheets
│   └── js/                   # JavaScript files
├── includes/                 # PHP includes
├── templates/                # Custom templates
└── README.md                 # This file
```

### Hooks and Filters
The theme uses WordPress hooks and filters extensively:
- Custom WooCommerce hooks for enhanced functionality
- AJAX handlers for no-refresh experience
- Custom endpoints for My Account page
- Security enhancements through filters

### Adding Custom Features
1. Add new functionality to `functions.php`
2. Create AJAX handlers in `includes/ajax-handlers.php`
3. Add custom templates in `templates/` folder
4. Style new features in `style.css`

## 🧪 Testing

### Debug Mode
Enable debug mode by adding to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Testing Checklist
- [ ] Product display and functionality
- [ ] Cart and checkout process
- [ ] Order tracking and Shiprocket integration
- [ ] My Account dashboard features
- [ ] Mobile responsiveness
- [ ] Security features
- [ ] Admin dashboard analytics

## 🚀 Performance

### Optimization Features
- **Lazy Loading** - Images load as needed
- **Minified Assets** - Compressed CSS/JS files
- **Caching Ready** - Compatible with caching plugins
- **CDN Ready** - Optimized for CDN deployment
- **Database Optimization** - Efficient queries

### Recommended Plugins
- **WP Rocket** - Caching and performance
- **Smush** - Image optimization
- **Cloudflare** - CDN and security
- **UpdraftPlus** - Backup solution

## 🔒 Security

### Security Features
- Custom login URL (`/secure-access/`)
- Hidden WordPress version
- XML-RPC disabled
- Login attempt limiting
- Input sanitization
- Nonce verification
- Security headers

### Security Best Practices
1. Keep WordPress and plugins updated
2. Use strong passwords
3. Enable two-factor authentication
4. Regular backups
5. Monitor security logs

## 📊 Analytics

### Built-in Analytics
- Sales tracking and reporting
- Customer behavior analysis
- Product performance metrics
- Conversion tracking
- Custom event tracking

### Google Analytics Integration
1. Get your Google Analytics 4 tracking ID
2. Go to **Nanimade > Settings**
3. Enter your GA4 Measurement ID
4. Save settings

## 🛠️ Troubleshooting

### Common Issues

**Issue: Floating cart not working**
- Check if jQuery is loaded
- Verify AJAX URL is correct
- Check browser console for errors

**Issue: Shiprocket integration not working**
- Verify API credentials
- Check internet connectivity
- Test API connection in settings

**Issue: Custom fields not showing**
- Ensure ACF Pro is activated
- Check field group settings
- Verify product post type

### Support
For support and customization:
1. Check the documentation
2. Review the code comments
3. Test with default WordPress theme
4. Check error logs

## 📈 Future Enhancements

### Planned Features
- **AI-Powered Recommendations** - Machine learning product suggestions
- **Voice Commerce** - Voice-enabled shopping
- **AR Product Visualization** - Augmented reality features
- **Advanced Loyalty Program** - Customer retention features
- **Multi-language Support** - International expansion
- **Advanced Analytics** - Predictive analytics

### Customization Ideas
- **Subscription Products** - Recurring pickle deliveries
- **Recipe Integration** - Pickle recipes and cooking tips
- **Customer Reviews** - Enhanced review system
- **Social Media Integration** - Social sharing and login
- **Bulk Ordering** - B2B functionality

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

1. Fork the project
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📞 Support

For support and questions:
- **Email**: support@nanimade.com
- **Website**: [nanimade.com](https://nanimade.com)
- **Documentation**: Check the included documentation files

---

**🎉 Congratulations! Your Nanimade Pickles e-commerce website is ready to launch! 🥒**

Built with ❤️ for the pickle industry.