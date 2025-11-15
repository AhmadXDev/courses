<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/ProductManager.php';

// Restrict access to admins only
SessionManager::requireAdmin();

// Initialize manager
$productManager = new ProductManager();

// Get product ID
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify product exists
$product = $productManager->getProductById($productId);

if (!$product) {
    // Product not found, redirect with error
    header('Location: ' . BASE_URL . 'presentation/admin/products.php?error=Product+not+found');
    exit;
}

// Process deletion
$result = $productManager->deleteProduct($productId);

if ($result) {
    // Delete the product image if it's not the default
    if ($product['image'] != 'default.jpg') {
        $imagePath = '../../assets/images/products/' . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Success, redirect with success message
    header('Location: ' . BASE_URL . 'presentation/admin/products.php?success=Product+deleted+successfully');
    exit;
} else {
    // Failed, redirect with error
    header('Location: ' . BASE_URL . 'presentation/admin/products.php?error=Failed+to+delete+product');
    exit;
} 
