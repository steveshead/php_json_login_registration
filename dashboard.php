<?php
// Set page title and container class
$pageTitle = 'Dashboard';

// Include header (which starts the session)
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to access the dashboard.';
    header('Location: login.php');
    exit;
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : '';
$user_last_name = isset($_SESSION['user_last_name']) ? $_SESSION['user_last_name'] : '';
$user_name = $user_first_name . ' ' . $user_last_name; // For backward compatibility
$user_email = $_SESSION['user_email'];
$user_username = isset($_SESSION['user_username']) ? $_SESSION['user_username'] : '';
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'member';
?>

<?php if (isset($_SESSION['user_first_name']) && !empty($_SESSION['user_first_name'])): ?>
    <h1>Welcome to your dashboard, <span class="blue"><?= $_SESSION['user_first_name'] ?></span>!</h1>
<?php else: ?>
    <h1>Welcome to tto your dashboard</h1>
<?php endif; ?>

<div class="user-info">
    <h2>Your Profile</h2>
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user_first_name); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user_last_name); ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user_username); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($user_role)); ?></p>
    <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
</div>

<p>This is your personal dashboard. You are now logged in to the system.</p>

<?php if ($user_role === 'admin'): ?>
<div class="admin-section">
    <h2>Admin Functions</h2>
    <p>As an administrator, you have access to additional features:</p>
    <p>
        <a href="admin.php" class="btn">Manage Users</a>
    </p>
</div>
<?php endif; ?>

<p>
    <a href="profile.php" class="btn">Edit Profile</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</p>

<?php
// Include footer
include 'footer.php';
?>
