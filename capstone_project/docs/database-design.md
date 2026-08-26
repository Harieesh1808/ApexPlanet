# Database Design & ER Diagram

## Entities and Relationships

The `elearning_db` database is normalized (3NF) and contains the following tables:

1.  **roles**: Defines system roles (Admin, Instructor, Student).
2.  **users**: Stores user accounts.
    *   `role_id` (FK to roles)
3.  **otp_verifications**: Stores OTPs for email verification.
    *   `user_id` (FK to users)
4.  **categories**: Course categories.
5.  **courses**: Created by instructors, belong to a category.
    *   `instructor_id` (FK to users)
    *   `category_id` (FK to categories)
6.  **lessons**: Content units within a course.
    *   `course_id` (FK to courses)
7.  **enrollments**: Tracks student enrollments (Many-to-Many between users and courses).
    *   `user_id` (FK to users)
    *   `course_id` (FK to courses)
8.  **reviews**: Student reviews for courses.
    *   `user_id` (FK to users)
    *   `course_id` (FK to courses)

## ER Diagram

```mermaid
erDiagram
    ROLES {
        int id PK
        string role_name
    }
    
    USERS {
        int id PK
        int role_id FK
        string name
        string email
        string password_hash
        string profile_picture
        boolean email_verified
        datetime created_at
        datetime updated_at
    }
    
    OTP_VERIFICATIONS {
        int id PK
        int user_id FK
        string otp_hash
        datetime expires_at
        int attempts
        datetime verified_at
    }
    
    CATEGORIES {
        int id PK
        string name
        string description
    }
    
    COURSES {
        int id PK
        int instructor_id FK
        int category_id FK
        string title
        string description
        decimal price
        string thumbnail
        enum status
    }
    
    LESSONS {
        int id PK
        int course_id FK
        string title
        string description
        string content_url
        int position
    }
    
    ENROLLMENTS {
        int id PK
        int user_id FK
        int course_id FK
        datetime enrolled_at
        int progress
        enum status
    }
    
    REVIEWS {
        int id PK
        int user_id FK
        int course_id FK
        int rating
        string comment
    }

    ROLES ||--o{ USERS : "has"
    USERS ||--o| OTP_VERIFICATIONS : "generates"
    USERS ||--o{ COURSES : "instructs"
    CATEGORIES ||--o{ COURSES : "categorizes"
    COURSES ||--o{ LESSONS : "contains"
    USERS ||--o{ ENROLLMENTS : "enrolls in"
    COURSES ||--o{ ENROLLMENTS : "has enrollments"
    USERS ||--o{ REVIEWS : "writes"
    COURSES ||--o{ REVIEWS : "receives"
```
