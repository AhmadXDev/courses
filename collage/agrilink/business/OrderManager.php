<?php
require_once __DIR__ . '/../data/OrderDAO.php';
require_once __DIR__ . '/../data/ProductDAO.php';

class OrderManager {
    private $orderDAO;
    private $productDAO;
    
    public function __construct() {
        $this->orderDAO = new OrderDAO();
        $this->productDAO = new ProductDAO();
    }
    
    public function createOrder($orderData) {
        // Validate user is logged in
        if (!SessionManager::isLoggedIn()) {
            return [
                'success' => false,
                'message' => 'You must be logged in to place an order'
            ];
        }
        
        // Prevent vendors from placing orders
        if (SessionManager::isVendor()) {
            return [
                'success' => false,
                'message' => 'As a vendor, you cannot place orders. Vendors can only fulfill orders from customers.'
            ];
        }
        
        // Prevent admins from placing orders
        if (SessionManager::isAdmin()) {
            return [
                'success' => false,
                'message' => 'As an admin, you cannot place orders. Admins can only manage orders and products.'
            ];
        }
        
        // Validate order items
        if (!isset($orderData['items']) || empty($orderData['items'])) {
            return [
                'success' => false,
                'message' => 'Order must contain at least one item'
            ];
        }
        
        // Calculate total amount and validate items
        $totalAmount = 0;
        $validatedItems = [];
        
        foreach ($orderData['items'] as $item) {
            // Get product details to validate availability and price
            $product = $this->productDAO->getProductById($item['product_id']);
            
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Product not found: ID ' . $item['product_id']
                ];
            }
            
            // Check stock availability
            if ($product['stock'] < $item['quantity']) {
                return [
                    'success' => false,
                    'message' => 'Not enough stock for product: ' . $product['name']
                ];
            }
            
            // Add item to validated items with current price
            $validatedItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product['price']
            ];
            
            // Add to total amount
            $totalAmount += $product['price'] * $item['quantity'];
        }
        
        // Create order data
        $processedOrderData = [
            'user_id' => SessionManager::getCurrentUserId(),
            'total_amount' => $totalAmount,
            'pickup_date' => $orderData['pickup_date'] ?? date('Y-m-d', strtotime('+1 day')),
            'status' => 'pending',
            'items' => $validatedItems
        ];
        
        // Create order
        $orderId = $this->orderDAO->createOrder($processedOrderData);
        
        if ($orderId) {
            return [
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $orderId,
                'total_amount' => $totalAmount
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to place order'
            ];
        }
    }
    
    public function getOrderById($id) {
        $order = $this->orderDAO->getOrderById($id);
        
        // Check access permission
        if ($order && !$this->canAccessOrder($order)) {
            return null;
        }
        
        return $order;
    }
    
    public function getOrdersByUser($userId) {
        // Check permission
        if (SessionManager::getCurrentUserId() != $userId && !SessionManager::isAdmin()) {
            return [];
        }
        
        return $this->orderDAO->getOrdersByUser($userId);
    }
    
    public function getOrdersByVendor($vendorId) {
        // Check permission
        if (SessionManager::getCurrentUserId() != $vendorId && !SessionManager::isAdmin()) {
            return [];
        }
        
        return $this->orderDAO->getOrdersByVendor($vendorId);
    }
    
    public function getAllOrders() {
        // Only admin can see all orders
        if (!SessionManager::isAdmin()) {
            return [];
        }
        
        return $this->orderDAO->getAllOrders();
    }
    
    public function updateOrderStatus($id, $status) {
        // Validate status
        $validStatuses = ['pending', 'confirmed', 'ready', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Invalid status'
            ];
        }
        
        // Check if order exists and user has permission
        $order = $this->orderDAO->getOrderById($id);
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found'
            ];
        }
        
        if (!$this->canManageOrder($order)) {
            return [
                'success' => false,
                'message' => 'You do not have permission to update this order'
            ];
        }
        
        // Update status
        $success = $this->orderDAO->updateOrderStatus($id, $status);
        
        if ($success) {
            return [
                'success' => true,
                'message' => 'Order status updated successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update order status'
            ];
        }
    }
    
    private function canAccessOrder($order) {
        $currentUserId = SessionManager::getCurrentUserId();
        
        // Admin can access all orders
        if (SessionManager::isAdmin()) {
            return true;
        }
        
        // Customer can access their own orders
        if ($order['user_id'] == $currentUserId) {
            return true;
        }
        
        // Vendor can access orders containing their products
        if (SessionManager::isVendor()) {
            foreach ($order['items'] as $item) {
                $product = $this->productDAO->getProductById($item['product_id']);
                if ($product && $product['vendor_id'] == $currentUserId) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    private function canManageOrder($order) {
        // Admin can manage all orders
        if (SessionManager::isAdmin()) {
            return true;
        }
        
        // Vendors can manage orders containing their products
        if (SessionManager::isVendor()) {
            $currentUserId = SessionManager::getCurrentUserId();
            
            foreach ($order['items'] as $item) {
                $product = $this->productDAO->getProductById($item['product_id']);
                if ($product && $product['vendor_id'] == $currentUserId) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function isVendorInOrder($vendorId, $orderId) {
        $order = $this->orderDAO->getOrderById($orderId);
        
        if (!$order) {
            return false;
        }
        
        foreach ($order['items'] as $item) {
            $product = $this->productDAO->getProductById($item['product_id']);
            if ($product && $product['vendor_id'] == $vendorId) {
                return true;
            }
        }
        
        return false;
    }
} 