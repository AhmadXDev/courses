<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/ProductManager.php';
require_once '../../business/OrderManager.php';

// Restrict access to vendors and admins
SessionManager::requireVendor();

// Initialize managers
$productManager = new ProductManager();
$orderManager = new OrderManager();

// Get current user ID
$userId = SessionManager::getCurrentUserId();

// Get vendor's products
$products = $productManager->getProductsByVendor($userId);

// Get vendor's orders
$orders = $orderManager->getOrdersByVendor($userId);

// Count statistics
$totalProducts = count($products);
$totalOrders = count($orders);
$totalStock = 0;
foreach ($products as $product) {
    $totalStock += $product['stock'];
}

// Get recent orders (last 5)
$recentOrders = array_slice($orders, 0, 5);

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <h1 class="mb-4">Vendor Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text display-4"><?php echo $totalProducts; ?></p>
                    <a href="<?php echo BASE_URL; ?>presentation/vendor/products.php" class="btn btn-sm btn-outline-success">
                        Manage Products
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text display-4"><?php echo $totalOrders; ?></p>
                    <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php" class="btn btn-sm btn-outline-success">
                        View All Orders
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Stock</h5>
                    <p class="card-text display-4"><?php echo $totalStock; ?></p>
                    <a href="<?php echo BASE_URL; ?>presentation/vendor/products.php?low_stock=1" class="btn btn-sm btn-outline-success">
                        Low Stock Items
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <a href="<?php echo BASE_URL; ?>presentation/vendor/product_form.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus-circle me-2"></i>Add New Product
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php?status=pending" class="btn btn-outline-warning w-100">
                                <i class="fas fa-clock me-2"></i>View Pending Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentOrders)): ?>
                        <p class="text-muted">No orders yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo count($order['items']); ?> products</td>
                                            <td>
                                                <span class="badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>presentation/common/order_detail.php?id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (count($orders) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php" class="btn btn-outline-success">
                                    View All Orders
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Products Overview</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($products)): ?>
                        <p class="text-muted">No products yet. <a href="<?php echo BASE_URL; ?>presentation/vendor/product_form.php">Add your first product</a>.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Show only first 5 products
                                    $displayProducts = array_slice($products, 0, 5);
                                    foreach ($displayProducts as $product): 
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo BASE_URL . 'assets/images/products/' . htmlspecialchars($product['image']); ?>" 
                                                         class="me-2" alt="" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo $product['stock']; ?></td>
                                            <td>
                                                <?php if ($product['stock'] > 0): ?>
                                                    <span class="badge bg-success">In Stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>presentation/vendor/product_form.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary me-1">
                                                    Edit
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>presentation/common/product_detail.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-outline-success">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (count($products) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo BASE_URL; ?>presentation/vendor/products.php" class="btn btn-outline-success">
                                    Manage All Products
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?> 
