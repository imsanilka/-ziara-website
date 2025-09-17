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
    die("Connection failed: " . mysqli_connect_error());
}

// Validate cart_id parameter
if (isset($_POST["cart_id"])) {
    $cart_id = filter_var($_POST["cart_id"], FILTER_VALIDATE_INT);
    $user_id = (int)$_SESSION["id"];

    if ($cart_id === false) {
        $_SESSION["cart_error"] = "Invalid cart item.";
        header("location: cart.php");
        exit;
    }

    try {
        // Remove item from cart (ensure it belongs to the current user)
        $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $cart_id, $user_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION["cart_message"] = "Item removed from cart.";
                } else {
                    throw new Exception("Item not found or doesn't belong to user.");
                }
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Remove from Cart Error: " . $e->getMessage());
        $_SESSION["cart_error"] = "Error removing item from cart.";
    }
} else {
    $_SESSION["cart_error"] = "No item specified for removal.";
}

$conn->close();

// Redirect back to cart page
header("location: cart.php");
exit;
?>

