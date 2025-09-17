<?php
session_start();
require_once "../includes/config.php";

// Check admin authentication
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../login.php");
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("location: index.php");
    exit;
}

$order_id = (int)$_GET['id'];

// Fetch order details with user information
$sql = "SELECT o.*, u.username 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?";

$order = null;
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
}

// Fetch order items with product details
$items = [];
if ($order) {
    $sql = "SELECT oi.*, p.name, p.image, p.price 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - ZIARA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .card {
            border-radius: 15px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(to right, #010101ff, #0a0a0aff);
            color: white;
        }
        .table img {
            border-radius: 8px;
        }
        .badge {
            font-size: 0.9em;
            padding: 8px 12px;
        }
    </style>
</head>
<body class="bg-light">
    <?php include "../includes/admin-navbar.php"; ?>

    <div class="container mt-4">
        <?php if ($order): ?>
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    Order #<?php echo $order['id']; ?>
                                </h5>
                                <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Customer Details -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Customer Details</h6>
                                    <p class="mb-1">
                                        <strong>Name:</strong> 
                                        <?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Order Date:</strong> 
                                        <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <h6 class="text-muted">Order Summary</h6>
                                    <p class="mb-1">
                                        <strong>Total Amount:</strong> 
                                        $<?php echo number_format($order['total_amount'], 2); ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Status:</strong> 
                                        <?php echo ucfirst($order['status']); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <h6 class="text-muted mb-3">Order Items</h6>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Size</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($item['image']): ?>
                                                            <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                 class="me-2"
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        <?php endif; ?>
                                                        <span><?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?></td>
                                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td class="text-end">
                                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end">
                                                <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                                </a>
                                <?php if ($order['status'] !== 'completed'): ?>
                                    <button type="button" 
                                            class="btn btn-success ms-2"
                                            onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                        <i class="bi bi-check-circle"></i> Mark as Completed
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Order not found. <a href="index.php" class="alert-link">Return to dashboard</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateOrderStatus(orderId, status) {
            if (confirm('Are you sure you want to mark this order as completed?')) {
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
                        alert('Order marked as completed successfully!');
                        window.location.href = 'index.php';
                    } else {
                        alert(data.message || 'Error updating order status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order status');
                });
            }
        }
    </script>
</body>
</html>