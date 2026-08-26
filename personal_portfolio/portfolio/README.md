# Personal Portfolio Website

This is a dynamic, responsive personal portfolio website built using core web technologies. It features a modern design system, interactive elements, client-side validation, and a fully functional PHP/MySQL backend for a contact form and an admin panel to manage messages (CRUD operations).

## Technologies Used

- **Frontend**: HTML5, CSS3 (Vanilla CSS, Flexbox, Animations), JavaScript (DOM Manipulation, Events, Validation)
- **Backend**: PHP 
- **Database**: MySQL

## Features

- **Responsive Design**: Works flawlessly across mobile, tablet, and desktop screens.
- **Premium UI/UX**: Includes glassmorphism, gradients, micro-interactions, hover effects, and a dynamic animated background.
- **Dynamic Content**: Uses PHP to render dynamic components and handle form submissions.
- **Contact Form**: Includes both client-side (JavaScript) and server-side (PHP) validation.
- **Admin Panel**: Complete CRUD (Create, Read, Update, Delete) functionality to manage contact messages securely from the database.
- **Security**: Uses PHP Prepared Statements to prevent SQL Injection attacks.

## Project Structure

```
portfolio/
├── index.php         # Home page showcasing introduction
├── about.php         # Skills, education, and interests
├── projects.php      # Showcase of personal projects
├── contact.php       # Contact form with server-side processing
├── admin.php         # Admin dashboard for CRUD operations on messages
├── database.sql      # SQL script to initialize the database
├── css/
│   └── style.css     # Premium styling, animations, and responsive layout
├── js/
│   └── script.js     # Client-side form validation and interactivity
├── images/           # Directory for project/profile images
└── includes/
    └── db.php        # Database connection configuration
```

## Setup Instructions

To run this project locally, you will need a local web server environment such as [XAMPP](https://www.apachefriends.org/index.html).

### 1. Environment Setup
1. Install XAMPP and open the XAMPP Control Panel.
2. Start the **Apache** and **MySQL** modules.

### 2. Project Installation
1. Clone this repository or copy the `portfolio` directory.
2. Paste the `portfolio` folder into your XAMPP `htdocs` directory (typically `C:\xampp\htdocs\portfolio`).

### 3. Database Configuration (Using MySQL Workbench)
1. Ensure your XAMPP MySQL server is running.
2. Open **MySQL Workbench** and connect to your local MySQL instance (usually `localhost:3306`, user: `root`, no password).
3. Go to **File -> Open SQL Script...** and select the `database.sql` file located in the root of the project directory.
4. Click the lightning bolt icon (Execute) to run the script. This will automatically create the `portfolio_db` database and the `contacts` table.

*(Note: The database connection credentials are in `includes/db.php`. It uses the default XAMPP credentials: user `root` with an empty password.)*

### 4. Running the Application
- **Main Site**: Navigate to `http://localhost/portfolio/` to view the website.
- **Admin Panel**: Navigate to `http://localhost/portfolio/admin.php` to view, edit, or delete messages received through the contact form.

## Author

Developed as a foundational learning project demonstrating a full-stack web application built from scratch without frameworks.
