<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../login.php");
    exit;
}

require_once "../includes/config.php";

// Fetch dashboard data
$sql = "SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT SUM(total_amount) FROM orders) as total_revenue,
    (SELECT COUNT(*) FROM products WHERE stock < 10) as low_stock";
$result = $conn->query($sql);
$stats = $result->fetch_assoc();

// Fetch recent orders
$sql_recent = "SELECT o.*, u.username 
               FROM orders o 
               LEFT JOIN users u ON o.user_id = u.id 
               ORDER BY o.created_at DESC LIMIT 5";
$recent_orders = $conn->query($sql_recent)->fetch_all(MYSQLI_ASSOC);

// Fetch monthly sales data for the current year
$sales_data = array_fill(0, 12, 0); // Initialize array with zeros for all months
$sales_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

$sql_sales = "SELECT 
    MONTH(created_at) as month,
    SUM(total_amount) as total
    FROM orders 
    WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
    GROUP BY MONTH(created_at)
    ORDER BY month";

$result_sales = $conn->query($sql_sales);
if ($result_sales) {
    while ($row = $result_sales->fetch_assoc()) {
        $month_index = (int)$row['month'] - 1;
        $sales_data[$month_index] = (float)$row['total'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar Styling */
        .sidebar {
            background: linear-gradient(135deg, #030303ff 0%, #070707ff 100%);
            min-height: 100vh;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 8px;
            margin: 6px 12px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        /* Stats Cards */
        .stat-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0
        }

        .stat-icon {
            font-size: 2.8rem;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
            opacity: 1;
        }

        /* Quick Links */
        .quick-link {
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .quick-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .quick-link i {
            transition: all 0.3s ease;
        }

        .quick-link:hover i {
            transform: scale(1.1);
        }

        /* Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table tr {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .table tr:hover {
            transform: scale(1.01);
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .table td, .table th {
            border: none;
            padding: 15px;
        }

        .table thead th {
            background: transparent;
            font-weight: 600;
            color: #6c757d;
        }

        /* Custom Buttons */
        .btn-custom {
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Custom Card Headers */
        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* Dashboard Title */
        .dashboard-title {
            font-weight: 600;
            color: #060606ff;
            position: relative;
            padding-bottom: 10px;
        }

        .dashboard-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            border-radius: 2px;
        }

        /* Badge Styling */
        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-light">
    <?php include "../includes/admin-navbar.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php">
                                <i class="bi bi-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="bi bi-cart"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customers.php">
                                <i class="bi bi-people"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="bi bi-graph-up"></i> Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
                    <h1 class="dashboard-title">Dashboard Overview</h1>
                    <div class="btn-toolbar">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-custom btn-outline-dark">
                                <i class="bi bi-download me-2"></i>Export
                            </button>
                            <button type="button" class="btn btn-custom btn-outline-secondary">
                                <i class="bi bi-printer me-2"></i>Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card stat-card border-0 shadow">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">Total Products</h5>
                                        <h2 class="mb-0"><?php echo number_format($stats['total_products']); ?></h2>
                                    </div>
                                    <div class="text-success stat-icon">
                                        <i class="bi bi-box"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">Total Orders</h5>
                                        <h2 class="mb-0"><?php echo number_format($stats['total_orders']); ?></h2>
                                    </div>
                                    <div class="text-warning stat-icon">
                                        <i class="bi bi-cart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">Revenue</h5>
                                        <h2 class="mb-0">$<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                                    </div>
                                    <div class="text-info stat-icon">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add a new chart section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Sales Analytics</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="salesChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders with updated styling -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Orders</h5>
                        <a href="orders.php" class="btn btn-custom btn-sm btn-dark">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Links with updated styling -->
                <div class="row g-4">
                    <div class="col-md-4">
                        <a href="inventory.php" class="card quick-link text-decoration-none text-dark border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-box display-4 text-primary mb-2"></i>
                                <h5>Manage Inventory</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="customers.php" class="card quick-link text-decoration-none text-dark border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-people display-4 text-success mb-2"></i>
                                <h5>Manage Customers</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="reports.php" class="card quick-link text-decoration-none text-dark border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up display-4 text-warning mb-2"></i>
                                <h5>View Reports</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($sales_labels); ?>,
                datasets: [{
                    label: 'Monthly Sales',
                    data: <?php echo json_encode(array_values($sales_data)); ?>,
                    borderColor: '#1e3c72',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(30, 60, 114, 0.1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$ ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$ ' + value;
                            }
                        },
                        grid: {
                            display: true,
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Add this before </body> tag
<script>
    function updateOrderStatus(orderId, status) {
        if (confirm('Are you sure you want to update this order status?')) {
            fetch('update-order-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating order status');
                }
            });
        }
    }

    function deleteOrder(orderId) {
        if (confirm('Are you sure you want to delete this order?')) {
            fetch('delete-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting order');
                }
            });
        }
    }
</script>
    </script>
</body>
</html>

