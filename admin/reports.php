<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../login.php");
    exit;
}

require_once "../includes/config.php";

$total_sales = 0;
$total_orders_completed = 0;
$total_orders_pending = 0;
$latest_orders = [];

// Total Sales
$sql_total_sales = "SELECT SUM(total_amount) AS total_sales FROM orders WHERE status = 'completed'";
$result_total_sales = $conn->query($sql_total_sales);
if ($result_total_sales && $row = $result_total_sales->fetch_assoc()) {
    $total_sales = $row['total_sales'] ?? 0;
}

// Total Completed Orders
$sql_completed_orders = "SELECT COUNT(*) AS total FROM orders WHERE status = 'completed'";
$result_completed_orders = $conn->query($sql_completed_orders);
if ($result_completed_orders && $row = $result_completed_orders->fetch_assoc()) {
    $total_orders_completed = $row['total'] ?? 0;
}

// Total Pending Orders
$sql_pending_orders = "SELECT COUNT(*) AS total FROM orders WHERE status = 'pending'";
$result_pending_orders = $conn->query($sql_pending_orders);
if ($result_pending_orders && $row = $result_pending_orders->fetch_assoc()) {
    $total_orders_pending = $row['total'] ?? 0;
}

// Latest Orders
$sql_latest_orders = "SELECT o.id, u.username, o.total_amount, o.status, o.created_at FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10";
$result_latest_orders = $conn->query($sql_latest_orders);
if ($result_latest_orders->num_rows > 0) {
    while($row = $result_latest_orders->fetch_assoc()) {
        $latest_orders[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Dashboard - ZIARA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .status-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include "../includes/admin-navbar.php"; ?>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="bi bi-graph-up me-2"></i>Sales Reports</h2>
                    <div>
                        <button class="btn btn-outline-dark me-2">
                            <i class="bi bi-download me-2"></i>Export Report
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-printer me-2"></i>Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Total Sales</h6>
                                <h2 class="card-title mb-0">$<?php echo number_format($total_sales, 2); ?></h2>
                                <small>Revenue from completed orders</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Completed Orders</h6>
                                <h2 class="card-title mb-0"><?php echo $total_orders_completed; ?></h2>
                                <small>Successfully delivered</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Pending Orders</h6>
                                <h2 class="card-title mb-0"><?php echo $total_orders_pending; ?></h2>
                                <small>Awaiting processing</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Orders Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Latest Orders</h5>
                    <button class="btn btn-sm btn-outline-dark">
                        <i class="bi bi-eye me-1"></i>View All
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                            <?php if (!empty($latest_orders)): ?>
                                <?php foreach ($latest_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order["id"]; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light p-2 me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <?php echo htmlspecialchars($order["username"]); ?>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($order["total_amount"], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $order["status"] === 'completed' ? 'success' : 'warning'; ?> rounded-pill">
                                                <?php echo ucfirst($order["status"]); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y g:i A', strtotime($order["created_at"])); ?></td>
                                        <td>
                                            <a href="order-details.php?id=<?php echo $order["id"]; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox display-4 text-muted"></i>
                                        <p class="mt-2 mb-0">No orders found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

