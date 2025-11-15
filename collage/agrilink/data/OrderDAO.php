<?php
require_once 'Database.php';

class OrderDAO {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function createOrder($orderData) {
        try {
            $this->db->getConnection()->beginTransaction();
            
            // Create order
            $stmt = $this->db->prepare(
                "INSERT INTO orders (user_id, total_amount, pickup_date, status, created_at) 
                 VALUES (:user_id, :total_amount, :pickup_date, :status, NOW())"
            );
            
            $stmt->execute([
                'user_id' => $orderData['user_id'],
                'total_amount' => $orderData['total_amount'],
                'pickup_date' => $orderData['pickup_date'],
                'status' => $orderData['status'] ?? 'pending'
            ]);
            
            $orderId = $this->db->lastInsertId();
            
            // Add order items
            foreach ($orderData['items'] as $item) {
                $itemStmt = $this->db->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity, price) 
                     VALUES (:order_id, :product_id, :quantity, :price)"
                );
                
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                
                // Update product stock
                $stockStmt = $this->db->prepare(
                    "UPDATE products SET stock = stock - :quantity WHERE id = :product_id"
                );
                
                $stockStmt->execute([
                    'quantity' => $item['quantity'],
                    'product_id' => $item['product_id']
                ]);
            }
            
            $this->db->getConnection()->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getOrderById($id) {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name as customer_name, u.email as customer_email 
             FROM orders o
             JOIN users u ON o.user_id = u.id
             WHERE o.id = :id"
        );
        
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();
        
        if ($order) {
            $order['items'] = $this->getOrderItems($id);
        }
        
        return $order;
    }
    
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare(
            "SELECT oi.*, p.name as product_name, p.image 
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :order_id"
        );
        
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }
    
    public function getOrdersByUser($userId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC"
        );
        
        $stmt->execute(['user_id' => $userId]);
        $orders = $stmt->fetchAll();
        
        // Add items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        
        return $orders;
    }
    
    public function getOrdersByVendor($vendorId) {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT o.*, u.name as customer_name
             FROM orders o
             JOIN users u ON o.user_id = u.id
             JOIN order_items oi ON o.id = oi.order_id
             JOIN products p ON oi.product_id = p.id
             WHERE p.vendor_id = :vendor_id
             ORDER BY o.created_at DESC"
        );
        
        $stmt->execute(['vendor_id' => $vendorId]);
        $orders = $stmt->fetchAll();
        
        // Add vendor's items to each order
        foreach ($orders as &$order) {
            $stmt = $this->db->prepare(
                "SELECT oi.*, p.name as product_name, p.image 
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_id = :order_id AND p.vendor_id = :vendor_id"
            );
            
            $stmt->execute([
                'order_id' => $order['id'],
                'vendor_id' => $vendorId
            ]);
            
            $order['items'] = $stmt->fetchAll();
        }
        
        return $orders;
    }
    
    public function getAllOrders() {
        $stmt = $this->db->query(
            "SELECT o.*, u.name as customer_name 
             FROM orders o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC"
        );
        
        $orders = $stmt->fetchAll();
        
        // Add items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        
        return $orders;
    }
    
    public function updateOrderStatus($id, $status) {
        $stmt = $this->db->prepare(
            "UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id"
        );
        
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }
} 