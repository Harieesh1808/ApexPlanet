# Task 4: Online Bookstore

## 1. Overview
This is a Real-World Full Stack PHP + MySQL Online Bookstore application. It provides a complete e-commerce workflow from user registration and product browsing to shopping cart management and order placement. It includes a robust admin panel for managing users, products, orders, and viewing analytics.

## 2. Problem Statement
Many bookstores lack a functional, secure, and user-friendly online storefront. This project aims to bridge that gap by providing a ready-to-deploy web application where bookstores can sell their inventory online.

## 3. Features
- **User Authentication**: Secure registration, login, logout, and password reset.
- **Product Catalog**: Browse books with search and filtering by category.
- **Shopping Cart**: Add books, update quantities, and calculate totals.
- **Checkout**: Place orders with real-time stock validation and database transactions.
- **User Dashboard**: Manage profile details and view order history.
- **Admin Dashboard**: Comprehensive analytics, order status management, and CRUD operations for users and products.

## 4. Roles
- **user**: Standard customers who can browse, purchase, and manage their profiles.
- **admin**: Store managers with access to the admin dashboard and full control over the catalog and orders.

## 5. Technologies
- **Backend**: PHP 8+
- **Database**: MySQL (MariaDB via XAMPP)
- **Frontend**: HTML5, Vanilla CSS, Vanilla JavaScript

## 6. Prerequisites
- XAMPP installed and running (Apache & MySQL).
- PHP version 8.0 or higher.

## 7. XAMPP Setup & Database Configuration
1. Start Apache and MySQL in the XAMPP Control Panel.
2. Clone or place this project folder (`task4`) into `C:\xampp\htdocs\`.
3. Open phpMyAdmin (`http://localhost/phpmyadmin`).
4. Create a new database named `task4_bookstore`.
5. Import the provided `database.sql` file to create all required tables and sample categories.
6. The database connection is configured in `config/database.php`. By default, it connects to `localhost` with the `root` user and no password. Update this file if your setup requires a password.

## 8. Admin Setup
You can register a new user on the frontend and manually change their `role_id` to `1` in the `users` table via phpMyAdmin to grant admin privileges.

## 9. URL
Access the application at: `http://localhost/task4/`

## 10. Folder Structure
- `admin/`: Admin panel scripts.
- `assets/`: CSS, JS, and Images.
- `config/`: Database and application configuration.
- `docs/`: Requirements, wireframes, and documentation.
- `includes/`: Reusable components (header, footer, auth checks).
- `uploads/`: User-uploaded profile pictures and product images.

## 11. Testing & Verification
The application includes robust server-side validation and security measures:
- SQL Injection prevented via PDO Prepared Statements.
- Passwords are securely hashed using `password_hash()`.
- Session fixation prevented by regenerating session IDs.
- XSS mitigated by escaping user inputs on display using `htmlspecialchars()`.
- Access control enforced on all admin routes.
