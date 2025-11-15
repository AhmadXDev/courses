# AgriLink Presentation Layer

This directory contains all the presentation layer (UI) files for the AgriLink application.

## Directory Structure

The presentation layer is organized into the following directories:

- **admin/**: Admin-specific pages for managing users, products, and orders
  - product_delete.php - Handler for deleting products
  - product_form.php - Form for adding/editing products 
  - products.php - Admin product management page
  - orders.php - Admin order management page

- **auth/**: Authentication-related pages
  - login.php - User login page
  - logout.php - User logout handler
  - register.php - User registration page

- **common/**: Pages that are accessible to all user types
  - dashboard.php - Dashboard page for logged-in users
  - order_detail.php - Order details page
  - product_detail.php - Product details page
  - products.php - Product catalog/listing page

- **customer/**: Customer-specific pages
  - orders.php - Customer order history page

- **vendor/**: Vendor-specific pages
  - orders.php - Vendor order management page
  - product_form.php - Vendor product add/edit form
  - products.php - Vendor product management page

- **layouts/**: Reusable layout components
  - header.php - Common header with meta tags and CSS includes
  - navigation.php - Main navigation bar
  - footer.php - Common footer with JavaScript includes

## URL Structure

The URL structure for the application follows this pattern:

`BASE_URL + 'presentation/' + {user_role} + '/' + {resource}.php`

For example:
- Admin product management: `/presentation/admin/products.php`
- Vendor order management: `/presentation/vendor/orders.php`
- Public product listing: `/presentation/common/products.php` 