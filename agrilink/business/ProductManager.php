<?php
require_once __DIR__ . '/../data/ProductDAO.php';

class ProductManager {
    private $productDAO;
    
    public function __construct() {
        $this->productDAO = new ProductDAO();
    }
    
    public function getProductById($id) {
        return $this->productDAO->getProductById($id);
    }
    
    public function getAllProducts($limit = null, $offset = 0) {
        return $this->productDAO->getAllProducts($limit, $offset);
    }
    
    public function getProductsByVendor($vendorId) {
        return $this->productDAO->getProductsByVendor($vendorId);
    }
    
    public function getFeaturedProducts($limit = 3) {
        return $this->productDAO->getFeaturedProducts($limit);
    }
    
    public function searchProducts($keyword, $category = null) {
        return $this->productDAO->searchProducts($keyword, $category);
    }
    
    public function createProduct($productData) {
        // Validate fields
        $requiredFields = ['name', 'description', 'price', 'category', 'stock', 'vendor_id'];
        foreach ($requiredFields as $field) {
            if (!isset($productData[$field]) || empty($productData[$field])) {
                return [
                    'success' => false,
                    'message' => "Field '$field' is required"
                ];
            }
        }
        
        // Validate price
        if (!is_numeric($productData['price']) || $productData['price'] <= 0) {
            return [
                'success' => false,
                'message' => 'Price must be a positive number'
            ];
        }
        
        // Validate stock
        if (!is_numeric($productData['stock']) || $productData['stock'] < 0) {
            return [
                'success' => false,
                'message' => 'Stock must be a non-negative number'
            ];
        }
        
        // Handle image file - only if we don't already have an image name in productData
        if (!isset($productData['image']) || empty($productData['image'])) {
            // Handle image upload if needed
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $productData['image'] = $this->handleImageUpload($_FILES['image'], $productData['name']);
                if (!$productData['image']) {
                    return [
                        'success' => false,
                        'message' => 'Failed to upload image'
                    ];
                }
            } else {
                // Set default image if none provided
                $productData['image'] = 'default.jpg';
            }
        }
        
        // Debug log to check the image name
        error_log("Final image name before database insert: " . $productData['image']);
        
        // Create product short description if not provided
        if (!isset($productData['short_description']) || empty($productData['short_description'])) {
            $productData['short_description'] = substr($productData['description'], 0, 100) . '...';
        }
        
        // Create product
        $productId = $this->productDAO->createProduct($productData);
        
        if ($productId) {
            return [
                'success' => true,
                'message' => 'Product created successfully',
                'product_id' => $productId,
                'product_data' => $productData // Return the full product data for debugging
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create product'
            ];
        }
    }
    
    public function updateProduct($id, $productData) {
        // Check if product exists
        $product = $this->getProductById($id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        // Check if current user is the vendor of this product
        $currentUserId = SessionManager::getCurrentUserId();
        if (!SessionManager::isAdmin() && $product['vendor_id'] != $currentUserId) {
            return [
                'success' => false,
                'message' => 'You are not authorized to edit this product'
            ];
        }
        
        // Validate price if provided
        if (isset($productData['price'])) {
            if (!is_numeric($productData['price']) || $productData['price'] <= 0) {
                return [
                    'success' => false,
                    'message' => 'Price must be a positive number'
                ];
            }
        }
        
        // Validate stock if provided
        if (isset($productData['stock'])) {
            if (!is_numeric($productData['stock']) || $productData['stock'] < 0) {
                return [
                    'success' => false,
                    'message' => 'Stock must be a non-negative number'
                ];
            }
        }
        
        // Handle image file - only if we don't already have an image name in productData or need to replace it
        if (!isset($productData['image']) || empty($productData['image'])) {
            // Handle image upload if needed
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $productData['image'] = $this->handleImageUpload($_FILES['image'], $productData['name'] ?? $product['name']);
                if (!$productData['image']) {
                    return [
                        'success' => false,
                        'message' => 'Failed to upload image'
                    ];
                }
            } else {
                // Keep existing image
                $productData['image'] = $product['image'];
            }
        }
        
        // Debug log to check the image name
        error_log("Final image name before database update: " . $productData['image']);
        
        // Update product short description if description is updated
        if (isset($productData['description']) && !isset($productData['short_description'])) {
            $productData['short_description'] = substr($productData['description'], 0, 100) . '...';
        }
        
        // Update product
        $success = $this->productDAO->updateProduct($id, $productData);
        
        if ($success) {
            return [
                'success' => true,
                'message' => 'Product updated successfully',
                'product_data' => $productData // Return the full product data for debugging
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update product'
            ];
        }
    }
    
    public function deleteProduct($id) {
        // Check if product exists
        $product = $this->getProductById($id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        
        // Check if current user is the vendor of this product
        $currentUserId = SessionManager::getCurrentUserId();
        if (!SessionManager::isAdmin() && $product['vendor_id'] != $currentUserId) {
            return [
                'success' => false,
                'message' => 'You are not authorized to delete this product'
            ];
        }
        
        // Delete product
        $success = $this->productDAO->deleteProduct($id);
        
        if ($success) {
            // Attempt to delete product image if not default
            if ($product['image'] != 'default.jpg') {
                $imagePath = ROOT_PATH . '/assets/images/products/' . $product['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            
            return [
                'success' => true,
                'message' => 'Product deleted successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete product'
            ];
        }
    }
    
    public function getCategories() {
        return $this->productDAO->getCategories();
    }
    
    private function handleImageUpload($fileData, $productName) {
        $uploadsDir = ROOT_PATH . '/assets/images/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $filename = strtolower(str_replace(' ', '-', $productName)) . '-' . uniqid() . '.' . $extension;
        $targetPath = $uploadsDir . $filename;
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileData['type'], $allowedTypes)) {
            return false;
        }
        
        // Move uploaded file
        if (move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            return $filename;
        }
        
        return false;
    }
} 