<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/UserManager.php';

// Restrict access to admins only
SessionManager::requireAdmin();

// Get user ID from query parameters
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId <= 0) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode('Invalid user ID'));
    exit;
}

// Check if user is trying to delete their own account
if (SessionManager::getCurrentUserId() == $userId) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode('You cannot delete your own account'));
    exit;
}

// Delete user
$userManager = new UserManager();
$result = $userManager->deleteUser($userId);

if ($result['success']) {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?success=' . urlencode($result['message']));
} else {
    header('Location: ' . BASE_URL . 'presentation/admin/users.php?error=' . urlencode($result['message']));
}
exit; 