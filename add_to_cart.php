<?php
session_start();
require_once 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION["error"] = "Please login to add items to cart.";
    header("location: login.php");
    exit;
}

// Validate POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION["error"] = "Invalid request method.";
    header("location: shop.php");
    exit;
}

// Validate and sanitize inputs
$user_id = (int)$_SESSION["id"];
$product_id = isset($_POST["product_id"]) ? (int)$_POST["product_id"] : 0;
$quantity = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 0;
$size = isset($_POST["size"]) ? trim($_POST["size"]) : 'N/A';

if ($product_id <= 0 || $quantity <= 0) {
    $_SESSION["error"] = "Invalid product or quantity.";
    header("location: shop.php");
    exit;
}

try {
    // Check product existence and stock
    $check_product = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $check_product->bind_param("i", $product_id);
    $check_product->execute();
    $product_result = $check_product->get_result();
    
    if ($product_result->num_rows === 0) {
        throw new Exception("Product not found.");
    }
    
    $product = $product_result->fetch_assoc();
    if ($product['stock'] < $quantity) {
        throw new Exception("Not enough stock available.");
    }
    $check_product->close();
    
    // Add to cart
    $cart_sql = "INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)";
    $cart_stmt = $conn->prepare($cart_sql);
    
    if (!$cart_stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $cart_stmt->bind_param("iiis", $user_id, $product_id, $quantity, $size);
    
    if (!$cart_stmt->execute()) {
        throw new Exception("Error adding to cart: " . $cart_stmt->error);
    }
    
    $cart_stmt->close();
    
    $_SESSION["success"] = "Item added to cart successfully!";
    header("location: cart.php");
    exit;
    
} catch (Exception $e) {
    error_log("Cart Error: " . $e->getMessage());
    $_SESSION["error"] = $e->getMessage();
    header("location: shop.php");
    exit;
} finally {
    $conn->close();
}
?>

