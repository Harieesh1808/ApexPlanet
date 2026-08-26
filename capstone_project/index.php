<?php
// index.php
require_once __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>Welcome to EduPortal</h1>
    <p>Empower your future with world-class online courses.</p>
    <?php if (!isLoggedIn()): ?>
        <a href="/task5/register.php" class="btn btn-primary" style="background-color: var(--secondary-color); font-size: 1.1rem; padding: 0.75rem 1.5rem;">Start Learning Today</a>
    <?php else: ?>
        <a href="/task5/courses.php" class="btn btn-primary" style="background-color: var(--secondary-color); font-size: 1.1rem; padding: 0.75rem 1.5rem;">Browse Courses</a>
    <?php endif; ?>
</div>

<div style="text-align: center; padding: 2rem 0;">
    <h2>Featured Categories</h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">Explore a variety of topics and find your passion.</p>
    
    <div style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); width: 250px;">
            <h3 style="color: var(--primary-color);">Web Development</h3>
            <p style="font-size: 0.9rem; margin-top: 0.5rem;">Learn to build responsive websites.</p>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); width: 250px;">
            <h3 style="color: var(--primary-color);">Data Science</h3>
            <p style="font-size: 0.9rem; margin-top: 0.5rem;">Master data analysis and ML.</p>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); width: 250px;">
            <h3 style="color: var(--primary-color);">Design</h3>
            <p style="font-size: 0.9rem; margin-top: 0.5rem;">UI/UX and creative arts.</p>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
