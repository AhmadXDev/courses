<?php
require_once __DIR__ . '/../data/UserDAO.php';

class UserManager {
    private $userDAO;
    
    public function __construct() {
        $this->userDAO = new UserDAO();
    }
    
    public function authenticate($email, $password) {
        $user = $this->userDAO->getUserByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        // Start session and set user data
        SessionManager::login($user['id'], $user['name'], $user['email'], $user['role']);
        
        return true;
    }
    
    public function registerUser($userData) {
        // Check if email already exists
        $existingUser = $this->userDAO->getUserByEmail($userData['email']);
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'Email address already registered'
            ];
        }
        
        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Set default role if not specified
        if (!isset($userData['role']) || !in_array($userData['role'], ['customer', 'vendor'])) {
            $userData['role'] = 'customer';
        }
        
        // Create user
        $success = $this->userDAO->createUser($userData);
        
        if ($success) {
            return [
                'success' => true,
                'message' => 'Registration successful'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Registration failed'
            ];
        }
    }
    
    public function getUserById($id) {
        return $this->userDAO->getUserById($id);
    }
    
    public function updateUserProfile($id, $userData) {
        // Don't allow changing email to an existing one
        if (isset($userData['email'])) {
            $existingUser = $this->userDAO->getUserByEmail($userData['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                return [
                    'success' => false,
                    'message' => 'Email address already in use'
                ];
            }
        }
        
        // Handle password update
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        } else {
            // Don't update password if empty
            unset($userData['password']);
        }
        
        $success = $this->userDAO->updateUser($id, $userData);
        
        if ($success) {
            // Update session data if the current user is updating their own profile
            if (SessionManager::getCurrentUserId() == $id) {
                if (isset($userData['name'])) {
                    $_SESSION['user_name'] = $userData['name'];
                }
                if (isset($userData['email'])) {
                    $_SESSION['user_email'] = $userData['email'];
                }
                if (isset($userData['role'])) {
                    $_SESSION['user_role'] = $userData['role'];
                }
            }
            
            return [
                'success' => true,
                'message' => 'Profile updated successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update profile'
            ];
        }
    }
    
    public function getAllUsers() {
        return $this->userDAO->getAllUsers();
    }
    
    public function getAllVendors() {
        return $this->userDAO->getAllVendors();
    }
    
    // Alias method for getAllVendors to maintain consistent naming
    public function getVendors() {
        return $this->getAllVendors();
    }
    
    public function deleteUser($id) {
        // Don't allow deleting self
        if (SessionManager::getCurrentUserId() == $id) {
            return [
                'success' => false,
                'message' => 'Cannot delete your own account'
            ];
        }
        
        $success = $this->userDAO->deleteUser($id);
        
        if ($success) {
            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete user'
            ];
        }
    }
} 