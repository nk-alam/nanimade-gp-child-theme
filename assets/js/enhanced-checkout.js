/**
 * Enhanced Checkout JavaScript for Nanimade Theme
 * Interactive checkout with real-time updates and animations
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initCheckoutProgress();
        initOrderSummary();
        initTrackingWidget();
        initFormEnhancements();
        initRealTimeUpdates();
        initShiprocketTracking();
        initPickupOption();
        initPaymentMethods();
    });

    /**
     * Initialize checkout progress indicator
     */
    function initCheckoutProgress() {
        const progressSteps = $('.progress-step');
        let currentStep = 1;

        // Update progress based on form completion
        function updateProgress() {
            const billingComplete = isBillingComplete();
            const shippingComplete = isShippingComplete();
            const paymentComplete = isPaymentComplete();

            if (billingComplete && shippingComplete && paymentComplete) {
                currentStep = 4;
            } else if (billingComplete && shippingComplete) {
                currentStep = 3;
            } else if (billingComplete) {
                currentStep = 2;
            } else {
                currentStep = 1;
            }

            progressSteps.removeClass('active completed');
            progressSteps.each(function(index) {
                const stepNumber = index + 1;
                if (stepNumber < currentStep) {
                    $(this).addClass('completed');
                } else if (stepNumber === currentStep) {
                    $(this).addClass('active');
                }
            });
        }

        // Check if billing is complete
        function isBillingComplete() {
            const requiredFields = [
                'billing_first_name',
                'billing_last_name',
                'billing_email',
                'billing_phone'
            ];
            return requiredFields.every(field => $('#' + field).val().trim() !== '');
        }

        // Check if shipping is complete
        function isShippingComplete() {
            if ($('#pickup_option').is(':checked')) {
                return true; // Skip shipping validation for pickup
            }
            const requiredFields = [
                'shipping_address_1',
                'shipping_city',
                'shipping_postcode'
            ];
            return requiredFields.every(field => $('#' + field).val().trim() !== '');
        }

        // Check if payment is complete
        function isPaymentComplete() {
            return $('input[name="payment_method"]:checked').length > 0;
        }

        // Listen for form changes
        $('input, select, textarea').on('change keyup', updateProgress);
        $('input[name="payment_method"]').on('change', updateProgress);

        // Initial update
        updateProgress();
    }

    /**
     * Initialize enhanced order summary
     */
    function initOrderSummary() {
        const summaryHeader = $('.summary-header');
        const summaryContent = $('.summary-content');
        const summaryToggle = $('.summary-toggle');

        // Toggle summary visibility
        summaryHeader.on('click', function() {
            summaryContent.slideToggle(300);
            summaryToggle.toggleClass('rotated');
        });

        // Update order summary with cart data
        function updateOrderSummary() {
            $.ajax({
                url: nanimade_checkout.ajax_url,
                type: 'POST',
                data: {
                    action: 'nanimade_get_cart',
                    nonce: nanimade_checkout.nonce
                },
                success: function(response) {
                    if (response.success) {
                        renderOrderSummary(response.data);
                    }
                }
            });
        }

        // Render order summary
        function renderOrderSummary(cartData) {
            const orderItems = $('.order-items');
            const orderTotals = $('.order-totals');

            // Clear existing items
            orderItems.empty();

            // Add items
            cartData.items.forEach(function(item) {
                const itemHtml = `
                    <div class="order-item" data-key="${item.key}">
                        <img src="${item.image}" alt="${item.name}" class="item-image">
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">${item.price}</div>
                        </div>
                        <div class="item-quantity">Qty: ${item.quantity}</div>
                    </div>
                `;
                orderItems.append(itemHtml);
            });

            // Add totals
            orderTotals.html(`
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>${cartData.total}</span>
                </div>
                <div class="total-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="total-row final">
                    <span>Total:</span>
                    <span>${cartData.total}</span>
                </div>
            `);

            // Animate new items
            $('.order-item').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
                $(this).addClass('slideInUp');
            });
        }

        // Update summary periodically
        updateOrderSummary();
        setInterval(updateOrderSummary, 30000); // Update every 30 seconds
    }

    /**
     * Initialize tracking widget
     */
    function initTrackingWidget() {
        const trackingHeader = $('.tracking-header');
        const trackingContent = $('.tracking-content');
        const trackingToggle = $('.tracking-toggle');

        // Toggle tracking visibility
        trackingHeader.on('click', function() {
            trackingContent.slideToggle(300);
            trackingToggle.toggleClass('rotated');
        });

        // Simulate real-time status updates
        function updateTrackingStatus() {
            const statusSteps = $('.status-step');
            let currentStatus = 0;

            // Simulate status progression
            setInterval(function() {
                statusSteps.removeClass('active completed');
                
                if (currentStatus > 0) {
                    statusSteps.slice(0, currentStatus).addClass('completed');
                }
                
                if (currentStatus < statusSteps.length) {
                    statusSteps.eq(currentStatus).addClass('active');
                }

                currentStatus = Math.min(currentStatus + 1, statusSteps.length);
            }, 5000); // Update every 5 seconds
        }

        updateTrackingStatus();
    }

    /**
     * Initialize form enhancements
     */
    function initFormEnhancements() {
        // Auto-fill user data if logged in
        if (nanimade_checkout.is_logged_in) {
            $('#billing_email').val(nanimade_checkout.user_email);
            $('#billing_first_name').val(nanimade_checkout.user_first_name);
            $('#billing_last_name').val(nanimade_checkout.user_last_name);
        }

        // Enhanced input animations
        $('input, textarea, select').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            if (!$(this).val()) {
                $(this).parent().removeClass('focused');
            }
        });

        // Real-time validation
        $('input[required], textarea[required], select[required]').on('blur', function() {
            validateField($(this));
        });

        // Form submission enhancement
        $('form.checkout').on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                showError('Please fill in all required fields correctly.');
                return false;
            }
            
            // Show loading state
            showLoadingState();
        });
    }

    /**
     * Initialize real-time updates
     */
    function initRealTimeUpdates() {
        // Update delivery estimate based on location
        $('#billing_city, #shipping_city').on('change', function() {
            updateDeliveryEstimate($(this).val());
        });

        // Update totals when shipping method changes
        $('input[name="shipping_method[0]"]').on('change', function() {
            updateOrderTotals();
        });

        // Real-time address validation
        $('#billing_postcode, #shipping_postcode').on('blur', function() {
            validatePostcode($(this).val());
        });
    }

    /**
     * Initialize Shiprocket tracking
     */
    function initShiprocketTracking() {
        // Refresh tracking button
        $('.refresh-tracking').on('click', function() {
            const trackingNumber = $(this).data('tracking');
            const orderId = $(this).data('order');
            const trackingDetails = $('#tracking-details-' + trackingNumber);

            // Show loading
            trackingDetails.html('<div class="loading-spinner"></div> Loading...');

            // Get real-time tracking data
            $.ajax({
                url: nanimade_checkout.ajax_url,
                type: 'POST',
                data: {
                    action: 'nanimade_get_tracking',
                    tracking_number: trackingNumber,
                    order_id: orderId,
                    nonce: nanimade_checkout.nonce
                },
                success: function(response) {
                    if (response.success) {
                        renderTrackingDetails(trackingDetails, response.data);
                    } else {
                        trackingDetails.html('<div class="error-message">' + response.data + '</div>');
                    }
                },
                error: function() {
                    trackingDetails.html('<div class="error-message">Unable to fetch tracking information</div>');
                }
            });
        });

        // Auto-refresh tracking every 30 seconds
        setInterval(function() {
            $('.refresh-tracking').each(function() {
                if ($(this).is(':visible')) {
                    $(this).click();
                }
            });
        }, 30000);
    }

    /**
     * Initialize pickup option
     */
    function initPickupOption() {
        $('#pickup_option').on('change', function() {
            const isPickup = $(this).is(':checked');
            
            if (isPickup) {
                // Hide shipping fields
                $('.woocommerce-shipping-fields').slideUp(300);
                $('.shipping_address').slideUp(300);
                
                // Update delivery estimate
                $('.delivery-date').text('Same day pickup');
                
                // Show pickup information
                showPickupInfo();
            } else {
                // Show shipping fields
                $('.woocommerce-shipping-fields').slideDown(300);
                $('.shipping_address').slideDown(300);
                
                // Update delivery estimate
                updateDeliveryEstimate($('#billing_city').val());
                
                // Hide pickup information
                hidePickupInfo();
            }
        });
    }

    /**
     * Initialize payment methods
     */
    function initPaymentMethods() {
        // Enhanced payment method selection
        $('.wc_payment_method').on('click', function() {
            $('.wc_payment_method').removeClass('selected');
            $(this).addClass('selected');
            
            // Animate selection
            $(this).addClass('success-animation');
            setTimeout(() => {
                $(this).removeClass('success-animation');
            }, 600);
        });

        // Payment method icons
        const paymentIcons = {
            'cod': '💰',
            'bacs': '🏦',
            'cheque': '📄',
            'paypal': '💳'
        };

        $('.wc_payment_method label').each(function() {
            const method = $(this).text().toLowerCase();
            for (const [key, icon] of Object.entries(paymentIcons)) {
                if (method.includes(key)) {
                    $(this).prepend('<span class="payment-icon">' + icon + '</span> ');
                    break;
                }
            }
        });
    }

    /**
     * Utility functions
     */
    function validateField(field) {
        const value = field.val().trim();
        const fieldName = field.attr('name');
        
        // Remove existing validation classes
        field.removeClass('valid invalid');
        
        // Basic validation
        if (fieldName.includes('email') && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                field.addClass('invalid');
                return false;
            }
        }
        
        if (fieldName.includes('phone') && value) {
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (!phoneRegex.test(value)) {
                field.addClass('invalid');
                return false;
            }
        }
        
        if (field.attr('required') && !value) {
            field.addClass('invalid');
            return false;
        }
        
        field.addClass('valid');
        return true;
    }

    function validateForm() {
        let isValid = true;
        
        $('input[required], textarea[required], select[required]').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    function updateDeliveryEstimate(city) {
        const estimates = {
            'Mumbai': '1-2 business days',
            'Delhi': '2-3 business days',
            'Bangalore': '2-3 business days',
            'Chennai': '3-4 business days',
            'Kolkata': '3-4 business days'
        };
        
        const estimate = estimates[city] || '2-3 business days';
        $('.delivery-date').text(estimate);
    }

    function updateOrderTotals() {
        // Trigger WooCommerce update
        $('body').trigger('update_checkout');
    }

    function validatePostcode(postcode) {
        if (postcode && postcode.length < 6) {
            showError('Please enter a valid postal code');
        }
    }

    function showPickupInfo() {
        const pickupInfo = `
            <div class="pickup-info">
                <div class="pickup-icon">🏪</div>
                <div class="pickup-details">
                    <h4>Store Pickup</h4>
                    <p>Pick up your order from our store</p>
                    <p><strong>Address:</strong> 123 Pickle Street, Mumbai</p>
                    <p><strong>Hours:</strong> 9 AM - 8 PM (Mon-Sat)</p>
                </div>
            </div>
        `;
        
        if ($('.pickup-info').length === 0) {
            $('.pickup-option').after(pickupInfo);
        }
    }

    function hidePickupInfo() {
        $('.pickup-info').remove();
    }

    function renderTrackingDetails(container, data) {
        if (data && data.data && data.data.shipment_track) {
            const track = data.data.shipment_track[0];
            let html = '<div class="tracking-info">';
            
            if (track.shipment_status) {
                html += '<div class="tracking-status">';
                html += '<h4>Current Status: ' + track.shipment_status + '</h4>';
                html += '</div>';
            }
            
            if (track.shipment_track_activities && track.shipment_track_activities.length > 0) {
                html += '<div class="tracking-timeline">';
                track.shipment_track_activities.forEach(function(activity) {
                    html += '<div class="timeline-item">';
                    html += '<div class="timeline-date">' + activity.date + '</div>';
                    html += '<div class="timeline-content">' + activity.activity + '</div>';
                    html += '</div>';
                });
                html += '</div>';
            }
            
            html += '</div>';
            container.html(html);
        } else {
            container.html('<div class="no-tracking">No tracking information available</div>');
        }
    }

    function showLoadingState() {
        $('form.checkout').append('<div class="checkout-loading">Processing your order...</div>');
        $('.woocommerce-checkout-payment').addClass('processing');
    }

    function showError(message) {
        const errorHtml = '<div class="error-message">' + message + '</div>';
        $('.woocommerce-error').remove();
        $('form.checkout').prepend(errorHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            $('.error-message').fadeOut();
        }, 5000);
    }

    function showSuccess(message) {
        const successHtml = '<div class="success-message">' + message + '</div>';
        $('.woocommerce-message').remove();
        $('form.checkout').prepend(successHtml);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            $('.success-message').fadeOut();
        }, 3000);
    }

    // Global functions for external access
    window.nanimadeCheckout = {
        updateProgress: function() {
            initCheckoutProgress();
        },
        updateOrderSummary: function() {
            initOrderSummary();
        },
        refreshTracking: function(trackingNumber) {
            $('.refresh-tracking[data-tracking="' + trackingNumber + '"]').click();
        }
    };

})(jQuery);
