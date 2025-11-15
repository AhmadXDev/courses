<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/OrderManager.php';
require_once '../../business/ProductManager.php';

// Ensure user is logged in
SessionManager::requireLogin();

// Initialize managers
$orderManager = new OrderManager();
$productManager = new ProductManager();

// Get order ID from query parameters
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId <= 0) {
    header('Location: ' . BASE_URL);
    exit;
}

// Get order details
$order = $orderManager->getOrderById($orderId);

if (!$order) {
    header('Location: ' . BASE_URL);
    exit;
}

// Security check: Only allow order owner, vendor of products in order, or admin to view
$currentUserId = SessionManager::getCurrentUserId();
$canAccess = ($currentUserId == $order['user_id']) || 
             SessionManager::isAdmin() || 
             (SessionManager::isVendor() && $orderManager->isVendorInOrder($currentUserId, $orderId));

if (!$canAccess) {
    header('Location: ' . BASE_URL);
    exit;
}

// Process status change if admin or vendor
$message = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && 
    (SessionManager::isAdmin() || (SessionManager::isVendor() && $orderManager->isVendorInOrder($currentUserId, $orderId)))) {
    
    $newStatus = $_POST['status'];
    $validStatuses = ['pending', 'confirmed', 'ready', 'completed', 'cancelled'];
    
    if (in_array($newStatus, $validStatuses)) {
        $result = $orderManager->updateOrderStatus($orderId, $newStatus);
        
        if ($result) {
            $message = 'Order status updated successfully';
            $alertType = 'success';
            
            // Refresh order details
            $order = $orderManager->getOrderById($orderId);
        } else {
            $message = 'Failed to update order status';
            $alertType = 'danger';
        }
    }
}

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order #<?php echo $orderId; ?></h1>
        <?php if (SessionManager::isVendor()): ?>
            <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        <?php elseif (SessionManager::isCustomer()): ?>
            <a href="<?php echo BASE_URL; ?>presentation/customer/orders.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to My Orders
            </a>
        <?php elseif (SessionManager::isAdmin()): ?>
            <a href="<?php echo BASE_URL; ?>presentation/admin/orders.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Home
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Order Details Card -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Order Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?php if (SessionManager::isVendor() || SessionManager::isAdmin()): ?>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <?php endif; ?>
                    <p><strong>Order Date:</strong> <?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
                    <p><strong>Pickup Date:</strong> <?php echo date('F d, Y', strtotime($order['pickup_date'])); ?></p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Status:</strong> 
                        <span class="badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                    <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                </div>
            </div>
            
            <?php if (SessionManager::isVendor() || SessionManager::isAdmin()): ?>
                <hr>
                <form method="POST" action="" class="row g-3">
                    <div class="col-md-8">
                        <select name="status" class="form-select">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="ready" <?php echo $order['status'] == 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Order Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <?php if (SessionManager::isCustomer()): ?>
                            <th>Vendor</th>
                            <?php endif; ?>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): 
                            $product = $productManager->getProductById($item['product_id']);
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (isset($item['image']) && !empty($item['image'])): ?>
                                            <img src="<?php echo BASE_URL . 'assets/images/products/' . htmlspecialchars($item['image']); ?>" 
                                                class="me-2" alt="" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($item['product_name'] ?? "Product ID: {$item['product_id']}"); ?>
                                    </div>
                                </td>
                                <?php if (SessionManager::isCustomer()): ?>
                                <td><?php echo htmlspecialchars($product['vendor_name'] ?? 'Unknown Vendor'); ?></td>
                                <?php endif; ?>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-active">
                            <td colspan="<?php echo SessionManager::isCustomer() ? '4' : '3'; ?>" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?> 
