<?php
/**
 * Analytics Dashboard Template for Nanimade Plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>📊 Nanimade Analytics Dashboard</h1>
    
    <div class="nanimade-analytics-container">
        <div class="analytics-header">
            <div class="analytics-logo">📊</div>
            <div class="analytics-title">
                <h2>Business Analytics & Insights</h2>
                <p>Track your pickle empire's performance and growth</p>
            </div>
        </div>
        
        <!-- Date Range Selector -->
        <div class="date-range-selector">
            <h3>📅 Select Date Range</h3>
            <div class="date-controls">
                <select id="date-range">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 3 Months</option>
                    <option value="365">Last Year</option>
                    <option value="custom">Custom Range</option>
                </select>
                <div id="custom-date-range" style="display: none;">
                    <input type="date" id="start-date">
                    <input type="date" id="end-date">
                </div>
                <button type="button" class="button button-primary" id="update-analytics">
                    🔄 Update Analytics
                </button>
            </div>
        </div>
        
        <!-- Key Metrics -->
        <div class="key-metrics">
            <div class="metric-card">
                <div class="metric-icon">💰</div>
                <div class="metric-content">
                    <div class="metric-value" id="total-revenue">₹0</div>
                    <div class="metric-label">Total Revenue</div>
                    <div class="metric-change positive" id="revenue-change">+0%</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">📦</div>
                <div class="metric-content">
                    <div class="metric-value" id="total-orders">0</div>
                    <div class="metric-label">Total Orders</div>
                    <div class="metric-change positive" id="orders-change">+0%</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">👥</div>
                <div class="metric-content">
                    <div class="metric-value" id="new-customers">0</div>
                    <div class="metric-label">New Customers</div>
                    <div class="metric-change positive" id="customers-change">+0%</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">🥒</div>
                <div class="metric-content">
                    <div class="metric-value" id="products-sold">0</div>
                    <div class="metric-label">Products Sold</div>
                    <div class="metric-change positive" id="products-change">+0%</div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>📈 Revenue Trend</h3>
                    <div class="chart-controls">
                        <button type="button" class="chart-type-btn active" data-type="revenue">Revenue</button>
                        <button type="button" class="chart-type-btn" data-type="orders">Orders</button>
                        <button type="button" class="chart-type-btn" data-type="customers">Customers</button>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="trend-chart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-header">
                    <h3>🥒 Top Selling Products</h3>
                </div>
                <div class="chart-wrapper">
                    <canvas id="products-chart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Detailed Analytics -->
        <div class="detailed-analytics">
            <div class="analytics-grid">
                <!-- Sales by Category -->
                <div class="analytics-card">
                    <div class="card-header">
                        <h3>📊 Sales by Category</h3>
                    </div>
                    <div class="card-content">
                        <canvas id="category-chart"></canvas>
                    </div>
                </div>
                
                <!-- Customer Demographics -->
                <div class="analytics-card">
                    <div class="card-header">
                        <h3>👥 Customer Demographics</h3>
                    </div>
                    <div class="card-content">
                        <canvas id="demographics-chart"></canvas>
                    </div>
                </div>
                
                <!-- Order Status Distribution -->
                <div class="analytics-card">
                    <div class="card-header">
                        <h3>📦 Order Status</h3>
                    </div>
                    <div class="card-content">
                        <canvas id="status-chart"></canvas>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div class="analytics-card">
                    <div class="card-header">
                        <h3>💳 Payment Methods</h3>
                    </div>
                    <div class="card-content">
                        <canvas id="payment-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="recent-activity">
            <h3>🕒 Recent Activity</h3>
            <div class="activity-list" id="activity-list">
                <div class="loading">Loading recent activity...</div>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="export-section">
            <h3>📤 Export Data</h3>
            <div class="export-buttons">
                <button type="button" class="button button-secondary" id="export-csv">
                    📄 Export CSV
                </button>
                <button type="button" class="button button-secondary" id="export-pdf">
                    📋 Export PDF Report
                </button>
                <button type="button" class="button button-secondary" id="print-report">
                    🖨️ Print Report
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.nanimade-analytics-container {
    max-width: 1400px;
    margin: 20px 0;
}

.analytics-header {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #2d5a27, #8bc34a);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.analytics-logo {
    font-size: 3rem;
    margin-right: 20px;
}

.analytics-title h2 {
    margin: 0 0 10px 0;
    color: white;
}

.analytics-title p {
    margin: 0;
    opacity: 0.9;
}

.date-range-selector {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.date-range-selector h3 {
    margin: 0 0 15px 0;
    color: #2d5a27;
}

.date-controls {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.date-controls select,
.date-controls input {
    padding: 10px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
}

.key-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.metric-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    transition: transform 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
}

.metric-icon {
    font-size: 2.5rem;
    margin-right: 20px;
}

.metric-content {
    flex: 1;
}

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2d5a27;
    margin-bottom: 5px;
}

.metric-label {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.metric-change {
    font-size: 0.8rem;
    font-weight: 600;
}

.metric-change.positive {
    color: #4caf50;
}

.metric-change.negative {
    color: #f44336;
}

.charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.chart-container {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-header h3 {
    margin: 0;
    color: #2d5a27;
}

.chart-controls {
    display: flex;
    gap: 10px;
}

.chart-type-btn {
    padding: 8px 15px;
    border: 2px solid #e9ecef;
    background: white;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.chart-type-btn.active,
.chart-type-btn:hover {
    background: #2d5a27;
    color: white;
    border-color: #2d5a27;
}

.chart-wrapper {
    position: relative;
    height: 300px;
}

.detailed-analytics {
    margin-bottom: 30px;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
}

.analytics-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.card-header h3 {
    margin: 0;
    color: #2d5a27;
}

.card-content {
    padding: 20px;
    height: 250px;
}

.recent-activity {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.recent-activity h3 {
    margin: 0 0 20px 0;
    color: #2d5a27;
}

.activity-list {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    font-size: 1.5rem;
    margin-right: 15px;
    width: 40px;
    text-align: center;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.activity-time {
    color: #666;
    font-size: 0.9rem;
}

.export-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.export-section h3 {
    margin: 0 0 20px 0;
    color: #2d5a27;
}

.export-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.loading {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 20px;
}

@media (max-width: 768px) {
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .analytics-grid {
        grid-template-columns: 1fr;
    }
    
    .key-metrics {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .date-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .chart-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .export-buttons {
        flex-direction: column;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
jQuery(document).ready(function($) {
    let currentCharts = {};
    let currentDateRange = 30;
    
    // Initialize analytics
    loadAnalytics();
    
    // Date range change handler
    $('#date-range').on('change', function() {
        currentDateRange = $(this).val();
        if (currentDateRange === 'custom') {
            $('#custom-date-range').show();
        } else {
            $('#custom-date-range').hide();
            loadAnalytics();
        }
    });
    
    // Update analytics button
    $('#update-analytics').on('click', function() {
        loadAnalytics();
    });
    
    // Chart type buttons
    $('.chart-type-btn').on('click', function() {
        $('.chart-type-btn').removeClass('active');
        $(this).addClass('active');
        updateTrendChart($(this).data('type'));
    });
    
    // Export buttons
    $('#export-csv').on('click', function() {
        exportData('csv');
    });
    
    $('#export-pdf').on('click', function() {
        exportData('pdf');
    });
    
    $('#print-report').on('click', function() {
        window.print();
    });
    
    function loadAnalytics() {
        const startDate = $('#start-date').val();
        const endDate = $('#end-date').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_get_analytics',
                date_range: currentDateRange,
                start_date: startDate,
                end_date: endDate,
                nonce: '<?php echo wp_create_nonce("nanimade_analytics_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    updateMetrics(response.data.metrics);
                    updateCharts(response.data.charts);
                    updateActivity(response.data.activity);
                } else {
                    console.error('Analytics load failed:', response.data);
                }
            },
            error: function() {
                console.error('Network error loading analytics');
            }
        });
    }
    
    function updateMetrics(metrics) {
        $('#total-revenue').text('₹' + metrics.revenue.toLocaleString());
        $('#total-orders').text(metrics.orders.toLocaleString());
        $('#new-customers').text(metrics.customers.toLocaleString());
        $('#products-sold').text(metrics.products.toLocaleString());
        
        // Update change indicators
        $('#revenue-change').text(metrics.revenue_change + '%').removeClass('positive negative').addClass(metrics.revenue_change >= 0 ? 'positive' : 'negative');
        $('#orders-change').text(metrics.orders_change + '%').removeClass('positive negative').addClass(metrics.orders_change >= 0 ? 'positive' : 'negative');
        $('#customers-change').text(metrics.customers_change + '%').removeClass('positive negative').addClass(metrics.customers_change >= 0 ? 'positive' : 'negative');
        $('#products-change').text(metrics.products_change + '%').removeClass('positive negative').addClass(metrics.products_change >= 0 ? 'positive' : 'negative');
    }
    
    function updateCharts(chartData) {
        // Destroy existing charts
        Object.values(currentCharts).forEach(chart => {
            if (chart) chart.destroy();
        });
        currentCharts = {};
        
        // Create trend chart
        const trendCtx = document.getElementById('trend-chart').getContext('2d');
        currentCharts.trend = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: chartData.trend.labels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData.trend.revenue,
                    borderColor: '#2d5a27',
                    backgroundColor: 'rgba(45, 90, 39, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Create products chart
        const productsCtx = document.getElementById('products-chart').getContext('2d');
        currentCharts.products = new Chart(productsCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.products.labels,
                datasets: [{
                    data: chartData.products.data,
                    backgroundColor: [
                        '#2d5a27',
                        '#8bc34a',
                        '#ff6b35',
                        '#ff9800',
                        '#2196f3'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Create category chart
        const categoryCtx = document.getElementById('category-chart').getContext('2d');
        currentCharts.category = new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: chartData.category.labels,
                datasets: [{
                    label: 'Sales',
                    data: chartData.category.data,
                    backgroundColor: '#2d5a27'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Create demographics chart
        const demographicsCtx = document.getElementById('demographics-chart').getContext('2d');
        currentCharts.demographics = new Chart(demographicsCtx, {
            type: 'pie',
            data: {
                labels: chartData.demographics.labels,
                datasets: [{
                    data: chartData.demographics.data,
                    backgroundColor: [
                        '#2d5a27',
                        '#8bc34a',
                        '#ff6b35',
                        '#ff9800'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Create status chart
        const statusCtx = document.getElementById('status-chart').getContext('2d');
        currentCharts.status = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.status.labels,
                datasets: [{
                    data: chartData.status.data,
                    backgroundColor: [
                        '#4caf50',
                        '#ff9800',
                        '#2196f3',
                        '#f44336'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Create payment chart
        const paymentCtx = document.getElementById('payment-chart').getContext('2d');
        currentCharts.payment = new Chart(paymentCtx, {
            type: 'bar',
            data: {
                labels: chartData.payment.labels,
                datasets: [{
                    label: 'Orders',
                    data: chartData.payment.data,
                    backgroundColor: '#8bc34a'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    function updateActivity(activities) {
        const activityList = $('#activity-list');
        activityList.empty();
        
        if (activities.length === 0) {
            activityList.html('<div class="loading">No recent activity</div>');
            return;
        }
        
        activities.forEach(activity => {
            const activityHtml = `
                <div class="activity-item">
                    <div class="activity-icon">${activity.icon}</div>
                    <div class="activity-content">
                        <div class="activity-title">${activity.title}</div>
                        <div class="activity-time">${activity.time}</div>
                    </div>
                </div>
            `;
            activityList.append(activityHtml);
        });
    }
    
    function updateTrendChart(type) {
        if (!currentCharts.trend) return;
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_get_trend_data',
                type: type,
                date_range: currentDateRange,
                nonce: '<?php echo wp_create_nonce("nanimade_analytics_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    currentCharts.trend.data.datasets[0].data = response.data.values;
                    currentCharts.trend.data.datasets[0].label = response.data.label;
                    currentCharts.trend.update();
                }
            }
        });
    }
    
    function exportData(format) {
        const startDate = $('#start-date').val();
        const endDate = $('#end-date').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nanimade_export_analytics',
                format: format,
                date_range: currentDateRange,
                start_date: startDate,
                end_date: endDate,
                nonce: '<?php echo wp_create_nonce("nanimade_analytics_nonce"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    if (format === 'csv') {
                        downloadFile(response.data.url, 'nanimade-analytics.csv');
                    } else if (format === 'pdf') {
                        window.open(response.data.url, '_blank');
                    }
                } else {
                    alert('Export failed: ' + response.data);
                }
            },
            error: function() {
                alert('Export failed due to network error');
            }
        });
    }
    
    function downloadFile(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});
</script>
