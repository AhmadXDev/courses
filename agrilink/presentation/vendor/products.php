<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/ProductManager.php';

// Restrict access to vendors and admins
SessionManager::requireVendor();

// Initialize manager
$productManager = new ProductManager();

// Get current user ID
$userId = SessionManager::getCurrentUserId();

// Get low_stock filter
$lowStockFilter = isset($_GET['low_stock']) && $_GET['low_stock'] == '1';

// Get vendor's products
$products = $productManager->getProductsByVendor($userId);

// Apply low stock filter if specified
if ($lowStockFilter) {
    $products = array_filter($products, function($product) {
        return $product['stock'] <= 10; // Define "low stock" as 10 or fewer
    });
}

// Initialize message variables
$message = '';
$alertType = '';

// Check for session messages from product form submission
if (isset($_SESSION['product_message'])) {
    $message = $_SESSION['product_message'];
    $alertType = $_SESSION['product_alert_type'] ?? 'success';
    
    // Clear session messages to prevent them from showing again on refresh
    unset($_SESSION['product_message']);
    unset($_SESSION['product_alert_type']);
}

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $lowStockFilter ? 'Low Stock Products' : 'Manage Products'; ?></h1>
        <div>
            <a href="<?php echo BASE_URL; ?>presentation/vendor/product_form.php" class="btn btn-success me-2">
                <i class="fas fa-plus-circle me-2"></i>Add New Product
            </a>
            <a href="<?php echo BASE_URL; ?>presentation/common/dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Filter Options -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo BASE_URL; ?>presentation/vendor/products.php" 
                   class="btn <?php echo !$lowStockFilter ? 'btn-success' : 'btn-outline-success'; ?>">
                    All Products
                </a>
                <a href="<?php echo BASE_URL; ?>presentation/vendor/products.php?low_stock=1" 
                   class="btn <?php echo $lowStockFilter ? 'btn-warning' : 'btn-outline-warning'; ?>">
                    Low Stock Items
                </a>
            </div>
        </div>
    </div>
    
    <!-- Products List -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Your Products</h5>
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <p class="text-muted">
                    No products found. 
                    <a href="<?php echo BASE_URL; ?>presentation/vendor/product_form.php">Add your first product</a>.
                </p>
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
                            <?php foreach ($products as $product): ?>
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
                                           class="btn btn-sm btn-outline-success me-1">
                                            View
                                        </a>
                                        <a href="#" onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo addslashes(htmlspecialchars($product['name'])); ?>')" 
                                           class="btn btn-sm btn-outline-danger">
                                            Delete
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

<!-- Confirmation Modal for Delete -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the product: <span id="productNameToDelete"></span>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete Product</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(productId, productName) {
        document.getElementById('productNameToDelete').textContent = productName;
        document.getElementById('confirmDeleteBtn').href = '<?php echo BASE_URL; ?>presentation/vendor/product_delete.php?id=' + productId;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
        
        // Add the no-hover class when modal opens
        document.body.classList.add('modal-open-no-hover');
    }
    
    // Fix for modal hover issues
    document.addEventListener('DOMContentLoaded', function() {
        // Remove the class when modals are closed
        document.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open-no-hover');
        });
        
        // Move the delete confirmation modal to the end of body
        const deleteModal = document.getElementById('deleteConfirmModal');
        if (deleteModal) {
            document.body.appendChild(deleteModal);
        }
    });
</script> 
