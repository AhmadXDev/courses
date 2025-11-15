<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/UserManager.php';

// Restrict access to admins only
SessionManager::requireAdmin();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php');
    exit;
}

// Get form data
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = $_POST['role'] ?? 'customer';
$password = $_POST['password'] ?? '';

// Validate required fields
if (empty($userId) || empty($name) || empty($email) || empty($role)) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode('All fields are required except password'));
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode('Invalid email format'));
    exit;
}

// Validate role
if (!in_array($role, ['customer', 'vendor', 'admin'])) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode('Invalid role'));
    exit;
}

// Validate password if provided
if (!empty($password) && strlen($password) < 6) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode('Password must be at least 6 characters'));
    exit;
}

// Update user
$userManager = new UserManager();
$userData = [
    'name' => $name,
    'email' => $email,
    'role' => $role
];

// Add password to update data only if provided
if (!empty($password)) {
    $userData['password'] = $password;
}

$result = $userManager->updateUserProfile($userId, $userData);

if ($result['success']) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?success=' . urlencode($result['message']));
} else {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode($result['message']));
}
exit; 