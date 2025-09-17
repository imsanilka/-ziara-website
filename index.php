<?php
session_start();
require_once 'includes/config.php';

// Fetch new arrivals from database
try {
    $sql = "SELECT id, name, description, price, image FROM products 
            WHERE stock > 0 
            ORDER BY created_at DESC 
            LIMIT 4";
    $result = $conn->query($sql);
    $new_arrivals = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching new arrivals: " . $e->getMessage());
    $new_arrivals = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ziara - Elegant Clothing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <section class="hero-banner d-flex justify-content-center align-items-center text-center text-white">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="display-3 fw-bold">Effortless Elegance, Redefined</h1>
            <p class="lead mt-3 mx-auto" style="max-width: 600px;">Discover our new collection, where timeless style meets modern sophistication.</p>
            <div class="mt-4">
                <a href="shop.php" class="btn btn-light me-2">Shop Now</a>
                <a href="about.php" class="btn btn-outline-light">Discover Our Collection</a>
            </div>
        </div>
    </section>

    <!--New Arrivals -->
    <section class="bg-white">
        <div class="container ">
            <div class="text-center mt-5 mb-5">
                    <h2 class="display-4 fw-bold">New Arrivals</h2>
                    <p class="lead text-muted">Freshly curated for the new season. Be the first to wear the latest trends.</p>
            </div>
            <div class="row">
                <?php if (!empty($new_arrivals)): ?>
                    <?php foreach ($new_arrivals as $product): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='assets/images/default-product.jpg'">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></p>
                                    <p class="card-text"><strong>LKR <?php echo number_format($product['price'], 2); ?></strong></p>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-dark">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No new arrivals at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>    

<!--Feedback-->
    <section id="about" class="bg-light py-5">
        <div class="container">
            <div class="text-center ">
                <h2 class="display-4 fw-bold">What Our Customers Say</h2>
                <p class="lead text-muted">We are trusted by thousands of happy customers.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card p-4 h-100 shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                            <img src="assets/images/customer_1.jpeg" class="rounded-circle me-3" alt="Customer avatar" style="width: 60px; height: 60px;">
                            <div>
                                <p class="fw-bold mb-1">Sarah L.</p>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                            </div>
                        </div>
                        <blockquote class="text-muted">
                            <p>"The quality of the fabric is exceptional, and the fit is perfect. I feel so confident and elegant in my new dress from Ziara. It's my go-to for special occasions."</p>
                        </blockquote>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="card p-4 h-100 shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                            <img src="assets/images/customer_2.jpeg" class="rounded-circle me-3" alt="Customer avatar" style="width: 60px; height: 60px;">
                            <div>
                                <p class="fw-bold mb-1">Jessica M.</p>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-regular fa-star text-muted"></i>
                            </div>
                        </div>
                        <blockquote class="text-muted">
                            <p>"I'm so impressed with the attention to detail. From the stitching to the packaging, everything feels luxurious. Ziara has become my favorite online clothing store."</p>
                        </blockquote>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="card p-4 h-100 shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                            <img src="assets/images/customer_3.jpeg" class="rounded-circle me-3" alt="Customer avatar" style="width: 60px; height: 60px;">
                            <div>
                                <p class="fw-bold mb-1">Emily R.</p>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                                <i class="fa-solid fa-star text-warning"></i>
                            </div>
                        </div>
                        <blockquote class="text-muted">
                            <p>"Fast shipping and wonderful customer service. The team was so helpful with my sizing questions. The clothes are even more beautiful in person!"</p>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

     <!-- Categories Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/men.png" class="card-img-top" alt="Men's Fashion">
                        <div class="card-body">
                            <h5 class="card-title">Men</h5>
                            <p class="card-text text-muted">Sharp & Sophisticated</p>
                            <a href="men.php" class="btn btn-dark float-start">View Details</a>
                        </div>
                    </div>
                </div>
                 <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/women.png" class="card-img-top" alt="Women's Fashion">
                        <div class="card-body">
                            <h5 class="card-title">Women</h5>
                            <p class="card-text text-muted">Effortless Elegance</p>
                            <a href="women.php" class="btn btn-dark float-start">View Details</a>
                        </div>
                    </div>
                </div>
                 <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/kid.png" class="card-img-top" alt="Kids' Fashion">
                        <div class="card-body">
                            <h5 class="card-title">Kids</h5>
                            <p class="card-text text-muted">Playful & Practical</p>
                            <a href="kids.php" class="btn btn-dark float-start">View Details</a>
                        </div>
                    </div>
                </div>
                 <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/accessories.png" class="card-img-top" alt="Accessories">
                        <div class="card-body">
                            <h5 class="card-title">Accessories</h5>
                            <p class="card-text text-muted">The Finishing Touch</p>
                            <a href="accessories.php" class="btn btn-dark float-start">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="shop.php" class="btn btn-dark">View All Products</a>
            </div>
        </div>
    </section>

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

