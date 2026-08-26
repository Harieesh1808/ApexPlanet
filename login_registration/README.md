# AntiAuth - Login & Registration UI

## Project Objective
A polished, responsive, mobile-first Login and Registration web interface built using HTML5, CSS3, JavaScript, and Bootstrap 5. 

## Technologies Used
- HTML5
- CSS3 (Custom Styles + CSS Variables)
- Bootstrap 5 (CSS & JS components)
- Bootstrap Icons
- Google Fonts (Inter)
- JavaScript (Vanilla ES6)
- PHP (For dummy AJAX endpoint)

## Features Implemented
1. **Responsive Navbar**: With mobile collapse and active states.
2. **Custom CSS System**: Defined color palette, transitions, and hover effects overriding standard Bootstrap.
3. **Form Validation**: Client-side HTML5 validation enhanced with custom JavaScript to show user-friendly messages and styles.
4. **Password Strength & Match**: The registration form shows password strength and validates that the confirm password field matches.
5. **Show/Hide Password**: Custom icon toggle to reveal or hide password input.
6. **AJAX Email Check**: The registration form validates if the email already exists using `fetch()` and a PHP endpoint (with a JS mock fallback if the PHP server is offline).

## Folder Structure
```
project/
├── index.html            # Landing page
├── login.html            # Login page
├── register.html         # Registration page
├── css/
│   └── style.css         # Custom styles and CSS variables
├── js/
│   ├── auth.js           # Password visibility toggle
│   ├── validation.js     # Form validation logic and password strength
│   └── ajax.js           # AJAX fetch logic for email checking
├── php/
│   └── check-user.php    # Dummy PHP endpoint returning JSON
└── README.md
```

## How to run the project

### Static UI Check
1. Open `index.html` in any modern web browser to view the project.
2. Navigate between Home, Login, and Sign Up using the navbar or on-page buttons.
3. Test validation by submitting empty forms.

### Running via XAMPP (Local Server)
To test the full functionality including the AJAX email validation, you should run the project through XAMPP:

1. Open your XAMPP installation directory (usually `C:\xampp`).
2. Navigate to the `htdocs` folder (`C:\xampp\htdocs`).
3. Copy the entire `task2` folder (which contains this project) into the `htdocs` directory.
4. Open the **XAMPP Control Panel** and start the **Apache** module.
5. Open your web browser and go to: `http://localhost/task2/index.html` (If you renamed the folder, use that name instead of `task2`).
6. You can now test the registration form's email validation by entering `admin@example.com` to see the "already in use" error, or another email to see the success state.

### AJAX Flow Explanation
1. User types in the **Email Address** field on `register.html`.
2. After 500ms of no typing, JavaScript (`ajax.js`) validates the format.
3. If valid, it shows a loading spinner on the input field and sends a `GET` request using `fetch()` to `php/check-user.php?email=...`
4. The PHP script pauses for 0.5s to simulate network latency, checks an array of dummy emails, and returns a JSON response `{ "exists": true }` or `{ "exists": false }`.
5. JavaScript parses the JSON and updates the UI (changing borders to red/green and showing feedback text) without reloading the page.
6. **Fallback**: If the fetch fails (e.g., viewing static HTML without a PHP server), `ajax.js` catches the error and applies a mock check where emails starting with `admin` are treated as taken.
