// AJAX for Email existence check
document.addEventListener('DOMContentLoaded', () => {
    
    const emailInput = document.getElementById('registerEmail');
    if (!emailInput) return;
    
    const emailHelp = document.getElementById('emailHelp');
    const emailStatusIcon = document.getElementById('emailStatusIcon');
    const emailFeedback = document.getElementById('emailFeedback');
    let typingTimer;
    const doneTypingInterval = 500; // wait 500ms after typing
    
    // Validate email format
    const isValidEmail = (email) => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    };

    emailInput.addEventListener('input', () => {
        clearTimeout(typingTimer);
        
        // Reset states
        emailInput.classList.remove('is-loading-email', 'email-available', 'is-invalid');
        emailStatusIcon.classList.add('d-none');
        emailStatusIcon.classList.remove('email-available');
        emailHelp.classList.add('d-none');
        emailInput.setCustomValidity('');
        emailFeedback.textContent = 'Please enter a valid email address.';
        
        if (emailInput.value && isValidEmail(emailInput.value)) {
            typingTimer = setTimeout(checkEmailAvailability, doneTypingInterval);
        }
    });

    const checkEmailAvailability = async () => {
        const email = emailInput.value;
        
        // Show loading state
        emailInput.classList.add('is-loading-email');
        emailHelp.textContent = 'Checking availability...';
        emailHelp.classList.remove('d-none', 'text-success', 'text-danger');
        emailHelp.classList.add('text-muted');
        
        try {
            // Using a relative path so it works when run from local server
            const response = await fetch(`php/check-user.php?email=${encodeURIComponent(email)}`);
            
            // If response is not ok (e.g. 404 because PHP is not running)
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            // Remove loading state
            emailInput.classList.remove('is-loading-email');
            
            if (data.exists) {
                // Email is taken
                emailInput.classList.add('is-invalid');
                emailInput.setCustomValidity('Email already in use');
                emailFeedback.textContent = 'This email is already registered.';
                emailHelp.classList.add('d-none');
            } else {
                // Email is available
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('email-available');
                emailInput.setCustomValidity('');
                
                emailStatusIcon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
                emailStatusIcon.classList.remove('d-none');
                emailStatusIcon.classList.add('email-available');
                
                emailHelp.textContent = 'Email is available!';
                emailHelp.classList.remove('text-muted');
                emailHelp.classList.add('text-success');
            }
            
        } catch (error) {
            console.error('Error checking email:', error);
            emailInput.classList.remove('is-loading-email');
            
            // Provide a mock fallback if PHP is not available (as requested by instructions)
            emailHelp.textContent = 'Server unavailable. Falling back to local validation.';
            
            // Mock logic: if email starts with 'admin', consider it taken
            if (email.toLowerCase().startsWith('admin')) {
                emailInput.classList.add('is-invalid');
                emailInput.setCustomValidity('Email already in use');
                emailFeedback.textContent = 'This email is already registered (Mock).';
                emailHelp.classList.add('d-none');
            } else {
                emailInput.classList.add('email-available');
                emailInput.setCustomValidity('');
                
                emailStatusIcon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
                emailStatusIcon.classList.remove('d-none');
                emailStatusIcon.classList.add('email-available');
            }
        }
    };

});
