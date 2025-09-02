# PHP Login & Registration System with JSON Storage

A secure user management system built with PHP that uses JSON files for data storage instead of a traditional database. This application provides user authentication, role-based access control, profile management, and administrative capabilities.

## Features

- **User Authentication**
  - Secure registration with password hashing
  - Login with username or email
  - Session management
  - Logout functionality

- **User Roles**
  - Admin role with special privileges
  - Member role for regular users

- **Profile Management**
  - View user profile information
  - Edit profile details (first name, last name, username, email)
  - Update password
  - Upload and manage profile photos
  - Default avatar for users without a custom photo

- **Admin Controls**
  - View all registered users
  - Add new users
  - Edit existing users
  - Delete users
  - Manage user roles

- **Security Features**
  - Password hashing using PHP's password_hash()
  - Strong password complexity requirements:
    - Minimum 8 characters
    - At least one uppercase letter
    - At least one lowercase letter
    - At least one number
    - At least one special character
  - Password visibility toggle (show/hide password)
  - Real-time password strength indicator (Weak/Medium/Strong)
  - Password requirements displayed below password fields
  - Input validation and sanitization
  - Protection against duplicate usernames and emails
  - Role-based access control
  - Automatic session timeout after 1 hour of inactivity

## Technology Stack

- PHP (No frameworks)
- JSON for data storage
- HTML/CSS for frontend
- SCSS for styling (compiled to CSS)
- Vanilla JavaScript for interactive elements

## File Structure

- `index.php` - Landing page
- `login.php` - Login and registration forms
- `auth.php` - Authentication logic (login, registration, user management)
- `dashboard.php` - User dashboard
- `admin.php` - Admin control panel
- `profile.php` - User profile editing
- `logout.php` - Logout functionality
- `header.php` - Common header
- `footer.php` - Common footer
- `phpinfo.php` - PHP configuration information
- `css/` - Stylesheet directory
  - `styles.scss` - SCSS source file
  - `styles.css` - Compiled CSS
  - `styles.css.map` - Source map for CSS debugging
- `js/` - JavaScript directory
  - `main.js` - Main JavaScript functionality
- `images/` - Image assets directory
  - `favicon/` - Favicon files
    - `favicon_dark_theme.ico` - Dark theme favicon (ICO format)
    - `favicon_dark_theme.png` - Dark theme favicon (PNG format)
    - `favicon_dark_theme.svg` - Dark theme favicon (SVG format)
    - `favicon_light_theme.ico` - Light theme favicon (ICO format)
    - `favicon_light_theme.png` - Light theme favicon (PNG format)
    - `favicon_light_theme.svg` - Light theme favicon (SVG format)
- `data/` - Data storage directory
  - `users.json` - User data storage
- `uploads/` - User uploaded content
  - `profile_photos/` - Storage for user profile photos
    - `default_avatar.png` - Default avatar image for users without a custom photo

## Installation

1. Clone or download the repository
2. Place the files in your web server's document root or a subdirectory
3. Ensure the web server has write permissions to the `data/` directory
4. Access the application through your web browser

## Usage

### Default Accounts

The system comes with two pre-configured accounts:

1. **Admin User**
   - Username: admin
   - Email: admin@gmail.com
   - Password: admin1234!
   - Role: admin

2. **Member User**
   - Username: member
   - Email: member@gmail.com
   - Password: member1234!
   - Role: member

### User Roles and Capabilities

- **Admin**
  - Can access the admin panel
  - Can view all users
  - Can add, edit, and delete users
  - Can assign roles to users
  - Can manage their own profile

- **Member**
  - Can access their dashboard
  - Can view and edit their profile
  - Can upload and manage their profile photo
  - Cannot access admin features

### Profile Photo Management

- Users can upload a profile photo from their profile page
- Supported formats: JPG, JPEG, PNG, and GIF
- Maximum file size: 2MB
- If no photo is uploaded, a default avatar is displayed
- The custom file input is styled to match the overall design of the application

## Security Considerations

- The application uses JSON files for data storage, which is suitable for small applications or prototypes
- For production environments with high traffic or sensitive data, consider using a proper database system
- The `data/` directory should be properly secured to prevent direct access to the JSON files

## License

This project is open-source and available under the MIT License.
