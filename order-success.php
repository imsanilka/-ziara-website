<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Redirect if accessed directly without placing order
if (!isset($_SESSION['last_order_id'])) {
    header('Location: shop.php');
    exit;
}

// Get order ID and clear it from session
$order_id = (int)$_SESSION['last_order_id'];
unset($_SESSION['last_order_id']);

// Verify order exists in database
require_once 'includes/config.php';

try {
    $sql = "SELECT id FROM orders WHERE id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $order_id, $_SESSION['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Order not found or doesn't belong to user
            header('Location: shop.php');
            exit;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    error_log("Order Success Error: " . $e->getMessage());
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Add cache control meta tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <?php 
    if (!file_exists('includes/navbar.php')) {
        die('Navigation file not found');
    } else {
        include 'includes/navbar.php';
    }
    ?>

    <div class="container mt-5 text-center">
        <div class="card p-5 shadow-sm">
            <div class="text-success mb-4">
                <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
            </div>
            <h2 class="mb-4">Thank You for Shopping with ZIARA!</h2>
            <p class="mb-4">Your order #<?php echo htmlspecialchars($order_id); ?> has been placed successfully.</p>
            <p class="mb-4">We will process your order soon.</p>
            <div class="mt-4">
                <a href="order_confirmation.php?order_id=<?php echo $order_id; ?>" 
                   class="btn btn-outline-primary me-2">View Order Details</a>
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>

    <?php 
    if (!file_exists('includes/footer.php')) {
        die('Footer file not found');
    } else {
        include 'includes/footer.php';
    }
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>