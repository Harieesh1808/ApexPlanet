document.addEventListener('DOMContentLoaded', () => {
    const userName = localStorage.getItem('userName');
    const userEmail = localStorage.getItem('userEmail');

    if (userName && userEmail) {
        // Redirect to index if trying to access auth pages while logged in
        if (window.location.pathname.endsWith('login.html') || window.location.pathname.endsWith('register.html')) {
            window.location.href = 'index.html';
            return;
        }

        // User is logged in
        
        // 1. Update Navbar
        // Find the navbar list
        const navbarNav = document.querySelector('#navbarNav ul');
        if (navbarNav) {
            // Remove Login and Sign Up buttons
            // They are the last two list items
            const navItems = navbarNav.querySelectorAll('li.nav-item');
            
            navItems.forEach(item => {
                const link = item.querySelector('a');
                if (link && (link.textContent.trim() === 'Login' || link.textContent.trim() === 'Sign Up')) {
                    item.remove();
                }
            });

            // Add Logout button
            const logoutLi = document.createElement('li');
            logoutLi.className = 'nav-item ms-lg-3';
            logoutLi.innerHTML = `<button id="logoutBtn" class="btn btn-outline-danger px-4 w-100">Logout</button>`;
            navbarNav.appendChild(logoutLi);

            // Handle Logout Click
            document.getElementById('logoutBtn').addEventListener('click', () => {
                localStorage.removeItem('userName');
                localStorage.removeItem('userEmail');
                window.location.href = 'index.html';
            });
        }

        // 2. Personalize Home Page Hero (only on index.html)
        if (window.location.pathname.endsWith('index.html') || window.location.pathname === '/' || window.location.pathname === '') {
            const heroHeading = document.querySelector('main h1.display-4.fw-bold');
            if (heroHeading) {
                // Change "Secure & Seamless Authentication" to "Hi <name>, welcome back!"
                // Keep the span for styling if desired, but simple text replacement is easiest.
                heroHeading.innerHTML = `Hi <span class="text-primary">${userName}</span>, welcome back!`;
            }
            
            const heroSubtitle = document.querySelector('main p.lead.text-muted');
            if (heroSubtitle) {
                heroSubtitle.textContent = `You are successfully logged in as ${userEmail}. Experience our seamless auth flows.`;
            }
            
            // 3. Hide CTA buttons on main page
            const heroButtons = document.getElementById('hero-cta-buttons');
            if (heroButtons) {
                heroButtons.style.display = 'none';
                heroButtons.classList.remove('d-flex');
            }
        }
    }

    // Global Caps Lock Detector (works on all pages)
    const checkCapsLock = (e) => {
        if (e.getModifierState && e.getModifierState('CapsLock')) {
            document.querySelectorAll('[id^="capsLockWarning-"]').forEach(el => el.classList.remove('d-none'));
        } else {
            document.querySelectorAll('[id^="capsLockWarning-"]').forEach(el => el.classList.add('d-none'));
        }
    };
    document.addEventListener('keydown', checkCapsLock);
    document.addEventListener('keyup', checkCapsLock);
});
