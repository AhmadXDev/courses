<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/OrderManager.php';

// Restrict access to administrators only
SessionManager::requireAdmin();

// Initialize order manager
$orderManager = new OrderManager();

// Get status filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Get all orders
$orders = $orderManager->getAllOrders();

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
    </div>
    
    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Filter Orders</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="ready" <?php echo $statusFilter === 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                        <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No orders found with the selected filter.
        </div>
    <?php else: ?>
        <!-- Orders List -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">All Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Pickup Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['pickup_date'])); ?></td>
                                    <td>
                                        <span class="badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>presentation/common/order_detail.php?id=<?php echo $order['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?> 
