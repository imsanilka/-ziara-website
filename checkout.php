<?php
session_start();
require_once 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verify cart is not empty
$user_id = (int)$_SESSION['id'];
$sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc()['count'];

if ($count === 0) {
    $_SESSION['error'] = "Your cart is empty!";
    header("location: cart.php");
    exit;
}

// Get cart total
$total_amount = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0;
if ($total_amount <= 0) {
    $_SESSION['error'] = "Invalid order total!";
    header("location: cart.php");
    exit;
}

$_SESSION['checkout_total'] = $total_amount;

// Fetch cart items
$cart_items = [];
try {
    $sql = "SELECT c.*, p.name, p.price, p.image 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $cart_items[] = $row;
            }
        } else {
            throw new Exception("Error fetching cart items: " . $stmt->error);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    error_log("Checkout Error: " . $e->getMessage());
    $_SESSION['error'] = "Error loading cart items.";
    header("location: cart.php");
    exit;
}

// Verify cart items were fetched
if (empty($cart_items)) {
    $_SESSION['error'] = "Could not load cart items.";
    header("location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Checkout</h1>
        <div class="card mb-4">
            <div class="card-header">Order Summary</div>
            <div class="card-body">
                <ul class="list-group mb-3">
                    <?php foreach ($cart_items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0"><?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?></h6>
                                <small class="text-muted">
                                    Quantity: <?php echo htmlspecialchars($item['quantity'] ?? 0); ?>
                                    <?php if (isset($item['size'])): ?>
                                        | Size: <?php echo htmlspecialchars($item['size']); ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <span class="text-muted">$<?php 
                                $price = isset($item['price']) ? (float)$item['price'] : 0;
                                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
                                echo number_format($price * $quantity, 2); 
                            ?></span>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total (USD)</span>
                        <strong>$<?php echo number_format($total_amount, 2); ?></strong>
                    </li>
                </ul>

                <form action="process_order.php" method="post">
                    <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">

                    <h4 class="mb-3">Shipping Address</h4>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="firstName" class="form-label">First name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="" value="" required>
                        </div>

                        <div class="col-sm-6">
                            <label for="lastName" class="form-label">Last name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="" value="" required>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" required>
                        </div>

                        <div class="col-12">
                            <label for="address2" class="form-label">Address 2 <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control" id="address2" name="address2" placeholder="Apartment or suite">
                        </div>

                        <div class="col-md-5">
                            <label for="country" class="form-label">Country</label>
                            <select class="form-select" id="country" name="country" required>
                                <option value="">Choose...</option>
                                <option>United States</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="state" class="form-label">State</label>
                            <select class="form-select" id="state" name="state" required>
                                <option value="">Choose...</option>
                                <option>California</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="zip" class="form-label">Zip</label>
                            <input type="text" class="form-control" id="zip" name="zip" placeholder="" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3">Payment</h4>

                    <div class="my-3">
                        <div class="form-check">
                            <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked required>
                            <label class="form-check-label" for="credit">Credit card</label>
                        </div>
                        <div class="form-check">
                            <input id="debit" name="paymentMethod" type="radio" class="form-check-input" required>
                            <label class="form-check-label" for="debit">Debit card</label>
                        </div>
                        <div class="form-check">
                            <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" required>
                            <label class="form-check-label" for="paypal">PayPal</label>
                        </div>
                    </div>

                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="cc-name" class="form-label">Name on card</label>
                            <input type="text" class="form-control" id="cc-name" name="cc-name" placeholder="" required>
                            <small class="text-muted">Full name as displayed on card</small>
                        </div>

                        <div class="col-md-6">
                            <label for="cc-number" class="form-label">Credit card number</label>
                            <input type="text" class="form-control" id="cc-number" name="cc-number" placeholder="" required>
                        </div>

                        <div class="col-md-3">
                            <label for="cc-expiration" class="form-label">Expiration</label>
                            <input type="text" class="form-control" id="cc-expiration" name="cc-expiration" placeholder="" required>
                        </div>

                        <div class="col-md-3">
                            <label for="cc-cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cc-cvv" name="cc-cvv" placeholder="" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <button class="w-100 btn btn-primary btn-lg" type="submit">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybB+2z5G+2z5h2z5G+2z5G+2z5G+2z5G+2z5G+2z5G+2z5G" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0Gz5G+2z5h2z5G+2z5G+2z5G+2z5G+2z5G+2z5G+2z5G" crossorigin="anonymous"></script>
    <script>
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = [
        'firstName', 'lastName', 'address', 'country', 
        'state', 'zip', 'cc-name', 'cc-number', 
        'cc-expiration', 'cc-cvv'
    ];
    
    for (const field of requiredFields) {
        const input = document.getElementById(field);
        if (!input || !input.value.trim()) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return;
        }
    }
});
</script>
</body>
</html>