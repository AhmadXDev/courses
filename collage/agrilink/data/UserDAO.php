<?php
require_once 'Database.php';

class UserDAO {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    public function createUser($userData) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, created_at) 
             VALUES (:name, :email, :password, :role, NOW())"
        );
        
        return $stmt->execute([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => $userData['role']
        ]);
    }
    
    public function updateUser($id, $userData) {
        $updateFields = [];
        $params = ['id' => $id];
        
        foreach ($userData as $key => $value) {
            if ($key != 'id') {
                $updateFields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        $updateString = implode(', ', $updateFields);
        $stmt = $this->db->prepare("UPDATE users SET $updateString WHERE id = :id");
        
        return $stmt->execute($params);
    }
    
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY name");
        return $stmt->fetchAll();
    }
    
    public function getAllVendors() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = :role ORDER BY name");
        $stmt->execute(['role' => 'vendor']);
        return $stmt->fetchAll();
    }
    
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
} 