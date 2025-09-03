<?php
session_start();

// Define the path to the users JSON file
$users_file = 'data/users.json';

// Create data directory if it doesn't exist
if (!file_exists('data') && !mkdir('data', 0755, true) && !is_dir('data')) {
    $_SESSION['error'] = 'Failed to create data directory.';
    header('Location: login.php');
    exit;
}

// Create users file if it doesn't exist
if (!file_exists($users_file)) {
    file_put_contents($users_file, json_encode([]));
}

/**
 * Validate password complexity
 * 
 * @param string $password The password to validate
 * @return array An array with 'valid' (boolean) and 'message' (string) keys
 */
function validatePasswordComplexity($password) {
    // Initialize result
    $result = [
        'valid' => true,
        'message' => ''
    ];

    // Check minimum length (8 characters)
    if (strlen($password) < 8) {
        $result['valid'] = false;
        $result['message'] = 'Password must be at least 8 characters long.';
        return $result;
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $result['valid'] = false;
        $result['message'] = 'Password must contain at least one uppercase letter.';
        return $result;
    }

    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        $result['valid'] = false;
        $result['message'] = 'Password must contain at least one lowercase letter.';
        return $result;
    }

    // Check for at least one number
    if (!preg_match('/[0-9]/', $password)) {
        $result['valid'] = false;
        $result['message'] = 'Password must contain at least one number.';
        return $result;
    }

    // Check for at least one special character
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $result['valid'] = false;
        $result['message'] = 'Password must contain at least one special character.';
        return $result;
    }

    return $result;
}

// Get the action from the form or query string
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Handle different actions
switch ($action) {
    case 'register':
        handleRegistration();
        break;
    case 'login':
        handleLogin();
        break;
    case 'add_user':
        handleAddUser();
        break;
    case 'edit_user':
        handleEditUser();
        break;
    case 'delete_user':
        handleDeleteUser();
        break;
    case 'update_profile':
        handleUpdateProfile();
        break;
    default:
        // Redirect to index if no valid action
        $_SESSION['error'] = 'Invalid action.';
        header('Location: login.php');
        exit;
}

// Handle user registration
function handleRegistration() {
    global $users_file;

    // Get form data
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validate form data
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: login.php');
        exit;
    }

    // Validate username (alphanumeric and underscore only)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $_SESSION['error'] = 'Username can only contain letters, numbers, and underscores.';
        header('Location: login.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: login.php');
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: login.php');
        exit;
    }

    // Validate password complexity
    $password_validation = validatePasswordComplexity($password);
    if (!$password_validation['valid']) {
        $_SESSION['error'] = $password_validation['message'];
        header('Location: login.php');
        exit;
    }

    // Load existing users
    $users = json_decode(file_get_contents($users_file), true);

    // Check if email or username already exists
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $_SESSION['error'] = 'Email already registered. Please use a different email.';
            header('Location: login.php');
            exit;
        }
        if (isset($user['username']) && $user['username'] === $username) {
            $_SESSION['error'] = 'Username already taken. Please choose a different username.';
            header('Location: login.php');
            exit;
        }
    }

    // Load existing users to determine the next ID
    $users = json_decode(file_get_contents($users_file), true);

    // Find the highest existing ID
    $highest_id = 0;
    foreach ($users as $user) {
        $user_id = (int)$user['id'];
        if ($user_id > $highest_id) {
            $highest_id = $user_id;
        }
    }

    // Create new user with incremented ID
    $new_user = [
        'id' => (string)($highest_id + 1),
        'first_name' => $first_name,
        'last_name' => $last_name,
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'member', // Default role for new users
        'created_at' => date('Y-m-d H:i:s'),
        'photo' => '' // Empty photo field for new users
    ];

    // Add user to the array
    $users[] = $new_user;

    // Save updated users array to JSON file
    if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
        $_SESSION['success'] = 'Registration successful! You can now log in.';
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        header('Location: login.php');
        exit;
    }
}

// Handle adding a new user from admin panel
function handleAddUser() {
    global $users_file;

    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = 'You do not have permission to perform this action.';
        header('Location: login.php');
        exit;
    }

    // Get form data
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : 'member';

    // Validate form data
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: admin.php');
        exit;
    }

    // Validate username (alphanumeric and underscore only)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $_SESSION['error'] = 'Username can only contain letters, numbers, and underscores.';
        header('Location: admin.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: admin.php');
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: admin.php');
        exit;
    }

    // Validate password complexity
    $password_validation = validatePasswordComplexity($password);
    if (!$password_validation['valid']) {
        $_SESSION['error'] = $password_validation['message'];
        header('Location: admin.php');
        exit;
    }

    // Validate role
    if ($role !== 'admin' && $role !== 'member') {
        $_SESSION['error'] = 'Invalid role.';
        header('Location: admin.php');
        exit;
    }

    // Load existing users
    $users = json_decode(file_get_contents($users_file), true);

    // Check if email or username already exists
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $_SESSION['error'] = 'Email already registered. Please use a different email.';
            header('Location: admin.php');
            exit;
        }
        if (isset($user['username']) && $user['username'] === $username) {
            $_SESSION['error'] = 'Username already taken. Please choose a different username.';
            header('Location: admin.php');
            exit;
        }
    }

    // Find the highest existing ID
    $highest_id = 0;
    foreach ($users as $user) {
        $user_id = (int)$user['id'];
        if ($user_id > $highest_id) {
            $highest_id = $user_id;
        }
    }

    // Create new user with incremented ID
    $new_user = [
        'id' => (string)($highest_id + 1),
        'first_name' => $first_name,
        'last_name' => $last_name,
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
        'created_at' => date('Y-m-d H:i:s'),
        'photo' => '' // Empty photo field for new users
    ];

    // Add user to the array
    $users[] = $new_user;

    // Save updated users array to JSON file
    if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
        $_SESSION['success'] = 'User added successfully.';
        header('Location: admin.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to add user. Please try again.';
        header('Location: admin.php');
        exit;
    }
}

// Handle editing a user from admin panel
function handleEditUser() {
    global $users_file;

    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = 'You do not have permission to perform this action.';
        header('Location: login.php');
        exit;
    }

    // Get form data
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : 'member';

    // Validate form data
    if (empty($id) || empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
        $_SESSION['error'] = 'ID, First Name, Last Name, Username, and Email are required.';
        header('Location: admin.php');
        exit;
    }

    // Validate username (alphanumeric and underscore only)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $_SESSION['error'] = 'Username can only contain letters, numbers, and underscores.';
        header('Location: admin.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: admin.php');
        exit;
    }

    // Check if passwords match if provided
    if (!empty($password) && $password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: admin.php');
        exit;
    }

    // Validate password complexity if provided
    if (!empty($password)) {
        $password_validation = validatePasswordComplexity($password);
        if (!$password_validation['valid']) {
            $_SESSION['error'] = $password_validation['message'];
            header('Location: admin.php');
            exit;
        }
    }

    // Validate role
    if ($role !== 'admin' && $role !== 'member') {
        $_SESSION['error'] = 'Invalid role.';
        header('Location: admin.php');
        exit;
    }

    // Load existing users
    $users = json_decode(file_get_contents($users_file), true);

    // Find the user to edit
    $user_found = false;
    $user_index = -1;

    foreach ($users as $index => $user) {
        if ($user['id'] === $id) {
            $user_found = true;
            $user_index = $index;
            break;
        }
    }

    if (!$user_found) {
        $_SESSION['error'] = 'User not found.';
        header('Location: admin.php');
        exit;
    }

    // Check if email or username already exists (excluding the current user)
    foreach ($users as $user) {
        if ($user['id'] !== $id) {
            if ($user['email'] === $email) {
                $_SESSION['error'] = 'Email already registered. Please use a different email.';
                header('Location: admin.php');
                exit;
            }
            if (isset($user['username']) && $user['username'] === $username) {
                $_SESSION['error'] = 'Username already taken. Please choose a different username.';
                header('Location: admin.php');
                exit;
            }
        }
    }

    // Update user data
    $users[$user_index]['first_name'] = $first_name;
    $users[$user_index]['last_name'] = $last_name;
    $users[$user_index]['username'] = $username;
    $users[$user_index]['email'] = $email;
    $users[$user_index]['role'] = $role;

    // Update password if provided
    if (!empty($password)) {
        $users[$user_index]['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Save updated users array to JSON file
    if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
        $_SESSION['success'] = 'User updated successfully.';
        header('Location: admin.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update user. Please try again.';
        header('Location: admin.php');
        exit;
    }
}

// Handle deleting a user from admin panel
function handleDeleteUser() {
    global $users_file;

    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = 'You do not have permission to perform this action.';
        header('Location: login.php');
        exit;
    }

    // Get user ID from query string
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';

    if (empty($id)) {
        $_SESSION['error'] = 'User ID is required.';
        header('Location: admin.php');
        exit;
    }

    // Load existing users
    $users = json_decode(file_get_contents($users_file), true);

    // Prevent deleting the current logged-in user
    if ($id === $_SESSION['user_id']) {
        $_SESSION['error'] = 'You cannot delete your own account.';
        header('Location: admin.php');
        exit;
    }

    // Find the user to delete
    $user_found = false;
    $updated_users = [];

    foreach ($users as $user) {
        if ($user['id'] === $id) {
            $user_found = true;
        } else {
            $updated_users[] = $user;
        }
    }

    if (!$user_found) {
        $_SESSION['error'] = 'User not found.';
        header('Location: admin.php');
        exit;
    }

    // Save updated users array to JSON file
    if (file_put_contents($users_file, json_encode($updated_users, JSON_PRETTY_PRINT))) {
        $_SESSION['success'] = 'User deleted successfully.';
        header('Location: admin.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to delete user. Please try again.';
        header('Location: admin.php');
        exit;
    }
}

// Handle user profile update
function handleUpdateProfile() {
    global $users_file;

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Please log in to update your profile.';
        header('Location: login.php');
        exit;
    }

    // Get form data
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Verify that the user is updating their own profile
    if ($id !== $_SESSION['user_id']) {
        $_SESSION['error'] = 'You can only update your own profile.';
        header('Location: dashboard.php');
        exit;
    }

    // Validate form data
    if (empty($id) || empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
        $_SESSION['error'] = 'First Name, Last Name, Username, and Email are required.';
        header('Location: profile.php');
        exit;
    }

    // Validate username (alphanumeric and underscore only)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $_SESSION['error'] = 'Username can only contain letters, numbers, and underscores.';
        header('Location: profile.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: profile.php');
        exit;
    }

    // Check if passwords match if provided
    if (!empty($password) && $password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: profile.php');
        exit;
    }

    // Validate password complexity if provided
    if (!empty($password)) {
        $password_validation = validatePasswordComplexity($password);
        if (!$password_validation['valid']) {
            $_SESSION['error'] = $password_validation['message'];
            header('Location: profile.php');
            exit;
        }
    }

    // Load existing users
    $users = json_decode(file_get_contents($users_file), true);

    // Find the user to edit
    $user_found = false;
    $user_index = -1;

    foreach ($users as $index => $user) {
        if ($user['id'] === $id) {
            $user_found = true;
            $user_index = $index;
            break;
        }
    }

    if (!$user_found) {
        $_SESSION['error'] = 'User not found.';
        header('Location: dashboard.php');
        exit;
    }

    // Check if email or username already exists (excluding the current user)
    foreach ($users as $user) {
        if ($user['id'] !== $id) {
            if ($user['email'] === $email) {
                $_SESSION['error'] = 'Email already registered. Please use a different email.';
                header('Location: profile.php');
                exit;
            }
            if (isset($user['username']) && $user['username'] === $username) {
                $_SESSION['error'] = 'Username already taken. Please choose a different username.';
                header('Location: profile.php');
                exit;
            }
        }
    }

    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_photo']['tmp_name'];
        $file_name = $_FILES['profile_photo']['name'];
        $file_size = $_FILES['profile_photo']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
            header('Location: profile.php');
            exit;
        }

        // Validate file size (max 2MB)
        if ($file_size > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'File size must be less than 2MB.';
            header('Location: profile.php');
            exit;
        }

        // Create uploads directory if it doesn't exist
        if (!file_exists('uploads/profile_photos') && !mkdir('uploads/profile_photos', 0755, true) && !is_dir('uploads/profile_photos')) {
            $_SESSION['error'] = 'Failed to create uploads directory.';
            header('Location: profile.php');
            exit;
        }

        // Generate a unique filename
        $new_file_name = 'user_' . $id . '_' . time() . '.' . $file_ext;
        $upload_path = 'uploads/profile_photos/' . $new_file_name;

        // Move the uploaded file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Delete old photo if exists
            if (isset($users[$user_index]['photo']) && !empty($users[$user_index]['photo']) && file_exists($users[$user_index]['photo']) && strpos($users[$user_index]['photo'], 'uploads/profile_photos/') === 0) {
                unlink($users[$user_index]['photo']);
            }

            // Update user data with new photo path
            $users[$user_index]['photo'] = $upload_path;
        } else {
            $_SESSION['error'] = 'Failed to upload profile photo.';
            header('Location: profile.php');
            exit;
        }
    }

    // Update user data
    $users[$user_index]['first_name'] = $first_name;
    $users[$user_index]['last_name'] = $last_name;
    $users[$user_index]['username'] = $username;
    $users[$user_index]['email'] = $email;

    // Update password if provided
    if (!empty($password)) {
        $users[$user_index]['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Save updated users array to JSON file
    if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
        // Update session variables
        $_SESSION['user_first_name'] = $first_name;
        $_SESSION['user_last_name'] = $last_name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_username'] = $username;
        $_SESSION['user_photo'] = isset($users[$user_index]['photo']) ? $users[$user_index]['photo'] : '';

        $_SESSION['success'] = 'Profile updated successfully.';
        header('Location: profile.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update profile. Please try again.';
        header('Location: profile.php');
        exit;
    }
}

// Handle user login
function handleLogin() {
    global $users_file;

    // Get form data
    $identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate form data
    if (empty($identifier) || empty($password)) {
        $_SESSION['error'] = 'Username/Email and password are required.';
        header('Location: login.php');
        exit;
    }

    // Load existing users
    $users = json_decode(file_get_contents($users_file), true);

    // Check if user exists and verify password
    $user_found = false;
    foreach ($users as $user) {
        // Check if identifier matches either username or email
        if (($user['email'] === $identifier) || (isset($user['username']) && $user['username'] === $identifier)) {
            $user_found = true;
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_first_name'] = $user['first_name'];
                $_SESSION['user_last_name'] = $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_username'] = isset($user['username']) ? $user['username'] : '';
                $_SESSION['user_role'] = isset($user['role']) ? $user['role'] : 'member';
                $_SESSION['user_photo'] = isset($user['photo']) ? $user['photo'] : '';

                // Set last activity timestamp for session timeout
                $_SESSION['last_activity'] = time();

                // Redirect to dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid password.';
                header('Location: login.php');
                exit;
            }
        }
    }

    if (!$user_found) {
        $_SESSION['error'] = 'Username or Email not found.';
        header('Location: login.php');
        exit;
    }
}
