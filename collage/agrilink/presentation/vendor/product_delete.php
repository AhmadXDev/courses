<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/ProductManager.php';

// Restrict access to vendors only
SessionManager::requireVendor();

// Initialize manager
$productManager = new ProductManager();

// Get product ID
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify product exists
$product = $productManager->getProductById($productId);

if (!$product) {
    // Product not found, redirect with error
    $_SESSION['product_message'] = 'Product not found';
    $_SESSION['product_alert_type'] = 'danger';
    header('Location: ' . BASE_URL . 'presentation/vendor/products.php');
    exit;
}

// Verify this product belongs to the current vendor
if ($product['vendor_id'] != SessionManager::getCurrentUserId()) {
    // Not authorized, redirect with error
    $_SESSION['product_message'] = 'You are not authorized to delete this product';
    $_SESSION['product_alert_type'] = 'danger';
    header('Location: ' . BASE_URL . 'presentation/vendor/products.php');
    exit;
}

// Process deletion
$result = $productManager->deleteProduct($productId);

// Check if result is an array with success field or just a boolean
if (is_array($result) && isset($result['success'])) {
    if ($result['success']) {
        $_SESSION['product_message'] = $result['message'] ?? 'Product deleted successfully';
        $_SESSION['product_alert_type'] = 'success';
    } else {
        $_SESSION['product_message'] = $result['message'] ?? 'Failed to delete product';
        $_SESSION['product_alert_type'] = 'danger';
    }
} else if ($result) {
    // Handle boolean true result for backward compatibility
    $_SESSION['product_message'] = 'Product deleted successfully';
    $_SESSION['product_alert_type'] = 'success';
} else {
    // Handle boolean false result
    $_SESSION['product_message'] = 'Failed to delete product';
    $_SESSION['product_alert_type'] = 'danger';
}

header('Location: ' . BASE_URL . 'presentation/vendor/products.php');
exit; 