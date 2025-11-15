<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/ProductManager.php';
require_once '../../business/OrderManager.php';

// Get product ID from query parameters
$productId = intval($_GET['id'] ?? 0);

if ($productId <= 0) {
    header('Location: ' . BASE_URL . 'presentation/common/products.php');
    exit;
}

// Initialize managers
$productManager = new ProductManager();
$orderManager = new OrderManager();

// Get product details
$product = $productManager->getProductById($productId);

if (!$product) {
    header('Location: ' . BASE_URL . 'presentation/common/products.php');
    exit;
}

// Process add to cart/order
$message = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    // Check if user is logged in
    if (!SessionManager::isLoggedIn()) {
        $redirectUrl = BASE_URL . 'presentation/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']);
        header('Location: ' . $redirectUrl);
        exit;
    }
    
    $quantity = intval($_POST['quantity']);
    
    // Validate quantity
    if ($quantity <= 0) {
        $message = 'Please enter a valid quantity';
        $alertType = 'danger';
    } elseif ($quantity > $product['stock']) {
        $message = 'Not enough stock available';
        $alertType = 'danger';
    } else {
        // Create order
        $orderData = [
            'items' => [
                [
                    'product_id' => $product['id'],
                    'quantity' => $quantity
                ]
            ],
            'pickup_date' => date('Y-m-d', strtotime('+2 days'))
        ];
        
        $result = $orderManager->createOrder($orderData);
        
        if ($result['success']) {
            $message = 'Order placed successfully! Your total is $' . number_format($result['total_amount'], 2);
            $alertType = 'success';
            
            // Refresh product details to update stock
            $product = $productManager->getProductById($productId);
        } else {
            $message = $result['message'];
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
    
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-5 mb-4">
            <div class="card">
                <img src="<?php echo BASE_URL . 'assets/images/products/' . htmlspecialchars($product['image']); ?>" 
                     class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-md-7">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-primary"><?php echo htmlspecialchars($product['category']); ?></span>
                <span class="text-muted ms-2">
                    <i class="fas fa-user me-1"></i>Sold by: <?php echo htmlspecialchars($product['vendor_name']); ?>
                </span>
            </div>
            
            <h3 class="text-primary mb-3">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></h3>
            
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <div class="mb-4">
                <h5>Availability:</h5>
                <?php if ($product['stock'] > 0): ?>
                    <p class="text-success">
                        <i class="fas fa-check-circle me-1"></i>In Stock (<?php echo $product['stock']; ?> available)
                    </p>
                <?php else: ?>
                    <p class="text-danger">
                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                    </p>
                <?php endif; ?>
            </div>
            
            <?php if ($product['stock'] > 0): ?>
                <?php if (!SessionManager::isVendor() && !SessionManager::isAdmin()): ?>
                <form method="POST" action="" id="orderForm">
                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-auto">
                            <label for="quantity" class="col-form-label">Quantity:</label>
                        </div>
                        <div class="col-3">
                            <input type="number" id="quantity" name="quantity" class="form-control" 
                                   min="1" max="<?php echo $product['stock']; ?>" value="1">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-success" id="confirmOrderBtn">
                                <i class="fas fa-shopping-cart me-1"></i>Order Now
                            </button>
                        </div>
                    </div>
                </form>
                <?php elseif (SessionManager::isVendor()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>As a vendor, you can only view products. To place orders, please use a customer account.
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>As an admin, you can only view products. To place orders, please use a customer account.
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal fade" id="orderConfirmModal" tabindex="-1" aria-labelledby="orderConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="orderConfirmModalLabel">Confirm Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to order:</p>
                    <p><strong><span id="orderQuantity">1</span>x <?php echo htmlspecialchars($product['name']); ?></strong></p>
                    <p>Pickup date will be: <strong><?php echo date('F d, Y', strtotime('+2 days')); ?></strong></p>
                    <p>Total Price: <strong>$<span id="orderTotal"><?php echo number_format($product['price'], 2); ?></span></strong></p>
                    <p class="mt-3">Do you want to proceed with this order?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="placeOrderBtn">Place Order</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products Placeholder -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Related Products</h3>
            <div class="row">
                <?php 
                // Get products in the same category
                $relatedProducts = $productManager->searchProducts('', $product['category']);
                $count = 0;
                
                foreach ($relatedProducts as $relatedProduct) {
                    // Skip current product and limit to 3 related products
                    if ($relatedProduct['id'] == $product['id'] || $count >= 3) {
                        continue;
                    }
                    $count++;
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo BASE_URL . 'assets/images/products/' . htmlspecialchars($relatedProduct['image']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($relatedProduct['name']); ?></h5>
                                <p class="card-text text-primary fw-bold">
                                    $<?php echo htmlspecialchars(number_format($relatedProduct['price'], 2)); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <a href="<?php echo BASE_URL . 'presentation/common/product_detail.php?id=' . $relatedProduct['id']; ?>" 
                                   class="btn btn-outline-success w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?>

<script>
    // Order confirmation functionality
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const confirmOrderBtn = document.getElementById('confirmOrderBtn');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const orderForm = document.getElementById('orderForm');
        const orderQuantitySpan = document.getElementById('orderQuantity');
        const orderTotalSpan = document.getElementById('orderTotal');
        const productPrice = <?php echo $product['price']; ?>;
        const orderModal = new bootstrap.Modal(document.getElementById('orderConfirmModal'));
        
        // Update total when quantity changes
        quantityInput.addEventListener('change', updateOrderSummary);
        quantityInput.addEventListener('input', updateOrderSummary);
        
        function updateOrderSummary() {
            const quantity = parseInt(quantityInput.value) || 1;
            const total = (quantity * productPrice).toFixed(2);
            
            orderQuantitySpan.textContent = quantity;
            orderTotalSpan.textContent = total;
        }
        
        // Show confirmation modal when "Order Now" is clicked
        confirmOrderBtn.addEventListener('click', function() {
            updateOrderSummary();
            orderModal.show();
        });
        
        // Submit the form when "Place Order" is clicked
        placeOrderBtn.addEventListener('click', function() {
            orderForm.submit();
        });
    });
</script> 
