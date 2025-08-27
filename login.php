<?php
// Set page title
$pageTitle = 'Login & Registration';

// Include header (which starts the session)
include 'header.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Check for error or success messages
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';

// Clear session messages
unset($_SESSION['error']);
unset($_SESSION['success']);
?>

<h1>Login & Registration</h1>

<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="tabs">
    <div class="tab active" onclick="showTab('login')">Login</div>
    <div class="tab" onclick="showTab('register')">Register</div>
</div>

<div id="login" class="tab-content active">
    <h2>Login</h2>
    <form action="auth.php" method="post">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="login-identifier">Username or Email</label>
            <input type="text" id="login-identifier" name="identifier" required>
        </div>
        <div class="form-group">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<div id="register" class="tab-content">
    <h2>Register</h2>
    <form action="auth.php" method="post">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
            <label for="register-first-name">First Name</label>
            <input type="text" id="register-first-name" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="register-last-name">Last Name</label>
            <input type="text" id="register-last-name" name="last_name" required>
        </div>
        <div class="form-group">
            <label for="register-username">Username</label>
            <input type="text" id="register-username" name="username" required>
        </div>
        <div class="form-group">
            <label for="register-email">Email</label>
            <input type="email" id="register-email" name="email" required>
        </div>
        <div class="form-group">
            <label for="register-password">Password</label>
            <div class="password-input-container">
                <input type="password" id="register-password" name="password" placeholder="Password">
                <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility(this)"></i>
            </div>
            <div id="passwordStrength"></div>

            <small class="password-requirements">Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.</small>
        </div>
        <div class="form-group">
            <label for="register-confirm-password">Confirm Password</label>
            <input type="password" id="register-confirm-password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Register</button>
    </form>
</div>

<?php
// Set page-specific JavaScript
$pageScript = "
    function showTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Deactivate all tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Activate the selected tab and content
        document.getElementById(tabId).classList.add('active');
        document.querySelector('.tab[onclick=\"showTab(\\'' + tabId + '\\')\"').classList.add('active');
    }
";

// Include footer
include 'footer.php';
?>
