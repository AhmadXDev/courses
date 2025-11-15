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

// Get all vendors for dropdown
$vendors = $userManager->getVendors();

// Check if editing existing product
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$isEditing = $productId > 0;

// Default empty product
$product = [
    'id' => 0,
    'name' => '',
    'description' => '',
    'short_description' => '',
    'price' => '',
    'category' => '',
    'stock' => '',
    'image' => 'default.jpg',
    'vendor_id' => '',
    'is_featured' => false
];

// If editing, get product details
if ($isEditing) {
    $loadedProduct = $productManager->getProductById($productId);
    
    // Check if product exists
    if (!$loadedProduct) {
        header('Location: ' . BASE_URL . 'presentation/admin/products.php');
        exit;
    }
    
    $product = $loadedProduct;
}

// Process form submission
$message = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $productData = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'short_description' => $_POST['short_description'] ?? '',
        'price' => floatval($_POST['price'] ?? 0),
        'category' => $_POST['category'] ?? '',
        'stock' => intval($_POST['stock'] ?? 0),
        'vendor_id' => intval($_POST['vendor_id'] ?? 0),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0
    ];
    
    // Validate required fields
    $errors = [];
    
    if (empty($productData['name'])) {
        $errors[] = 'Product name is required';
    }
    
    if (empty($productData['description'])) {
        $errors[] = 'Description is required';
    }
    
    if (empty($productData['short_description'])) {
        // Generate short description from full description
        $productData['short_description'] = substr($productData['description'], 0, 100) . '...';
    }
    
    if ($productData['price'] <= 0) {
        $errors[] = 'Price must be greater than zero';
    }
    
    if (empty($productData['category'])) {
        $errors[] = 'Category is required';
    }
    
    if ($productData['stock'] < 0) {
        $errors[] = 'Stock cannot be negative';
    }
    
    if ($productData['vendor_id'] <= 0) {
        $errors[] = 'Vendor is required';
    }
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $uploadDir = '../../assets/images/products/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        // Check if image
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($imageFileType, $validExtensions)) {
            $errors[] = 'Only JPG, JPEG, PNG & GIF files are allowed';
        } elseif ($_FILES['image']['size'] > 5000000) { // 5MB max
            $errors[] = 'File is too large (max 5MB)';
        } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $productData['image'] = $fileName;
        } else {
            $errors[] = 'Error uploading file';
        }
    } else {
        // Keep existing image when editing
        if ($isEditing) {
            $productData['image'] = $product['image'];
        }
    }
    
    // If no errors, save product
    if (empty($errors)) {
        if ($isEditing) {
            $result = $productManager->updateProduct($productId, $productData);
            $successMessage = 'Product updated successfully';
        } else {
            $result = $productManager->createProduct($productData);
            $successMessage = 'Product created successfully';
        }
        
        if ($result) {
            $message = $successMessage;
            $alertType = 'success';
            
            // Redirect to products list after successful save
            header('Location: ' . BASE_URL . 'presentation/admin/products.php');
            exit;
        } else {
            $message = 'Error saving product';
            $alertType = 'danger';
        }
    } else {
        $message = 'Please fix the following errors: ' . implode(', ', $errors);
        $alertType = 'danger';
        
        // Keep submitted data on error
        $product = array_merge($product, $productData);
    }
}

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $isEditing ? 'Edit Product' : 'Add New Product'; ?></h1>
        <a href="<?php echo BASE_URL; ?>presentation/admin/products.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Product Information</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column - Basic Info -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name*</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="vendor_id" class="form-label">Vendor*</label>
                            <select class="form-select" id="vendor_id" name="vendor_id" required>
                                <option value="" disabled <?php echo empty($product['vendor_id']) ? 'selected' : ''; ?>>
                                    Select Vendor
                                </option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo $vendor['id']; ?>" 
                                            <?php echo $product['vendor_id'] == $vendor['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vendor['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <input type="text" class="form-control" id="short_description" name="short_description" 
                                   value="<?php echo htmlspecialchars($product['short_description']); ?>"
                                   placeholder="Brief summary (100 chars)">
                            <div class="form-text">Leave blank to auto-generate from description</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Full Description*</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price ($)*</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo htmlspecialchars($product['price']); ?>" 
                                       min="0.01" step="0.01" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">Stock*</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       value="<?php echo htmlspecialchars($product['stock']); ?>" 
                                       min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category*</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="" disabled <?php echo empty($product['category']) ? 'selected' : ''; ?>>
                                    Select Category
                                </option>
                                <option value="Vegetables" <?php echo $product['category'] === 'Vegetables' ? 'selected' : ''; ?>>
                                    Vegetables
                                </option>
                                <option value="Fruits" <?php echo $product['category'] === 'Fruits' ? 'selected' : ''; ?>>
                                    Fruits
                                </option>
                                <option value="Dairy & Eggs" <?php echo $product['category'] === 'Dairy & Eggs' ? 'selected' : ''; ?>>
                                    Dairy & Eggs
                                </option>
                                <option value="Meat" <?php echo $product['category'] === 'Meat' ? 'selected' : ''; ?>>
                                    Meat
                                </option>
                                <option value="Bakery" <?php echo $product['category'] === 'Bakery' ? 'selected' : ''; ?>>
                                    Bakery
                                </option>
                                <option value="Honey & Preserves" <?php echo $product['category'] === 'Honey & Preserves' ? 'selected' : ''; ?>>
                                    Honey & Preserves
                                </option>
                                <option value="Herbs & Spices" <?php echo $product['category'] === 'Herbs & Spices' ? 'selected' : ''; ?>>
                                    Herbs & Spices
                                </option>
                                <option value="Other" <?php echo $product['category'] === 'Other' ? 'selected' : ''; ?>>
                                    Other
                                </option>
                            </select>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured"
                                   <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_featured">Feature on homepage</label>
                        </div>
                    </div>
                    
                    <!-- Right Column - Image -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            
                            <?php if (!empty($product['image'])): ?>
                                <div class="mb-3">
                                    <img src="<?php echo BASE_URL . 'assets/images/products/' . htmlspecialchars($product['image']); ?>"
                                         class="img-thumbnail mb-2" style="max-height: 200px;">
                                    <div class="form-text">Current image: <?php echo htmlspecialchars($product['image']); ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" class="form-control" id="image" name="image"
                                   accept="image/jpeg,image/png,image/gif,image/jpg">
                            <div class="form-text">
                                <?php echo $isEditing ? 'Upload new image or keep existing' : 'Recommended size: 800x600 px'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end">
                    <a href="<?php echo BASE_URL; ?>presentation/admin/products.php" class="btn btn-outline-secondary me-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i><?php echo $isEditing ? 'Update Product' : 'Create Product'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?> 
