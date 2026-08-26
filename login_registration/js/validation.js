// Form validation logic
document.addEventListener('DOMContentLoaded', () => {

    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Utility: Show alert message
    const showAlert = (alertElement, message, type) => {
        alertElement.className = `alert alert-${type} alert-dismissible fade show`;
        alertElement.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertElement.classList.remove('d-none');
    };



    // Login Form Validation
    if (loginForm) {
        const loginAlert = document.getElementById('loginAlert');
        
        let loginCheckTimeout = null;
        let isDynamicValid = false;
        const loginEmail = document.getElementById('loginEmail');
        const loginPassword = document.getElementById('loginPassword');

        const checkLoginDynamic = () => {
            clearTimeout(loginCheckTimeout);
            const email = loginEmail.value;
            const password = loginPassword.value;
            
            // Remove HTML5 validation styles to avoid eye-button bug
            loginForm.classList.remove('was-validated');
            
            if (email.length > 3 && password.length > 0 && loginEmail.checkValidity()) {
                loginCheckTimeout = setTimeout(() => {
                    fetch('php/login-check.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: email, password: password })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loginEmail.classList.remove('is-invalid');
                            loginPassword.classList.remove('is-invalid');
                            loginEmail.classList.add('is-valid');
                            loginPassword.classList.add('is-valid');
                            isDynamicValid = true;
                        } else {
                            loginEmail.classList.remove('is-valid');
                            loginPassword.classList.remove('is-valid');
                            loginEmail.classList.add('is-invalid');
                            loginPassword.classList.add('is-invalid');
                            isDynamicValid = false;
                        }
                    });
                }, 500);
            } else {
                loginEmail.classList.remove('is-valid');
                loginPassword.classList.remove('is-valid');
                // Only show invalid if they typed something but it's not valid format
                if (email.length > 0 || password.length > 0) {
                     loginEmail.classList.add('is-invalid');
                     loginPassword.classList.add('is-invalid');
                } else {
                     loginEmail.classList.remove('is-invalid');
                     loginPassword.classList.remove('is-invalid');
                }
                isDynamicValid = false;
            }
        };

        loginEmail.addEventListener('input', checkLoginDynamic);
        loginPassword.addEventListener('input', checkLoginDynamic);

        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const email = loginEmail.value;
            const password = loginPassword.value;

            // If it's empty, force invalid UI and stop
            if (email.length === 0 || password.length === 0) {
                 loginEmail.classList.add('is-invalid');
                 loginPassword.classList.add('is-invalid');
                 showAlert(loginAlert, 'Email and password are required.', 'danger');
                 return;
            }
            
            const btn = loginForm.querySelector('button[type="submit"]');
            const originalBtnText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing in...';
            
            fetch('php/login-check.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email, password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem('userName', data.name);
                    localStorage.setItem('userEmail', email);
                    showAlert(loginAlert, 'Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 1500);
                } else {
                    loginEmail.classList.remove('is-valid');
                    loginPassword.classList.remove('is-valid');
                    loginEmail.classList.add('is-invalid');
                    loginPassword.classList.add('is-invalid');
                    
                    showAlert(loginAlert, data.message || 'Invalid credentials.', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                showAlert(loginAlert, 'An error occurred. Please try again.', 'danger');
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
            });
        });
    }

    // Register Form Validation
    if (registerForm) {
        const registerAlert = document.getElementById('registerAlert');
        const passInput = document.getElementById('registerPassword');
        const confirmPassInput = document.getElementById('registerConfirmPassword');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        const strengthContainer = document.getElementById('passwordStrengthContainer');

        // Check password match on input
        confirmPassInput.addEventListener('input', () => {
            if (confirmPassInput.value !== passInput.value) {
                confirmPassInput.setCustomValidity("Passwords don't match");
            } else {
                confirmPassInput.setCustomValidity('');
            }
        });
        
        // Update confirm validity when password changes
        passInput.addEventListener('input', () => {
            if (confirmPassInput.value && confirmPassInput.value !== passInput.value) {
                confirmPassInput.setCustomValidity("Passwords don't match");
            } else {
                confirmPassInput.setCustomValidity('');
            }
            
            // Password Strength logic
            const val = passInput.value;
            if (val.length > 0) {
                strengthContainer.style.display = 'block';
                let strength = 0;
                let text = '';
                let colorClass = '';
                
                if (val.length >= 8) strength += 25;
                if (val.match(/[a-z]+/)) strength += 25;
                if (val.match(/[A-Z]+/)) strength += 25;
                if (val.match(/[0-9]+/)) strength += 25;
                if (val.match(/[$@#&!]+/)) strength += 25;
                
                strength = Math.min(100, strength);
                
                if (strength <= 25) { text = 'Weak'; colorClass = 'bg-danger'; }
                else if (strength <= 50) { text = 'Fair'; colorClass = 'bg-warning'; }
                else if (strength <= 75) { text = 'Good'; colorClass = 'bg-info'; }
                else { text = 'Strong'; colorClass = 'bg-success'; }
                
                strengthBar.style.width = strength + '%';
                strengthBar.className = 'progress-bar ' + colorClass;
                strengthText.textContent = text;
            } else {
                strengthContainer.style.display = 'none';
            }
        });

        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Re-validate match just in case
            if (confirmPassInput.value !== passInput.value) {
                confirmPassInput.setCustomValidity("Passwords don't match");
            } else {
                confirmPassInput.setCustomValidity('');
            }
            
            let isValid = true;
            
            if (!registerForm.checkValidity()) {
                isValid = false;
            }
            
            // Check if email has custom invalidity from AJAX
            const emailInput = document.getElementById('registerEmail');
            if (emailInput.validationMessage === 'Email already in use') {
                isValid = false;
            }
            
            registerForm.classList.add('was-validated');
            
            if (isValid) {
                const btn = registerForm.querySelector('button[type="submit"]');
                const nameInput = document.getElementById('registerName');
                const originalBtnText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating account...';
                
                fetch('php/register-user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: nameInput.value, email: emailInput.value, password: passInput.value })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert(registerAlert, 'Account created successfully! Redirecting to login...', 'success');
                        setTimeout(() => {
                            window.location.href = 'login.html';
                        }, 1500);
                    } else {
                        showAlert(registerAlert, data.message || 'Registration failed.', 'danger');
                        btn.disabled = false;
                        btn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    showAlert(registerAlert, 'An error occurred during registration. Please try again.', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                });
            }
        });
    }

});
