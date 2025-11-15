<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/ProductManager.php';
require_once '../../business/UserManager.php';

// Restrict access to admins only
SessionManager::requireAdmin();

// Initialize managers
$productManager = new ProductManager();
$userManager = new UserManager();

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$vendorId = isset($_GET['vendor']) ? intval($_GET['vendor']) : 0;
$featured = isset($_GET['featured']) && $_GET['featured'] == '1';
$lowStock = isset($_GET['low_stock']) && $_GET['low_stock'] == '1';

// Check for success or error messages
$message = '';
$alertType = '';

if (isset($_GET['success'])) {
    $message = $_GET['success'];
    $alertType = 'success';
} elseif (isset($_GET['error'])) {
    $message = $_GET['error'];
    $alertType = 'danger';
}

// Get all products
$products = $productManager->getAllProducts();

// Get all vendors for filter dropdown
$vendors = $userManager->getVendors();

// Apply filters
if (!empty($category)) {
    $products = array_filter($products, function($product) use ($category) {
        return $product['category'] === $category;
    });
}

if ($vendorId > 0) {
    $products = array_filter($products, function($product) use ($vendorId) {
        return $product['vendor_id'] === $vendorId;
    });
}

if ($featured) {
    $products = array_filter($products, function($product) {
        return $product['is_featured'] == true;
    });
}

if ($lowStock) {
    $products = array_filter($products, function($product) {
        return $product['stock'] <= 10; // Define "low stock" as 10 or fewer
    });
}

// Get unique categories for filter dropdown
$categories = [];
foreach ($productManager->getAllProducts() as $product) {
    if (!in_array($product['category'], $categories)) {
        $categories[] = $product['category'];
    }
}
sort($categories);

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin - Manage Products</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Home
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
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Filter Products</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <!-- Category Filter -->
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Vendor Filter -->
                <div class="col-md-3">
                    <label for="vendor" class="form-label">Vendor</label>
                    <select class="form-select" id="vendor" name="vendor">
                        <option value="0">All Vendors</option>
                        <?php foreach ($vendors as $vendor): ?>
                            <option value="<?php echo $vendor['id']; ?>" 
                                    <?php echo $vendorId === $vendor['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($vendor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Featured Filter -->
                <div class="col-md-3">
                    <label class="form-label d-block">Featured</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                               <?php echo $featured ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="featured">Featured Only</label>
                    </div>
                </div>
                
                <!-- Low Stock Filter -->
                <div class="col-md-3">
                    <label class="form-label d-block">Stock</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="low_stock" name="low_stock" value="1"
                               <?php echo $lowStock ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="low_stock">Low Stock Only</label>
                    </div>
                </div>
                
                <!-- Filter/Reset Buttons -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <a href="<?php echo BASE_URL; ?>presentation/admin/products.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-undo me-2"></i>Reset Filters
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products List -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Products (<?php echo count($products); ?>)</h5>
            <a href="<?php echo BASE_URL; ?>presentation/admin/product_form.php" class="btn btn-sm btn-light">
                <i class="fas fa-plus-circle me-2"></i>Add New Product
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <p class="text-muted">No products found matching your criteria.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Vendor</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo BASE_URL . 'assets/images/products/' . htmlspecialchars($product['image']); ?>" 
                                                 class="me-2" alt="" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <span class="<?php echo $product['stock'] <= 10 ? 'text-danger fw-bold' : ''; ?>">
                                            <?php echo $product['stock']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['vendor_name']); ?></td>
                                    <td>
                                        <?php if ($product['is_featured']): ?>
                                            <span class="badge bg-success">Featured</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Featured</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo BASE_URL; ?>presentation/admin/product_form.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>presentation/common/product_detail.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $product['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $product['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the product: <strong><?php echo htmlspecialchars($product['name']); ?></strong>?</p>
                                                        <p class="text-danger">This action cannot be undone!</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <a href="<?php echo BASE_URL; ?>presentation/admin/product_delete.php?id=<?php echo $product['id']; ?>" 
                                                           class="btn btn-danger">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

<script>
    // Fix for modal hover issues
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all modal triggers
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            trigger.addEventListener('click', function() {
                // Add a class to the body when a modal is opened
                document.body.classList.add('modal-open-no-hover');
            });
        });
        
        // Remove the class when modals are closed
        document.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open-no-hover');
        });
        
        // Move all modals to the end of body to prevent parent hover effects
        const modals = Array.from(document.querySelectorAll('.modal'));
        modals.forEach(modal => {
            document.body.appendChild(modal);
        });
    });
</script>

<?php require_once '../../presentation/layouts/footer.php'; ?> 
