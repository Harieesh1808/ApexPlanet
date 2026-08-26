<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | Projects</title>
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
            <h2>My Projects</h2>
            <div class="projects-grid">
                
                <div class="project-card">
                    <div class="project-image" style="padding: 0;">
                        <img src="images/project_1.png" alt="Project 1" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="project-info">
                        <h3>E-Commerce Platform</h3>
                        <div class="tech-stack">PHP, MySQL, HTML, CSS, JS</div>
                        <p style="color: var(--text-muted); font-size: 0.95rem;">A fully functional e-commerce website with product listing, shopping cart, and user authentication.</p>
                    </div>
                </div>

                <div class="project-card">
                    <div class="project-image" style="padding: 0;">
                        <img src="images/project_2.png" alt="Project 2" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="project-info">
                        <h3>Task Management App</h3>
                        <div class="tech-stack">JavaScript, LocalStorage, CSS Flexbox</div>
                        <p style="color: var(--text-muted); font-size: 0.95rem;">A responsive to-do application allowing users to track their daily tasks with dynamic filtering.</p>
                    </div>
                </div>

                <div class="project-card">
                    <div class="project-image" style="padding: 0;">
                        <img src="images/project_3.png" alt="Project 3" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="project-info">
                        <h3>Portfolio Website</h3>
                        <div class="tech-stack">HTML5, CSS3, PHP, MySQL</div>
                        <p style="color: var(--text-muted); font-size: 0.95rem;">The current website you are viewing! Built from scratch using raw web technologies to showcase my skills.</p>
                    </div>
                </div>
                
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> MyPortfolio. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
