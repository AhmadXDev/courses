<?php
require_once 'config.php';
require_once 'business/SessionManager.php';
require_once 'presentation/layouts/header.php';
require_once 'presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <div class="jumbotron bg-light p-4 rounded">
        <h1 class="display-4">Welcome to AgriLink</h1>
        <p class="lead">Connecting local farmers and artisans with customers in your community.</p>
        <hr class="my-4">
        <p>Browse fresh produce and handmade goods from local vendors, place orders, and schedule pickups at local markets.</p>
        <a class="btn btn-primary btn-lg" href="presentation/common/products.php" role="button">Browse Products</a>
    </div>

    <div class="row mt-4">
        <h2 class="mb-4">Featured Products</h2>
        <?php
        require_once 'business/ProductManager.php';
        $productManager = new ProductManager();
        $featuredProducts = $productManager->getFeaturedProducts();

        foreach ($featuredProducts as $product) {
            echo '<div class="col-md-4 mb-4">';
            echo '<div class="card h-100">';
            echo '<img src="assets/images/products/' . htmlspecialchars($product['image']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';
            echo '<p class="card-text">' . htmlspecialchars($product['short_description']) . '</p>';
            echo '<p class="card-text text-primary">$' . htmlspecialchars(number_format($product['price'], 2)) . '</p>';
            echo '<a href="presentation/common/product_detail.php?id=' . htmlspecialchars($product['id']) . '" class="btn btn-outline-primary">View Details</a>';
            echo '</div></div></div>';
        }
        ?>
    </div>
</main>

<?php require_once 'presentation/layouts/footer.php'; ?> 