<?php
session_start();
header('Content-Type: application/json');
require_once "../includes/config.php";

// Check admin authentication
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $status, $order_id);
        $success = $stmt->execute();
        $stmt->close();
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);