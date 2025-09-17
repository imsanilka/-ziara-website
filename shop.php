<?php
session_start();
require_once 'includes/config.php';

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$products = [];
// Only show in-stock items and add proper ordering
$sql = "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC";

try {
    $result = $conn->query($sql);
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $result->free();
    } else {
        throw new Exception("Error fetching products: " . $conn->error);
    }
} catch (Exception $e) {
    error_log("Shop Error: " . $e->getMessage());
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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

    <section class="bg-light">
        <div class="container">
            <div class="text-center mt-5 mb-5">
                <h1 class="font-serif fw-bold" style="font-size: 3rem;">Shop</h1>
                <p class="mt-3 text-muted fs-5">Discover our latest collection</p>
            </div>
             <div class="row mb-5 justify-content-center">
                <div class="col-md-10">
                    <div class="d-flex justify-content-center gap-4 border-bottom pb-4 category-links">
                        <a href="men.php" class="text-decoration-none text-center">
                            <img src="assets/images/men.png" class="rounded-circle border border-2 border-transparent p-1" alt="Men's Fashion" style="width: 100px; height: 100px;">
                            <span class="d-block mt-2 fw-semibold text-muted">Men</span>
                        </a>
                        <a href="women.php" class="text-decoration-none text-center">
                            <img src="assets/images/women.png" class="rounded-circle border border-2 border-transparent p-1" alt="Women's Fashion" style="width: 100px; height: 100px;">
                            <span class="d-block mt-2 fw-semibold text-muted">Women</span>
                        </a>
                        <a href="kids.php" class="text-decoration-none text-center">
                            <img src="assets/images/kid.png" class="rounded-circle border border-2 border-transparent p-1" alt="Kids' Fashion" style="width: 100px; height: 100px;">
                            <span class="d-block mt-2 fw-semibold text-muted">Kids</span>
                        </a>
                        <a href="accessories.php" class="text-decoration-none text-center">
                            <img src="assets/images/accessories.png" class="rounded-circle border border-2 border-transparent p-1" alt="Accessories" style="width: 100px; height: 100px;">
                            <span class="d-block mt-2 fw-semibold text-muted">Accessories</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container mt-5">
        <h1 class="mb-4">Our Products</h1>
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='assets/images/default-product.jpg'"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                                </p>
                                <p class="card-text">
                                    <strong>$<?php echo number_format($product['price'], 2); ?></strong>
                                </p>
                                <?php if ($product['stock'] > 0): ?>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-dark">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-frown display-1 text-muted"></i>
                    <p class="lead mt-3">No products available at the moment.</p>
                </div>
            <?php endif; ?>
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
    <script src="assets/js/script.js"></script>
</body>
</html>

