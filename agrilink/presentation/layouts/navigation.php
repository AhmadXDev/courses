<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" 
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>presentation/common/products.php">
                        <i class="fas fa-store me-1"></i>Products
                    </a>
                </li>
                <?php if (SessionManager::isLoggedIn()): ?>
                    <?php if (SessionManager::isCustomer()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>presentation/customer/orders.php">
                            <i class="fas fa-shopping-basket me-1"></i>My Orders
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (SessionManager::isVendor()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>presentation/common/dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>presentation/vendor/products.php">
                                <i class="fas fa-boxes me-1"></i>My Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>presentation/vendor/orders.php">
                                <i class="fas fa-clipboard-list me-1"></i>Manage Orders
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (SessionManager::isAdmin()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog me-1"></i>Admin
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>presentation/admin/users.php">
                                        <i class="fas fa-users me-1"></i>Manage Users
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>presentation/admin/products.php">
                                        <i class="fas fa-boxes me-1"></i>Manage Products
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>presentation/admin/orders.php">
                                        <i class="fas fa-clipboard-list me-1"></i>Manage Orders
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <form class="d-flex" action="<?php echo BASE_URL; ?>presentation/common/products.php" method="GET">
                <input class="form-control me-2" type="search" placeholder="Search products..." name="search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</nav> 
