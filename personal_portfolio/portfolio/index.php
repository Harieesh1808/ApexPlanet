<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | Home</title>
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
        <section class="hero">
            <div class="hero-content">
                <h1>Hi, I'm <span style="color: var(--primary);">Harieesh</span>.</h1>
                <p>I build modern, responsive, and dynamic web applications. Let's turn ideas into reality with elegant code and beautiful design.</p>
                <a href="projects.php" class="btn">View My Work</a>
            </div>
            <div class="hero-image">
                <img src="images/profile.png" alt="Harieesh's Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> MyPortfolio. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
