<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/OrderManager.php';

// Restrict access to vendors and admins
SessionManager::requireVendor();

// Initialize manager
$orderManager = new OrderManager();

// Get current user ID
$userId = SessionManager::getCurrentUserId();

// Get status filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Get vendor's orders
$orders = $orderManager->getOrdersByVendor($userId);

// Apply status filter if specified
if (!empty($statusFilter)) {
    $orders = array_filter($orders, function($order) use ($statusFilter) {
        return $order['status'] === $statusFilter;
    });
}

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Orders</h1>
        <a href="<?php echo BASE_URL; ?>presentation/dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
    
    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php" 
                   class="btn <?php echo empty($statusFilter) ? 'btn-success' : 'btn-outline-success'; ?>">
                    All Orders
                </a>
                <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php?status=pending" 
                   class="btn <?php echo $statusFilter === 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                    Pending
                </a>
                <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php?status=confirmed" 
                   class="btn <?php echo $statusFilter === 'confirmed' ? 'btn-info' : 'btn-outline-info'; ?>">
                    Confirmed
                </a>
                <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php?status=ready" 
                   class="btn <?php echo $statusFilter === 'ready' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Ready
                </a>
                <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php?status=completed" 
                   class="btn <?php echo $statusFilter === 'completed' ? 'btn-success' : 'btn-outline-success'; ?>">
                    Completed
                </a>
                <a href="<?php echo BASE_URL; ?>presentation/vendor/orders.php?status=cancelled" 
                   class="btn <?php echo $statusFilter === 'cancelled' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                    Cancelled
                </a>
            </div>
        </div>
    </div>
    
    <!-- Orders List -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <?php echo empty($statusFilter) ? 'All Orders' : ucfirst($statusFilter) . ' Orders'; ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($orders)): ?>
                <p class="text-muted">No orders found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Pickup Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['pickup_date'])); ?></td>
                                    <td><?php echo count($order['items']); ?> products</td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
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
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?> 
