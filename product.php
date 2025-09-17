<?php
session_start();
require_once 'includes/config.php';

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$product = null;
$error_message = null;

// Validate product ID
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        $error_message = "Invalid product ID.";
    } else {
        try {
            $sql = "SELECT * FROM products WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($result->num_rows == 1) {
                        $product = $result->fetch_assoc();
                    } else {
                        $error_message = "Product not found.";
                    }
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
        } catch (Exception $e) {
            error_log("Product Error: " . $e->getMessage());
            $error_message = "An error occurred while fetching the product.";
        }
    }
} else {
    $error_message = "No product specified.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Product Not Found'; ?> - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php 
    if (!file_exists('includes/navbar.php')) {
        die('Navigation file not found');
    } else {
        include 'includes/navbar.php';
    }
    ?>

    <div class="container mt-5">
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif ($product): ?>
            <div class="row">
                <div class="col-md-6">
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         class="img-fluid" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='assets/images/default-product.jpg'">
                </div>
                <div class="col-md-6">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <h5>Category: <?php echo htmlspecialchars($product['category']); ?></h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <h3>$<?php echo number_format($product['price'], 2); ?></h3>
                    
                    <?php if ($product['stock'] > 0): ?>
                        <p class="text-success">
                            <i class="bi bi-check-circle-fill"></i> In Stock 
                            (<?php echo htmlspecialchars($product['stock']); ?> available)
                        </p>
                        <form action="add_to_cart.php" method="post" id="addToCartForm">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-bold">Quantity:</label>
                                <input type="number" 
                                       name="quantity" 
                                       id="quantity" 
                                       class="form-control" 
                                       value="1" 
                                       min="1" 
                                       max="<?php echo htmlspecialchars($product['stock']); ?>"
                                       required>
                            </div>
                            
                            <div class="input-check mb-3">
                                <label class="form-label fw-bold">Size</label>
                                <?php
                                $sizes = ['XS', 'S', 'M', 'L', 'XL'];
                                foreach ($sizes as $size): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="size" 
                                               value="<?php echo $size; ?>" 
                                               id="size<?php echo $size; ?>" 
                                               required>
                                        <label class="form-check-label" for="size<?php echo $size; ?>">
                                            <?php echo $size; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-danger">
                            <i class="bi bi-x-circle-fill"></i> Out of Stock
                        </p>
                        <button class="btn btn-dark" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php 
    if (!file_exists('includes/footer.php')) {
        die('Footer file not found');
    } else {
        include 'includes/footer.php';
    }
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        const quantity = parseInt(document.getElementById('quantity').value);
        const maxStock = parseInt(<?php echo $product ? $product['stock'] : 0; ?>);
        
        if (quantity < 1 || quantity > maxStock) {
            e.preventDefault();
            alert('Please enter a valid quantity (1-' + maxStock + ')');
            return false;
        }
        
        if (!document.querySelector('input[name="size"]:checked')) {
            e.preventDefault();
            alert('Please select a size');
            return false;
        }
    });
    </script>
</body>
</html>

