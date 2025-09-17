<?php
session_start();
require_once 'includes/config.php';

// Add error handling for database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$products = [];
$category = 'accessories';
$sql = "SELECT * FROM products WHERE category = ? AND stock > 0"; // Only show in-stock items

try {
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $category);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            throw new Exception("Error executing query: " . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    // Don't show detailed error to users
    $error_message = "An error occurred while fetching products.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessories - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php 
    if (!file_exists('includes/navbar.php')) {
        echo '<div class="alert alert-danger">Navigation file not found</div>';
    } else {
        include 'includes/navbar.php';
    }
    ?>

    <div class="container mt-5">
        <h1 class="mb-4">Accessories</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='assets/images/default-product.jpg'">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                                <a href="product.php?id=<?php echo htmlspecialchars($product['id']); ?>" 
                                   class="btn btn-dark">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-frown display-1 text-muted"></i>
                    <p class="lead mt-3">No accessories are currently available.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php 
    if (!file_exists('includes/footer.php')) {
        echo '<div class="alert alert-danger">Footer file not found</div>';
    } else {
        include 'includes/footer.php';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>

