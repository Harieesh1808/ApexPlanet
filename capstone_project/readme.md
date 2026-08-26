# Capstone Project: Learning Management System (LMS)

This project is a PHP-based web application that serves as a platform for online courses and learning.

## Features

*   **User Authentication**: Registration, Login, Logout, and OTP verification.
*   **Dashboard**: Personalized user dashboard (`dashboard.php`).
*   **Course Browsing**: View available courses (`courses.php`) and detailed course information (`course_details.php`).
*   **Enrollment**: Enroll in courses (`enroll.php`).
*   **Learning Interface**: Access course materials and lessons (`learn.php`).

## Technologies

*   PHP
*   MySQL (Database schema available in `database.sql`)
*   HTML/CSS/JavaScript (Frontend)

## Setup Instructions

1.  Clone the repository or download the source code.
2.  Set up a local web server environment (e.g., XAMPP, WAMP, or MAMP).
3.  Create a new MySQL database.
4.  Import the provided `database.sql` file into your newly created database.
5.  Configure the database connection settings (likely in the `config/` or `includes/` directory).
6.  Run `seed_courses.php` and `seed_lessons.php` to populate the database with initial sample data.
7.  Access the application via your local web server (e.g., `http://localhost/capstone_project`).

## File Structure Highlights

*   `assets/`: Contains CSS, JavaScript, and image files.
*   `config/`: Configuration files (e.g., database connection).
*   `includes/`: Reusable components (e.g., headers, footers).
*   `docs/`: Additional documentation.
*   Root directory contains the main PHP scripts for various application features.
