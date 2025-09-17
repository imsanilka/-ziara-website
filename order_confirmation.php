<?php
session_start();
require_once 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Validate order_id
$order_id = isset($_GET["order_id"]) ? filter_var($_GET["order_id"], FILTER_VALIDATE_INT) : 0;
if (!$order_id) {
    header("location: index.php");
    exit;
}

$user_id = (int)$_SESSION["id"];

// Fetch order details with error handling
$order = null;
$order_items = [];

try {
    $sql_order = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
    if ($stmt_order = $conn->prepare($sql_order)) {
        $stmt_order->bind_param("ii", $order_id, $user_id);
        if ($stmt_order->execute()) {
            $result_order = $stmt_order->get_result();
            if ($result_order->num_rows == 1) {
                $order = $result_order->fetch_assoc();
            }
        } else {
            throw new Exception("Error executing order query: " . $stmt_order->error);
        }
        $stmt_order->close();
    } else {
        throw new Exception("Error preparing order query: " . $conn->error);
    }

    if ($order) {
        // Fetch order items with error handling
        $sql_items = "SELECT oi.*, p.name, p.image 
                     FROM order_items oi 
                     JOIN products p ON oi.product_id = p.id 
                     WHERE oi.order_id = ?";
        if ($stmt_items = $conn->prepare($sql_items)) {
            $stmt_items->bind_param("i", $order_id);
            if ($stmt_items->execute()) {
                $result_items = $stmt_items->get_result();
                while ($row = $result_items->fetch_assoc()) {
                    $order_items[] = $row;
                }
            } else {
                throw new Exception("Error executing items query: " . $stmt_items->error);
            }
            $stmt_items->close();
        } else {
            throw new Exception("Error preparing items query: " . $conn->error);
        }
    }
} catch (Exception $e) {
    error_log("Order Confirmation Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while retrieving order details.";
    header("location: index.php");
    exit;
}

$conn->close();

if (!$order) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .table img {
            object-fit: cover;
            height: 50px;
            width: 50px;
            border-radius: 4px;
        }
        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.75em;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Order Confirmation</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION["order_success"])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $_SESSION["order_success"]; unset($_SESSION["order_success"]); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION["error"])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_SESSION["error"]); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION["error"]); ?>
                        <?php endif; ?>

                        <h5>Order Details</h5>
                        <p><strong>Order ID:</strong> <?php echo $order["id"]; ?></p>
                        <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order["created_at"])); ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-warning"><?php echo ucfirst($order["status"]); ?></span></p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($order["total_amount"], 2); ?></p>
                        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order["shipping_address"]); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order["payment_method"]); ?></p>

                        <h5 class="mt-4">Order Items</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="assets/images/<?php echo htmlspecialchars($item["image"]); ?>" 
                                                 width="50" 
                                                 class="me-2"
                                                 alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                                 onerror="this.src='assets/images/default-product.jpg'">
                                            <?php echo htmlspecialchars($item["name"]); ?>
                                        </td>
                                        <td><?php echo $item["quantity"]; ?></td>
                                        <td>$<?php echo number_format($item["price"], 2); ?></td>
                                        <td>$<?php echo number_format($item["price"] * $item["quantity"], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>$<?php echo number_format($order["total_amount"], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="mt-4">
                            <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                            <a href="index.php" class="btn btn-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>

