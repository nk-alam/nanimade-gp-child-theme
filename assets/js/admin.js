/**
 * Nanimade Admin JavaScript
 * Dashboard charts and interactive features
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initSalesChart();
        initQuickActions();
        initWidgetInteractions();
    });

    /**
     * Initialize sales chart
     */
    function initSalesChart() {
        if (typeof Chart !== 'undefined' && $('#salesChart').length > 0) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: sales_chart_data.labels,
                    datasets: [{
                        label: 'Daily Sales',
                        data: sales_chart_data.data,
                        borderColor: '#2d5a27',
                        backgroundColor: 'rgba(45, 90, 39, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2d5a27',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#2d5a27',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Sales: ₹' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
    }

    /**
     * Initialize quick actions
     */
    function initQuickActions() {
        $('.quick-action-item').on('click', function(e) {
            // Add click animation
            $(this).addClass('clicked');
            setTimeout(function() {
                $('.quick-action-item').removeClass('clicked');
            }, 200);
        });
    }

    /**
     * Initialize widget interactions
     */
    function initWidgetInteractions() {
        // Add hover effects to dashboard widgets
        $('.nanimade-dashboard-widget').hover(
            function() {
                $(this).addClass('widget-hover');
            },
            function() {
                $(this).removeClass('widget-hover');
            }
        );

        // Add click effects to buttons
        $('.button').on('click', function() {
            $(this).addClass('button-clicked');
            setTimeout(function() {
                $('.button').removeClass('button-clicked');
            }, 150);
        });

        // Initialize tooltips for status badges
        $('.order-status').each(function() {
            const status = $(this).text();
            const tooltip = getStatusTooltip(status);
            $(this).attr('title', tooltip);
        });
    }

    /**
     * Get status tooltip text
     */
    function getStatusTooltip(status) {
        const tooltips = {
            'Completed': 'Order has been successfully delivered',
            'Processing': 'Order is being prepared and will be shipped soon',
            'On Hold': 'Order is temporarily on hold',
            'Cancelled': 'Order has been cancelled',
            'Refunded': 'Order has been refunded',
            'Failed': 'Order payment failed'
        };
        
        return tooltips[status] || 'Order status information';
    }

    /**
     * Refresh dashboard data
     */
    function refreshDashboardData() {
        // This function can be used to refresh dashboard data via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_refresh_dashboard',
                nonce: nanimade_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateDashboardWidgets(response.data);
                }
            }
        });
    }

    /**
     * Update dashboard widgets with new data
     */
    function updateDashboardWidgets(data) {
        // Update statistics
        if (data.stats) {
            $('.stat-number').each(function(index) {
                const newValue = data.stats[index];
                if (newValue) {
                    $(this).text(newValue);
                }
            });
        }

        // Update recent orders
        if (data.recent_orders) {
            updateRecentOrdersList(data.recent_orders);
        }

        // Update hot products
        if (data.hot_products) {
            updateHotProductsList(data.hot_products);
        }
    }

    /**
     * Update recent orders list
     */
    function updateRecentOrdersList(orders) {
        const $list = $('.recent-orders-list');
        $list.empty();

        orders.forEach(function(order) {
            const orderHtml = `
                <div class="recent-order-item">
                    <div class="order-info">
                        <h4>Order #${order.id}</h4>
                        <p class="customer-name">${order.customer_name}</p>
                        <p class="order-date">${order.date}</p>
                    </div>
                    <div class="order-details">
                        <span class="order-total">${order.total}</span>
                        <span class="order-status status-${order.status}">${order.status_name}</span>
                    </div>
                    <div class="order-actions">
                        <a href="${order.edit_url}" class="button button-small">View</a>
                    </div>
                </div>
            `;
            $list.append(orderHtml);
        });
    }

    /**
     * Update hot products list
     */
    function updateHotProductsList(products) {
        const $list = $('.hot-products-list');
        $list.empty();

        products.forEach(function(product) {
            const productHtml = `
                <div class="hot-product-item">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}">
                    </div>
                    <div class="product-info">
                        <h4>${product.name}</h4>
                        <p class="product-sales">${product.sales} units sold</p>
                        <p class="product-revenue">${product.revenue} revenue</p>
                    </div>
                    <div class="product-actions">
                        <a href="${product.edit_url}" class="button button-small">Edit</a>
                        <a href="${product.view_url}" class="button button-small" target="_blank">View</a>
                    </div>
                </div>
            `;
            $list.append(productHtml);
        });
    }

    /**
     * Export dashboard data
     */
    function exportDashboardData() {
        const data = {
            stats: {
                total_sales: $('.stat-number').eq(0).text(),
                total_orders: $('.stat-number').eq(1).text(),
                today_sales: $('.stat-number').eq(2).text(),
                month_sales: $('.stat-number').eq(3).text()
            },
            chart_data: sales_chart_data,
            export_date: new Date().toISOString()
        };

        const dataStr = JSON.stringify(data, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        const url = URL.createObjectURL(dataBlob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = 'nanimade-dashboard-' + new Date().toISOString().split('T')[0] + '.json';
        link.click();
        
        URL.revokeObjectURL(url);
    }

    /**
     * Print dashboard
     */
    function printDashboard() {
        const printWindow = window.open('', '_blank');
        const dashboardContent = $('.nanimade-dashboard-widget').parent().html();
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Nanimade Dashboard Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .nanimade-dashboard-widget { 
                        border: 1px solid #ddd; 
                        margin-bottom: 20px; 
                        padding: 15px; 
                        page-break-inside: avoid; 
                    }
                    .stats-row { display: flex; gap: 20px; margin-bottom: 15px; }
                    .stat { flex: 1; text-align: center; padding: 10px; background: #f9f9f9; }
                    .stat-number { font-size: 1.5rem; font-weight: bold; color: #2d5a27; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background: #f5f5f5; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <h1>Nanimade Dashboard Report</h1>
                <p>Generated on: ${new Date().toLocaleString()}</p>
                ${dashboardContent}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    }

    /**
     * Initialize real-time updates (if needed)
     */
    function initRealTimeUpdates() {
        // Set up interval for real-time updates (every 5 minutes)
        setInterval(function() {
            refreshDashboardData();
        }, 300000); // 5 minutes
    }

    /**
     * Initialize keyboard shortcuts
     */
    function initKeyboardShortcuts() {
        $(document).keydown(function(e) {
            // Ctrl/Cmd + R to refresh dashboard
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 82) {
                e.preventDefault();
                refreshDashboardData();
            }
            
            // Ctrl/Cmd + E to export data
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 69) {
                e.preventDefault();
                exportDashboardData();
            }
            
            // Ctrl/Cmd + P to print dashboard
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 80) {
                e.preventDefault();
                printDashboard();
            }
        });
    }

    /**
     * Initialize notifications
     */
    function initNotifications() {
        // Check for new orders every 30 seconds
        setInterval(function() {
            checkForNewOrders();
        }, 30000);
    }

    /**
     * Check for new orders
     */
    function checkForNewOrders() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_check_new_orders',
                nonce: nanimade_admin.nonce
            },
            success: function(response) {
                if (response.success && response.data.new_orders > 0) {
                    showNotification(`You have ${response.data.new_orders} new order(s)!`, 'info');
                }
            }
        });
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = $(`
            <div class="notice notice-${type} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        `);
        
        // Add to admin notices area
        $('.wrap h1').after(notification);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            notification.fadeOut();
        }, 5000);
    }

    // Initialize additional features
    initKeyboardShortcuts();
    initNotifications();
    
    // Optionally enable real-time updates
    // initRealTimeUpdates();

    // Expose functions globally for external use
    window.nanimadeAdmin = {
        refreshDashboardData: refreshDashboardData,
        exportDashboardData: exportDashboardData,
        printDashboard: printDashboard,
        showNotification: showNotification
    };

})(jQuery);
