<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | About</title>
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
            <h2>About Me</h2>
            <div class="about-grid">
                <div class="card">
                    <h3>Education</h3>
                    <p><strong>Bachelor of Technology in Computer Science in Engineering</strong><br>
                    University of Technology, 2020 - 2024</p>
                    <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem;">Relevant Coursework: Web Development, Data Structures, Databases, UI/UX Design.</p>
                </div>
                
                <div class="card">
                    <h3>Skills</h3>
                    <ul class="skills-list">
                        <li>HTML5</li>
                        <li>CSS3</li>
                        <li>JavaScript</li>
                        <li>PHP</li>
                        <li>MySQL</li>
                        <li>Git</li>
                        <li>UI/UX</li>
                    </ul>
                </div>
                
                <div class="card">
                    <h3>Interests</h3>
                    <p style="color: var(--text-muted);">When I'm not coding, I enjoy:</p>
                    <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                        <li>Open Source Contributing</li>
                        <li>Photography</li>
                        <li>Reading Tech Blogs</li>
                        <li>Traveling</li>
                    </ul>
                </div>
            </div>
        </section>

        <section style="margin-top: 4rem;">
            <h2>Multimedia & Content</h2>
            <div class="card" style="text-align: center;">
                <h3 style="margin-bottom: 1rem;">My Favorite Tech Talk (Iframe)</h3>
                <iframe width="100%" height="315" src="https://www.youtube.com/embed/lKsvLGdoIH8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="border-radius: 12px; margin-bottom: 2rem;"></iframe>
                
                <h3 style="margin-bottom: 1rem;">Background Audio</h3>
                <audio controls style="margin-bottom: 2rem;">
                    <source src="images/demo_audio.mp3" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>

                <h3 style="margin-bottom: 1rem;">Demo Video</h3>
                <video width="100%" max-width="500px" controls style="border-radius: 12px;">
                    <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                    Your browser does not support HTML video.
                </video>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> MyPortfolio. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
