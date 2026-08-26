<?php
session_start();
$message = '';
$msgType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if db.php exists and include it
    if(file_exists('includes/db.php')){
        require 'includes/db.php';
        
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $userMessage = trim($_POST['message']);
        
        // Basic server-side validation
        if (empty($name) || empty($email) || empty($userMessage)) {
            $message = "All fields are required.";
            $msgType = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
            $msgType = "error";
        } else {
            // Prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $name, $email, $userMessage);
                if ($stmt->execute()) {
                    $message = "Thank you! Your message has been sent successfully.";
                    $msgType = "success";
                } else {
                    $message = "Oops! Something went wrong. Please try again later.";
                    $msgType = "error";
                }
                $stmt->close();
            } else {
                $message = "Database error. Please try again later.";
                $msgType = "error";
            }
        }
    } else {
        $message = "Database configuration is missing. Cannot submit form.";
        $msgType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | Contact</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">MyPortfolio</a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Contact Me</h2>
            <div class="contact-container card">
                
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $msgType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form id="contactForm" method="POST" action="contact.php">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
                        <div id="nameError" class="error-msg"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
                        <div id="emailError" class="error-msg"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Verification Password (Demo)</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter at least 6 characters" required>
                        <div id="passwordError" class="error-msg"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" placeholder="Hello, I would like to discuss..." required></textarea>
                        <div id="messageError" class="error-msg"></div>
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">Send Message</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> MyPortfolio. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
