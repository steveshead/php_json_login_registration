<?php
session_start();

// Check for session timeout (1 hour = 3600 seconds)
if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    $timeout_duration = 3600; // 1 hour in seconds

    if ($inactive_time >= $timeout_duration) {
        // Clear all session variables
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();

        // Start a new session for the message
        session_start();

        // Set a specific message for session expiration
        $_SESSION['session_expired'] = true;
        $_SESSION['error'] = 'Your session has expired due to inactivity. Please log in again.';

        // Redirect to login page if not already there
        if (basename($_SERVER['PHP_SELF']) != 'login.php' && 
            basename($_SERVER['PHP_SELF']) != 'index.php') {
            header('Location: login.php');
            exit;
        }
    } else {
        // Update last activity time stamp if user is active
        $_SESSION['last_activity'] = time();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Login & Registration System'; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Font Awesome CN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon/favicon_light_theme.svg" id="faviconTag">
</head>
<body>
    <div class="main-nav shadow">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php" class="uppercase">Login System</a>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="admin.php">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login / Sign up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <div class="container<?php echo isset($containerClass) ? ' ' . $containerClass : ''; ?>">
