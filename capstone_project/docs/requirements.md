# E-Learning Portal Requirements

## 1. Project Title and Overview
**Title:** E-Learning Portal Capstone
**Overview:** A comprehensive web-based platform designed to facilitate online education. It allows instructors to create and manage courses, and students to browse, enroll, and track their learning progress. The platform includes an admin dashboard for overall system management.

## 2. Problem Statement
Many existing online learning platforms are either too complex for simple setups or lack critical features like secure Email OTP verification, real-time analytics, and role-based access control. There is a need for a streamlined, secure, and responsive platform that handles core e-learning functionalities efficiently.

## 3. Proposed Solution
The proposed solution is a custom-built, responsive web application using PHP and MySQL. It will provide a secure environment with Email OTP authentication, three distinct user roles (Student, Instructor, Admin), dynamic AJAX-based search and filtering, and real-time Chart.js analytics for administrators.

## 4. Target Users
- **Students:** Individuals looking to learn new skills.
- **Instructors:** Educators and professionals wanting to share their knowledge and manage courses.
- **Administrators:** Platform managers responsible for overseeing users, content, and system health.

## 5. User Roles
- **Student:** Can browse courses, search/filter, enroll, view lessons, track progress, and manage their profile.
- **Instructor:** Can create, read, update, delete (CRUD) their own courses and lessons. Can view students enrolled in their courses.
- **Admin:** Has full access to all system entities, user management, and platform analytics.

## 6. Functional Requirements
- Secure User Registration and Login.
- Email OTP verification upon registration.
- Role-based Access Control (RBAC).
- Course Management (CRUD by Instructors and Admins).
- Lesson Management (CRUD by Instructors and Admins).
- Enrollment Management (Students can enroll, system prevents duplicates).
- AJAX real-time search, filtering, and pagination for courses.
- Profile Management (update details, upload avatar).
- Admin Dashboard with Chart.js analytics.

## 7. Non-functional Requirements
- **Security:** Prepared statements for SQL queries, password hashing, XSS/CSRF protection, secure sessions.
- **Performance:** Efficient SQL queries, AJAX to prevent full-page reloads, optimized assets.
- **Usability:** Responsive UI (desktop, tablet, mobile), clear feedback messages, accessible forms.
- **Reliability:** Graceful error handling.

## 8. Use Cases

### UC-1: Student Registration
- **Actor:** Guest
- **Preconditions:** None
- **Main Flow:** Guest fills out registration form, submits. System generates OTP, hashes it, stores it, sends it via email. Guest redirected to OTP verification page.
- **Alternative Flow:** Email already exists -> show error.
- **Expected Result:** A pending account is created and an OTP is sent.

### UC-2: OTP Verification
- **Actor:** Guest (Pending User)
- **Preconditions:** Has pending account and OTP.
- **Main Flow:** User enters OTP. System verifies hash and expiration. Marks account as verified.
- **Alternative Flow:** OTP incorrect or expired -> show error, increment attempts.
- **Expected Result:** User account becomes active and can log in.

### UC-3: Course Creation
- **Actor:** Instructor
- **Preconditions:** Logged in as Instructor.
- **Main Flow:** Instructor fills out course details (title, description, price, thumbnail, category), submits. System saves course to DB.
- **Alternative Flow:** Invalid file upload (thumbnail) -> show error.
- **Expected Result:** New course is created and visible on the platform.

### UC-4: Course Enrollment
- **Actor:** Student
- **Preconditions:** Logged in as Student.
- **Main Flow:** Student clicks "Enroll" on a course. System records enrollment.
- **Alternative Flow:** Student already enrolled -> prevent duplicate, show message.
- **Expected Result:** Student is enrolled and course appears in their dashboard.

## 9. Core Modules
- **Authentication & Authorization Module:** Login, Registration, OTP, RBAC, Sessions.
- **User Module:** Profile, Avatar, Password management.
- **Course & Category Module:** Course listings, Lesson content.
- **Enrollment Module:** Tracking student access and progress.
- **Search & Filter Module:** AJAX-driven dynamic content loading.
- **Analytics Module:** Chart.js integration for admin insights.

## 10. Database Entities
- `roles`
- `users`
- `otp_verifications`
- `categories`
- `courses`
- `lessons`
- `enrollments`

## 11. Security Requirements
- All user-controlled SQL must use prepared statements (PDO/mysqli).
- Passwords hashed using `password_hash()`.
- OTP must be hashed, have an expiration time (e.g., 10 mins), and limit attempts.
- CSRF tokens on forms.
- Data output sanitized using `htmlspecialchars()`.
- Server-side validation of file uploads (MIME type, size, extension).

## 12. Deployment Requirements
- Deploy to free PHP/MySQL hosting (e.g., InfinityFree).
- Use environment configuration (no localhost assumptions or hardcoded DB credentials).
- Disable verbose PHP errors in production.
- Ensure HTTPS is enabled.

## 13. Future Enhancements
- Payment gateway integration (Stripe/PayPal) for paid courses.
- Real-time chat or discussion forums for courses.
- Advanced video streaming (HLS/DASH) for lessons.
- Automated certificate generation upon course completion.
