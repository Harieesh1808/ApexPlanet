# Online Bookstore Requirements

## 1. Overview
The Online Bookstore is a web-based application built using PHP and MySQL. It provides a platform for users to browse, search, and purchase books online, while administrators can manage the inventory, users, and orders through a dedicated dashboard.

## 2. Problem Statement
Many small bookstores lack an online presence, limiting their reach. This application provides a complete, easy-to-use e-commerce solution tailored for bookstores, enabling them to digitize their catalog and accept online orders.

## 3. Target Users
- **Customers**: Individuals looking to browse and purchase books.
- **Administrators**: Store owners or staff members managing the catalog, inventory, and order fulfillment.

## 4. User Roles
- **user**: Standard customer account.
- **admin**: Administrative account with elevated privileges.

## 5. Functional Requirements
### Customer Features
- Register for a new account.
- Log in and log out securely.
- Reset a forgotten password via a secure token.
- Browse the book catalog.
- Search for books by title, author, or category.
- Filter books by category, price range, and availability.
- View detailed information about a specific book.
- Add books to a shopping cart and manage quantities.
- Place an order and proceed to checkout.
- View a history of past orders.
- Edit profile information and change passwords.

### Admin Features
- Secure admin login.
- View a dashboard with key metrics (total users, total orders, revenue, pending orders).
- Manage user accounts (view, edit, delete).
- Manage the book catalog (add, edit, delete books; upload images; manage stock).
- Manage categories.
- Manage orders (view details, update status).
- View analytics and sales reports.

## 6. Non-functional Requirements
- **Security**: Prevention of SQL injection (via prepared statements), XSS (via output escaping), CSRF (via tokens). Secure password hashing using `password_hash()`.
- **Responsive Design**: The UI must be usable on desktop, tablet, and mobile devices.
- **Performance**: Efficient database queries and pagination for large datasets.
- **Usability**: Intuitive navigation and clear feedback messages for user actions.

## 7. Major Modules
- Authentication & Authorization
- Product/Catalog Management
- Shopping Cart & Checkout
- Order Management
- User Profile Management
- Admin Dashboard & Analytics

## 8. Database Entities
- **roles**: Defines user roles (admin, user).
- **users**: Stores user account details and credentials.
- **categories**: Book categories/genres.
- **products**: The book catalog.
- **orders**: Customer orders and shipping details.
- **order_items**: Individual books within an order.
- **password_resets**: Tokens for the forgot password flow.

## 9. Security Requirements
- All user input must be validated server-side.
- Database credentials and sensitive configuration must not be exposed.
- Authentication sessions must be securely managed (`session_regenerate_id()`).
- File uploads (e.g., profile pictures, book covers) must be strictly validated for type (MIME) and size.

## 10. Future Improvements
- Integration with a payment gateway (e.g., Stripe, PayPal).
- Email notifications for order status updates.
- Product reviews and ratings.
- Wishlist functionality.
