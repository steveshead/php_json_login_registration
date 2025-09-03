<?php
// Set page title
$pageTitle = 'Welcome to Our System';

// Include header
include 'header.php';
?>

<?php if (isset($_SESSION['session_expired']) && $_SESSION['session_expired']): ?>
    <div class="error">Your session has expired due to inactivity. Please log in again.</div>
    <?php unset($_SESSION['session_expired']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['user_first_name']) && !empty($_SESSION['user_first_name'])): ?>
    <h1>Welcome <span class="blue"><?= $_SESSION['user_first_name'] ?></span>, to the Login & Registration System</h1>
<?php else: ?>
    <h1>Welcome to the Login & Registration System</h1>
<?php endif; ?>

<div class="landing-intro">
    <p>This is a secure user management system that allows you to:</p>
    <ul>
        <li>Create an account with a unique username and email</li>
        <li>Log in securely with either username or email</li>
        <li>Access your personal dashboard</li>
        <li>Manage your profile information</li>
        <li>Change your password with real-time strength feedback</li>
    </ul>

    <p>Our system uses secure password hashing, strong password requirements, and automatic session timeout to keep your information safe.</p>
</div>

<div class="landing-actions">
    <h2>Get Started</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
        <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="btn">Login / Register</a>
    <?php endif; ?>
</div>

<div class="landing-features">
    <h2>Features</h2>
    <div class="feature-grid">
        <div class="feature">
            <h3>Enhanced Security</h3>
            <p>Secure password hashing, strong password requirements, password visibility toggle, and automatic session timeout after 1 hour of inactivity.</p>
        </div>
        <div class="feature">
            <h3>User Roles</h3>
            <p>Different access levels for regular members and administrators with role-based access control.</p>
        </div>
        <div class="feature">
            <h3>Profile Management</h3>
            <p>Easily update your profile information and change your password with real-time strength indicator.</p>
        </div>
        <div class="feature">
            <h3>Admin Controls</h3>
            <p>Administrators can view, add, edit, and delete users, as well as manage user roles.</p>
        </div>
    </div>
</div>

<?php
// Include footer
include 'footer.php';
?>
