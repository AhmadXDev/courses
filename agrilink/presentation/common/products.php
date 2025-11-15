<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/ProductManager.php';

// Initialize product manager
$productManager = new ProductManager();

// Get search and filter parameters
$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';

// Get products based on search/filter
$products = [];
if (!empty($search)) {
    $products = $productManager->searchProducts($search, !empty($category) ? $category : null);
    $pageTitle = 'Search Results for "' . htmlspecialchars($search) . '"';
} else if (!empty($category)) {
    // Use searchProducts with empty keyword but with category for filtering
    $products = $productManager->searchProducts('', $category);
    $pageTitle = htmlspecialchars($category) . ' Products';
} else {
    $products = $productManager->getAllProducts();
    $pageTitle = 'All Products';
}

// Define all available categories instead of just getting from database
$allCategories = [
    'Vegetables',
    'Fruits',
    'Dairy & Eggs',
    'Meat',
    'Bakery',
    'Honey & Preserves',
    'Herbs & Spices',
    'Other'
];

// Get categories from database to merge with predefined list
$dbCategories = $productManager->getCategories();

// Combine database categories with predefined ones and remove duplicates
$categories = array_unique(array_merge($allCategories, $dbCategories));

// Sort categories but we'll handle "Other" specially
sort($categories);

// Remove "Other" if it exists in the array
$otherKey = array_search('Other', $categories);
if ($otherKey !== false) {
    unset($categories[$otherKey]);
}

// Add "Other" to the end
$categories[] = 'Other';

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>
    
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <?php if (!empty($search)): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Categories</label>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" 
                                            <?php echo $category === $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-md-9">
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No products found.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo BASE_URL . 'assets/images/products/' . htmlspecialchars($product['image']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($product['short_description']); ?></p>
                                    <p class="card-text text-primary fw-bold">
                                        $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?>
                                    </p>
                                    <p class="card-text text-muted small">
                                        <i class="fas fa-user me-1"></i>Sold by: <?php echo htmlspecialchars($product['vendor_name']); ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="<?php echo BASE_URL . 'presentation/common/product_detail.php?id=' . $product['id']; ?>" 
                                       class="btn btn-outline-success w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?> 
