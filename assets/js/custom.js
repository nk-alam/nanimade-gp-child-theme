/**
 * Nanimade Custom JavaScript
 * No-refresh functionality and modern UX features
 */

(function($) {
    'use strict';

    // Global variables
    let floatingCart = null;
    let notificationTimeout = null;

    // Initialize when document is ready
    $(document).ready(function() {
        initFloatingCart();
        initAjaxAddToCart();
        initOneClickReorder();
        initOrderTracking();
        initAnalytics();
        initNotifications();
        initSmoothScrolling();
        initLazyLoading();
        initSearchSuggestions();
    });

    /**
     * Initialize floating cart
     */
    function initFloatingCart() {
        // Create floating cart if it doesn't exist
        if ($('#floating-cart').length === 0) {
            $('body').append(`
                <div id="floating-cart" class="floating-cart">
                    <div class="cart-header">
                        <span class="cart-title">Shopping Cart</span>
                        <button class="cart-close">&times;</button>
                    </div>
                    <div class="cart-items"></div>
                    <div class="cart-total">Total: <span class="cart-total-amount">₹0.00</span></div>
                    <div class="cart-actions">
                        <button class="view-cart-btn">View Cart</button>
                        <button class="checkout-btn">Checkout</button>
                    </div>
                </div>
            `);
        }

        floatingCart = $('#floating-cart');

        // Cart toggle functionality
        $(document).on('click', '.cart-toggle, .add-to-cart-btn', function(e) {
            e.preventDefault();
            toggleFloatingCart();
        });

        // Close cart
        $(document).on('click', '.cart-close', function() {
            floatingCart.removeClass('active');
        });

        // Close cart when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#floating-cart').length && 
                !$(e.target).closest('.add-to-cart-btn').length) {
                floatingCart.removeClass('active');
            }
        });

        // Update cart on page load
        updateFloatingCart();
    }

    /**
     * Toggle floating cart visibility
     */
    function toggleFloatingCart() {
        floatingCart.toggleClass('active');
        if (floatingCart.hasClass('active')) {
            updateFloatingCart();
        }
    }

    /**
     * Update floating cart content
     */
    function updateFloatingCart() {
        $.ajax({
            url: nanimade_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'nanimade_get_cart',
                nonce: nanimade_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    renderFloatingCart(response.data);
                }
            }
        });
    }

    /**
     * Render floating cart items
     */
    function renderFloatingCart(cartData) {
        const cartItems = $('.cart-items');
        const cartTotal = $('.cart-total-amount');
        
        cartItems.empty();
        
        if (cartData.items.length > 0) {
            cartData.items.forEach(function(item) {
                cartItems.append(`
                    <div class="cart-item" data-key="${item.key}">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="cart-item-price">${item.price}</div>
                            <div class="cart-item-quantity">
                                <button class="quantity-btn minus" data-key="${item.key}">-</button>
                                <span>${item.quantity}</span>
                                <button class="quantity-btn plus" data-key="${item.key}">+</button>
                            </div>
                        </div>
                        <button class="remove-item" data-key="${item.key}">&times;</button>
                    </div>
                `);
            });
        } else {
            cartItems.append('<p>Your cart is empty</p>');
        }
        
        cartTotal.text(cartData.total);
    }

    /**
     * Initialize AJAX add to cart
     */
    function initAjaxAddToCart() {
        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const productId = $btn.data('product-id');
            const quantity = $btn.data('quantity') || 1;
            
            // Add loading state
            $btn.addClass('loading').text('Adding...');
            
            $.ajax({
                url: nanimade_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nanimade_add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    nonce: nanimade_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Product added to cart!', 'success');
                        updateFloatingCart();
                        updateCartCount(response.data.cart_count);
                        
                        // Animate button
                        $btn.removeClass('loading').text('Added!').addClass('success');
                        setTimeout(function() {
                            $btn.removeClass('success').text('Add to Cart');
                        }, 2000);
                    } else {
                        showNotification('Failed to add product', 'error');
                        $btn.removeClass('loading').text('Add to Cart');
                    }
                },
                error: function() {
                    showNotification('Network error', 'error');
                    $btn.removeClass('loading').text('Add to Cart');
                }
            });
        });
    }

    /**
     * Initialize one-click reorder
     */
    function initOneClickReorder() {
        $(document).on('click', '.one-click-reorder', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const orderId = $btn.data('order-id');
            
            $btn.addClass('loading').text('Adding...');
            
            $.ajax({
                url: nanimade_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nanimade_one_click_reorder',
                    order_id: orderId,
                    nonce: nanimade_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        updateCartCount(response.data.cart_count);
                        $btn.removeClass('loading').text('Added!').addClass('success');
                        setTimeout(function() {
                            $btn.removeClass('success').text('Reorder');
                        }, 2000);
                    } else {
                        showNotification('Failed to reorder', 'error');
                        $btn.removeClass('loading').text('Reorder');
                    }
                },
                error: function() {
                    showNotification('Network error', 'error');
                    $btn.removeClass('loading').text('Reorder');
                }
            });
        });
    }

    /**
     * Initialize order tracking
     */
    function initOrderTracking() {
        $(document).on('click', '.track-order-btn', function(e) {
            e.preventDefault();
            
            const trackingNumber = $(this).data('tracking');
            const $container = $(this).closest('.order-tracking-item');
            
            $container.addClass('loading');
            
            $.ajax({
                url: nanimade_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nanimade_track_order',
                    tracking_number: trackingNumber,
                    nonce: nanimade_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        renderTrackingInfo($container, response.data);
                    } else {
                        showNotification('Unable to track order', 'error');
                    }
                    $container.removeClass('loading');
                },
                error: function() {
                    showNotification('Network error', 'error');
                    $container.removeClass('loading');
                }
            });
        });
    }

    /**
     * Initialize analytics charts
     */
    function initAnalytics() {
        if (typeof Chart !== 'undefined' && $('#orderChart').length > 0) {
            // Order History Chart
            const orderCtx = document.getElementById('orderChart').getContext('2d');
            new Chart(orderCtx, {
                type: 'line',
                data: {
                    labels: chart_data.labels,
                    datasets: [{
                        label: 'Order Value',
                        data: chart_data.data,
                        borderColor: '#2d5a27',
                        backgroundColor: 'rgba(45, 90, 39, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return nanimade_ajax.currency + value;
                                }
                            }
                        }
                    }
                }
            });

            // Spending Trend Chart
            const spendingCtx = document.getElementById('spendingChart').getContext('2d');
            new Chart(spendingCtx, {
                type: 'bar',
                data: {
                    labels: chart_data.labels,
                    datasets: [{
                        label: 'Monthly Spending',
                        data: chart_data.data,
                        backgroundColor: '#8bc34a',
                        borderColor: '#2d5a27',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return nanimade_ajax.currency + value;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    /**
     * Initialize notification system
     */
    function initNotifications() {
        // Create notification container if it doesn't exist
        if ($('#notification').length === 0) {
            $('body').append('<div id="notification" class="notification"></div>');
        }
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        const $notification = $('#notification');
        
        // Clear existing timeout
        if (notificationTimeout) {
            clearTimeout(notificationTimeout);
        }
        
        $notification
            .removeClass('success error info')
            .addClass(type)
            .text(message)
            .addClass('show');
        
        // Auto hide after 3 seconds
        notificationTimeout = setTimeout(function() {
            $notification.removeClass('show');
        }, 3000);
    }

    /**
     * Initialize smooth scrolling
     */
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this.getAttribute('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });
    }

    /**
     * Initialize lazy loading
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Initialize search suggestions
     */
    function initSearchSuggestions() {
        const $searchInput = $('.woocommerce-product-search-field');
        
        if ($searchInput.length) {
            let searchTimeout;
            
            $searchInput.on('input', function() {
                const query = $(this).val();
                
                clearTimeout(searchTimeout);
                
                if (query.length > 2) {
                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: nanimade_ajax.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'nanimade_search_suggestions',
                                query: query,
                                nonce: nanimade_ajax.nonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    showSearchSuggestions(response.data);
                                }
                            }
                        });
                    }, 300);
                } else {
                    hideSearchSuggestions();
                }
            });
        }
    }

    /**
     * Show search suggestions
     */
    function showSearchSuggestions(suggestions) {
        let $suggestions = $('#search-suggestions');
        
        if ($suggestions.length === 0) {
            $suggestions = $('<div id="search-suggestions" class="search-suggestions"></div>');
            $('.woocommerce-product-search-field').after($suggestions);
        }
        
        $suggestions.empty();
        
        suggestions.forEach(function(suggestion) {
            $suggestions.append(`
                <div class="suggestion-item">
                    <a href="${suggestion.url}">
                        <img src="${suggestion.image}" alt="${suggestion.name}">
                        <div class="suggestion-details">
                            <div class="suggestion-name">${suggestion.name}</div>
                            <div class="suggestion-price">${suggestion.price}</div>
                        </div>
                    </a>
                </div>
            `);
        });
        
        $suggestions.show();
    }

    /**
     * Hide search suggestions
     */
    function hideSearchSuggestions() {
        $('#search-suggestions').hide();
    }

    /**
     * Update cart count in header
     */
    function updateCartCount(count) {
        $('.cart-count').text(count);
        $('.cart-count').addClass('pulse');
        setTimeout(function() {
            $('.cart-count').removeClass('pulse');
        }, 1000);
    }

    /**
     * Render tracking information
     */
    function renderTrackingInfo($container, trackingData) {
        const $trackingInfo = $container.find('.tracking-info');
        
        if ($trackingInfo.length === 0) {
            $container.append('<div class="tracking-info"></div>');
        }
        
        $container.find('.tracking-info').html(`
            <div class="tracking-details">
                <h4>Tracking Details</h4>
                <p><strong>Status:</strong> ${trackingData.status}</p>
                <p><strong>Location:</strong> ${trackingData.location}</p>
                <p><strong>Last Update:</strong> ${trackingData.last_update}</p>
            </div>
        `);
    }

    /**
     * Utility function for debouncing
     */
    function debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    /**
     * Utility function for throttling
     */
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Expose functions globally for AJAX callbacks
    window.nanimade = {
        showNotification: showNotification,
        updateFloatingCart: updateFloatingCart,
        updateCartCount: updateCartCount
    };

})(jQuery);
