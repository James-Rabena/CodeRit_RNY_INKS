<?php
// filepath: c:\xampp\htdocs\fragrancefusion\update_purchase.php
session_start();
require_once __DIR__ . '/db_connection.php';

// Check if the user is an admin
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit();
}

// Check if the required data is provided
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'error' => 'Order ID and status are required.']);
    exit();
}

$order_id = intval($_POST['order_id']);
$status = trim($_POST['status']);

// Update the order status in the database
$stmt = $conn->prepare("UPDATE cart_items SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update the order status.']);
}

$stmt->close();
$conn->close();
?>