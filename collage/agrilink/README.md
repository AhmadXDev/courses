# AgriLink - Local Farmer's Market Online

AgriLink is a web platform that connects local farmers and artisans with customers in the community. The application is built using a 3-tier architecture with PHP, MySQL, and HTML/CSS/JavaScript.

## Project Structure

The application follows a 3-tier architecture:

- **Presentation Layer**: Contains all frontend pages and templates
- **Business Logic Layer**: Handles authentication, validation, and business rules
- **Data Access Layer**: Manages database connections and operations

```
/AgriLink
├── /presentation (HTML/CSS/JS/PHP views)
│   ├── /layouts (Reusable templates)
│   └── *.php (Individual pages)
├── /business (PHP logic scripts)
├── /data (PHP database access scripts)
├── /assets
│   ├── /css
│   ├── /js
│   └── /images
├── config.php (DB configuration)
├── index.php (Entry point)
├── agrilink.sql (Database schema)
└── README.md
```

## Features

- User authentication with role-based access control (customer, vendor, admin)
- Product browsing, searching, and filtering
- Vendor dashboard for managing products and orders
- Order placement and pickup scheduling
- Admin panel for managing all users, products, and orders
- Responsive mobile-first design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- Web browser with JavaScript enabled

## Installation

1. Clone the repository to your web server's document root or a subdirectory
2. Import the database schema:
   ```
   mysql -u username -p < agrilink.sql
   ```
3. Configure database connection in `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'agrilink');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```
4. Update the BASE_URL in `config.php` to match your server setup:
   ```php
   define('BASE_URL', 'http://localhost/AgriLink/');
   ```
5. Ensure the web server has write permissions to the `/assets/images/products` directory

## Test Accounts

The database includes the following test accounts:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@agrilink.com | password123 |
| Customer | john@example.com | password123 |
| Customer | jane@example.com | password123 |
| Vendor | contact@greenfarms.com | password123 |
| Vendor | info@freshdairy.com | password123 |

## Technologies Used

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **CSS Framework**: Bootstrap 5
- **Icons**: Font Awesome
- **JavaScript Libraries**: jQuery 