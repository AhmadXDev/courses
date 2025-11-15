<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriLink - Local Farmer's Market Online</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" defer></script>
</head>
<body>
    <div class="container-fluid p-0">
        <header class="bg-success text-white p-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="mb-0">
                            <a href="<?php echo BASE_URL; ?>" class="text-white text-decoration-none">
                                <i class="fas fa-leaf me-2"></i>AgriLink
                            </a>
                        </h1>
                        <p class="mb-0 small">Local Farmer's Market Online</p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <?php if (SessionManager::isLoggedIn()): ?>
                            <span class="me-3">
                                Welcome, <?php echo htmlspecialchars(SessionManager::getCurrentUserName()); ?>
                                (<?php echo ucfirst(SessionManager::getCurrentUserRole()); ?>)
                            </span>
                            <?php if (SessionManager::isVendor()): ?>
                                <a href="<?php echo BASE_URL; ?>presentation/common/dashboard.php" class="btn btn-outline-light btn-sm me-2">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>presentation/auth/logout.php" class="btn btn-light btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>presentation/auth/login.php" class="btn btn-outline-light btn-sm me-2">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                            <a href="<?php echo BASE_URL; ?>presentation/auth/register.php" class="btn btn-light btn-sm">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
    </div>
</body>
</html> 
