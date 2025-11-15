<?php
require_once 'Database.php';

class ProductDAO {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT p.*, u.name as vendor_name 
                                    FROM products p
                                    JOIN users u ON p.vendor_id = u.id 
                                    WHERE p.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getAllProducts($limit = null, $offset = 0) {
        $sql = "SELECT p.*, u.name as vendor_name 
                FROM products p
                JOIN users u ON p.vendor_id = u.id 
                ORDER BY p.created_at DESC";
                
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetchAll();
    }
    
    public function getProductsByVendor($vendorId) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE vendor_id = :vendor_id ORDER BY name");
        $stmt->execute(['vendor_id' => $vendorId]);
        return $stmt->fetchAll();
    }
    
    public function getFeaturedProducts($limit = 3) {
        $stmt = $this->db->prepare("SELECT p.*, u.name as vendor_name 
                                    FROM products p
                                    JOIN users u ON p.vendor_id = u.id 
                                    WHERE p.is_featured = 1
                                    ORDER BY p.created_at DESC
                                    LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchProducts($keyword, $category = null) {
        $sql = "SELECT p.*, u.name as vendor_name 
                FROM products p
                JOIN users u ON p.vendor_id = u.id 
                WHERE (p.name LIKE :keyword1 OR p.description LIKE :keyword2)";
        
        $params = [
            'keyword1' => "%$keyword%",
            'keyword2' => "%$keyword%"
        ];
        
        if ($category) {
            $sql .= " AND p.category = :category";
            $params['category'] = $category;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function createProduct($productData) {
        $stmt = $this->db->prepare(
            "INSERT INTO products (name, description, short_description, price, category, 
                                  stock, image, vendor_id, is_featured, created_at) 
             VALUES (:name, :description, :short_description, :price, :category, 
                    :stock, :image, :vendor_id, :is_featured, NOW())"
        );
        
        $result = $stmt->execute([
            'name' => $productData['name'],
            'description' => $productData['description'],
            'short_description' => $productData['short_description'],
            'price' => $productData['price'],
            'category' => $productData['category'],
            'stock' => $productData['stock'],
            'image' => $productData['image'],
            'vendor_id' => $productData['vendor_id'],
            'is_featured' => $productData['is_featured'] ?? 0
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function updateProduct($id, $productData) {
        $updateFields = [];
        $params = ['id' => $id];
        
        foreach ($productData as $key => $value) {
            if ($key != 'id') {
                $updateFields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        $updateString = implode(', ', $updateFields);
        $stmt = $this->db->prepare("UPDATE products SET $updateString WHERE id = :id");
        
        return $stmt->execute($params);
    }
    
    public function deleteProduct($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT category FROM products ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
