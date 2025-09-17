<?php
session_start();
require_once 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['error'] = "Please login to manage your cart.";
    header("location: login.php");
    exit;
}

// Validate request method and cart_id
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cart_id'])) {
    $_SESSION['error'] = "Invalid request.";
    header('Location: cart.php');
    exit;
}

try {
    $cart_id = filter_var($_POST['cart_id'], FILTER_VALIDATE_INT);
    $user_id = (int)$_SESSION['id'];

    if ($cart_id === false) {
        throw new Exception("Invalid cart item ID");
    }

    // Verify item belongs to user and delete it
    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $cart_id, $user_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['success'] = "Item removed from cart successfully.";
            } else {
                throw new Exception("Item not found or doesn't belong to you.");
            }
        } else {
            throw new Exception("Error removing item: " . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("Database error: " . $conn->error);
    }

} catch (Exception $e) {
    error_log("Remove Cart Item Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to remove item from cart.";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

header('Location: cart.php');
exit;
?>