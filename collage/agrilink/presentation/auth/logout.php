<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';

// Logout user
SessionManager::logout();

// Redirect to home page
header('Location: ' . BASE_URL);
exit; 
