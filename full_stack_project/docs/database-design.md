# Database Design: Task 4 Bookstore

## Overview
The database uses a normalized relational model designed for MySQL. It is normalized to the 3rd Normal Form (3NF) to eliminate data redundancy and ensure data integrity.

## Tables and Relationships

### 1. `roles`
Stores user roles to implement Role-Based Access Control (RBAC).
- `id` (PK)
- `role_name` (e.g., 'admin', 'user')

### 2. `users`
Stores user accounts.
- `id` (PK)
- `role_id` (FK -> `roles.id`)
- `name`
- `email` (UNIQUE)
- `password_hash` (Securely hashed using `password_hash()`)
- `profile_picture`
- `created_at`, `updated_at`

### 3. `categories`
Book genres.
- `id` (PK)
- `name` (UNIQUE)
- `description`
- `created_at`

### 4. `products`
The main catalog of books.
- `id` (PK)
- `category_id` (FK -> `categories.id`)
- `title`, `author`, `description`
- `price` (DECIMAL)
- `stock_quantity` (INT)
- `image`
- `created_at`, `updated_at`

### 5. `orders`
Stores customer orders and shipping details.
- `id` (PK)
- `user_id` (FK -> `users.id`)
- `total_amount`
- `status` (ENUM: Pending, Confirmed, Processing, Shipped, Delivered, Cancelled)
- `shipping_name`, `shipping_address`, `shipping_phone`
- `created_at`, `updated_at`

### 6. `order_items`
Line items for each order.
- `id` (PK)
- `order_id` (FK -> `orders.id` ON DELETE CASCADE)
- `product_id` (FK -> `products.id` ON DELETE RESTRICT)
- `quantity`, `unit_price`, `subtotal`
*Note: `unit_price` is stored here to preserve historical accuracy even if the product's current price changes.*

### 7. `password_resets`
Tokens for the forgot password workflow.
- `id` (PK)
- `user_id` (FK -> `users.id` ON DELETE CASCADE)
- `token_hash` (Secure SHA-256 hash of the generated token)
- `expires_at`, `used_at`, `created_at`

## Indexes
Indexes are strategically placed to improve read performance on frequent queries:
- `users.email`, `users.role_id`
- `products.category_id`, `products.title`
- `orders.user_id`, `orders.status`, `orders.created_at`
- `order_items.order_id`, `order_items.product_id`
