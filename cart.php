<?php
session_start();
require_once 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verify database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = (int)$_SESSION['id'];
$cart_items = [];
$total = 0;

// Fetch cart items from database
try {
    $sql = "SELECT c.*, p.name, p.price, p.image, p.stock 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $cart_items[] = $row;
                $total += $row['price'] * $row['quantity'];
            }
        } else {
            throw new Exception("Error fetching cart items: " . $stmt->error);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    error_log("Cart Error: " . $e->getMessage());
    $_SESSION['error'] = "Error loading cart items.";
}

// Display messages
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            " . htmlspecialchars($_SESSION['error']) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            " . htmlspecialchars($_SESSION['success']) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-5">
        <h2>Shopping Cart</h2>
        <?php if (!empty($cart_items)): ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="img-thumbnail me-2"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <span><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($item['size']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <form action="update-cart.php" method="post" class="d-flex align-items-center">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock']; ?>" 
                                               class="form-control form-control-sm" style="width: 70px;">
                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <form action="remove-cart-item.php" method="post">
                                        <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="shop.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                </a>
                <?php if ($total > 0): ?>
                    <form action="checkout.php" method="POST">
                        <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                        <button type="submit" class="btn btn-primary">
                            Proceed to Checkout<i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-primary" disabled>
                        Proceed to Checkout<i class="bi bi-arrow-right ms-2"></i>
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <p class="lead mt-3">Your cart is empty</p>
                <a href="shop.php" class="btn btn-primary mt-3">
                    <i class="bi bi-shop me-2"></i>Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

