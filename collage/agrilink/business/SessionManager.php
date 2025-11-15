<?php
class SessionManager {
    // User role constants
    const ROLE_CUSTOMER = 'customer';
    const ROLE_VENDOR = 'vendor';
    const ROLE_ADMIN = 'admin';
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function login($userId, $name, $email, $role) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        $_SESSION['login_time'] = time();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }
    
    public static function logout() {
        // Clear all session variables
        $_SESSION = [];
        
        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
    }
    
    public static function getCurrentUserId() {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    public static function getCurrentUserName() {
        return self::isLoggedIn() ? $_SESSION['user_name'] : null;
    }
    
    public static function getCurrentUserRole() {
        return self::isLoggedIn() ? $_SESSION['user_role'] : null;
    }
    
    public static function isCustomer() {
        return self::getCurrentUserRole() === self::ROLE_CUSTOMER;
    }
    
    public static function isVendor() {
        return self::getCurrentUserRole() === self::ROLE_VENDOR;
    }
    
    public static function isAdmin() {
        return self::getCurrentUserRole() === self::ROLE_ADMIN;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'presentation/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }
    
    public static function requireVendor() {
        self::requireLogin();
        if (!self::isVendor() && !self::isAdmin()) {
            header('Location: ' . BASE_URL . 'index.php?error=unauthorized');
            exit;
        }
    }
    
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ' . BASE_URL . 'index.php?error=unauthorized');
            exit;
        }
    }
} 