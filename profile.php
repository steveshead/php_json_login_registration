<?php
// Set page title
$pageTitle = 'Edit Profile';

// Include header (which starts the session)
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to access your profile.';
    header('Location: login.php');
    exit;
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : '';
$user_last_name = isset($_SESSION['user_last_name']) ? $_SESSION['user_last_name'] : '';
$user_email = $_SESSION['user_email'];
$user_username = isset($_SESSION['user_username']) ? $_SESSION['user_username'] : '';

// Load user data from JSON file to get the most up-to-date information
$users_file = 'data/users.json';
$users = [];
$current_user = null;

if (file_exists($users_file)) {
    $users = json_decode(file_get_contents($users_file), true);

    // Find the current user
    foreach ($users as $user) {
        if ($user['id'] === $user_id) {
            $current_user = $user;
            break;
        }
    }
}

// If user not found in the JSON file, redirect to dashboard
if ($current_user === null) {
    $_SESSION['error'] = 'User data not found.';
    header('Location: dashboard.php');
    exit;
}
?>

<h1>Edit Your Profile</h1>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error"><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success"><?php echo $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="profile-form">
    <form action="auth.php" method="post">
        <input type="hidden" name="action" value="update_profile">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_id); ?>">

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($current_user['first_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($current_user['last_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($current_user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">New Password (leave blank to keep current)</label>
            <div class="password-input-container">
                <input type="password" id="password" name="password" placeholder="Password">
                <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility(this)"></i>
            </div>
            <div id="passwordStrength"></div>
            <small class="password-requirements">If changing password: Must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Update Profile</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
// Include footer
include 'footer.php';
?>
