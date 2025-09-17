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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = (int)$_SESSION["id"];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        if (!isset($_POST["quantities"]) || !is_array($_POST["quantities"])) {
            throw new Exception("Invalid request data");
        }

        foreach ($_POST["quantities"] as $cart_id => $quantity) {
            $cart_id = filter_var($cart_id, FILTER_VALIDATE_INT);
            $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
            
            if ($cart_id === false) {
                throw new Exception("Invalid cart item ID");
            }

            if ($quantity > 0) {
                // Check stock availability
                $sql_check = "SELECT p.stock FROM cart c 
                             JOIN products p ON c.product_id = p.id 
                             WHERE c.id = ? AND c.user_id = ? 
                             FOR UPDATE";
                             
                if ($stmt_check = $conn->prepare($sql_check)) {
                    $stmt_check->bind_param("ii", $cart_id, $user_id);
                    if ($stmt_check->execute()) {
                        $result = $stmt_check->get_result();
                        if ($result->num_rows == 1) {
                            $row = $result->fetch_assoc();
                            if ($quantity <= $row["stock"]) {
                                // Update cart quantity
                                $sql_update = "UPDATE cart SET quantity = ? 
                                             WHERE id = ? AND user_id = ?";
                                if ($stmt_update = $conn->prepare($sql_update)) {
                                    $stmt_update->bind_param("iii", $quantity, $cart_id, $user_id);
                                    if (!$stmt_update->execute()) {
                                        throw new Exception("Error updating quantity: " . $stmt_update->error);
                                    }
                                    $stmt_update->close();
                                } else {
                                    throw new Exception("Error preparing update: " . $conn->error);
                                }
                            } else {
                                throw new Exception("Not enough stock available for some items.");
                            }
                        }
                    } else {
                        throw new Exception("Error checking stock: " . $stmt_check->error);
                    }
                    $stmt_check->close();
                } else {
                    throw new Exception("Error preparing check: " . $conn->error);
                }
            } else {
                // Remove item if quantity is 0
                $sql_delete = "DELETE FROM cart WHERE id = ? AND user_id = ?";
                if ($stmt_delete = $conn->prepare($sql_delete)) {
                    $stmt_delete->bind_param("ii", $cart_id, $user_id);
                    if (!$stmt_delete->execute()) {
                        throw new Exception("Error removing item: " . $stmt_delete->error);
                    }
                    $stmt_delete->close();
                } else {
                    throw new Exception("Error preparing delete: " . $conn->error);
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        $_SESSION["success"] = "Cart updated successfully.";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Update Cart Error: " . $e->getMessage());
        $_SESSION["error"] = $e->getMessage();
    }
}

$conn->close();

// Redirect back to cart page
header("location: cart.php");
exit;
?>

