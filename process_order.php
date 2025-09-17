<?php
session_start();
require_once 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = (int)$_SESSION["id"];
    $total_amount = isset($_POST["total_amount"]) ? (float)$_POST["total_amount"] : 0;
    
    if ($total_amount <= 0) {
        $_SESSION["error"] = "Invalid order total";
        header("location: cart.php");
        exit;
    }

    try {
        $conn->begin_transaction();

        // Insert order
        $sql = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $user_id, $total_amount);
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating order");
        }
        
        $order_id = $conn->insert_id;

        // Get cart items
        $sql = "SELECT * FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Insert order items and update stock
        foreach ($cart_items as $item) {
            // Insert order item
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, size) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $order_id, $item['product_id'], $item['quantity'], $item['size']);
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating order item");
            }

            // Update product stock
            $sql = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $item['quantity'], $item['product_id'], $item['quantity']);
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating product stock");
            }
        }

        // Clear cart
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error clearing cart");
        }

        $conn->commit();
        $_SESSION["success"] = "Order placed successfully!";
        $_SESSION["last_order_id"] = $order_id;
        header("location: order-success.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Order Error: " . $e->getMessage());
        $_SESSION["error"] = "Error processing order. Please try again.";
        header("location: cart.php");
        exit;
    }
}

header("location: cart.php");
exit;
?>

