<?php
// Set page title and container class
$pageTitle = 'Admin Panel';
$containerClass = 'admin';

// Include header (which starts the session)
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to access the admin page.';
    header('Location: login.php');
    exit;
}

// Check if user has admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = 'You do not have permission to access the admin page.';
    header('Location: dashboard.php');
    exit;
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_username = isset($_SESSION['user_username']) ? $_SESSION['user_username'] : '';
$user_role = $_SESSION['user_role'];

// Load all users from JSON file
$users_file = 'data/users.json';
$users = [];

if (file_exists($users_file)) {
    $users = json_decode(file_get_contents($users_file), true);
}
?>
        <h1>Admin Panel</h1>

        <div class="navigation">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?php echo $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <h2>User Management</h2>

        <div class="action-buttons">
            <button class="btn" onclick="showAddUserForm()">Add New User</button>
        </div>

        <!-- Add User Form -->
        <div id="add-user-form" style="display: none;" class="user-form">
            <h3>Add New User</h3>
            <form action="auth.php" method="post">
                <input type="hidden" name="action" value="add_user">
                <div class="form-group">
                    <label for="add-name">Full Name</label>
                    <input type="text" id="add-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="add-username">Username</label>
                    <input type="text" id="add-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="add-email">Email</label>
                    <input type="email" id="add-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="add-password">Password</label>
                    <div class="password-input-container">
                        <input type="password" id="add-password" name="password" required>
                        <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility(this)"></i>
                    </div>
                    <div id="passwordStrength"></div>
                    <small class="password-requirements">Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.</small>
                </div>
                <div class="form-group">
                    <label for="add-confirm-password">Confirm Password</label>
                    <input type="password" id="add-confirm-password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="add-role">Role</label>
                    <select id="add-role" name="role" required>
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn">Add User</button>
                <button type="button" class="btn btn-danger" onclick="hideAddUserForm()">Cancel</button>
            </form>
        </div>

        <!-- Edit User Form -->
        <div id="edit-user-form" style="display: none;" class="user-form">
            <h3>Edit User</h3>
            <form action="auth.php" method="post">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" id="edit-id" name="id">
                <div class="form-group">
                    <label for="edit-name">Full Name</label>
                    <input type="text" id="edit-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit-username">Username</label>
                    <input type="text" id="edit-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="edit-email">Email</label>
                    <input type="email" id="edit-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="edit-password">Password (leave blank to keep current)</label>
                    <div class="password-input-container">
                        <input type="password" id="edit-password" name="password">
                        <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility(this)"></i>
                    </div>
                    <div id="passwordStrength_edit"></div>
                    <small class="password-requirements">If changing password: Must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.</small>
                </div>
                <div class="form-group">
                    <label for="edit-confirm-password">Confirm Password</label>
                    <input type="password" id="edit-confirm-password" name="confirm_password">
                </div>
                <div class="form-group">
                    <label for="edit-role">Role</label>
                    <select id="edit-role" name="role" required>
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn">Update User</button>
                <button type="button" class="btn btn-danger" onclick="hideEditUserForm()">Cancel</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'N/A'; ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo isset($user['role']) ? htmlspecialchars(ucfirst($user['role'])) : 'Member'; ?></td>
                    <td><?php echo date('jS F, Y h:i A', strtotime($user['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-sm" onclick="editUser('<?php echo $user['id']; ?>')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars(addslashes($user['name'])); ?>')">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

<?php
// Set page-specific JavaScript
$pageScript = "
    // Function to show the add user form
    function showAddUserForm() {
        document.getElementById('add-user-form').style.display = 'block';
    }

    // Function to hide the add user form
    function hideAddUserForm() {
        document.getElementById('add-user-form').style.display = 'none';
    }

    // Function to show the edit user form and populate it with user data
    function editUser(userId) {
        // Find the user data
        const users = " . json_encode($users) . ";
        const user = users.find(u => u.id === userId);

        if (user) {
            // Populate the form fields
            document.getElementById('edit-id').value = user.id;
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-username').value = user.username || '';
            document.getElementById('edit-email').value = user.email;
            document.getElementById('edit-role').value = user.role || 'member';

            // Clear password fields
            document.getElementById('edit-password').value = '';
            document.getElementById('edit-confirm-password').value = '';

            // Show the form
            document.getElementById('edit-user-form').style.display = 'block';
        }
    }

    // Function to hide the edit user form
    function hideEditUserForm() {
        document.getElementById('edit-user-form').style.display = 'none';
    }

    // Function to confirm and delete a user
    function deleteUser(userId, userName) {
        if (confirm('Are you sure you want to delete user \"' + userName + '\"?')) {
            window.location.href = 'auth.php?action=delete_user&id=' + userId;
        }
    }
";

// Include footer
include 'footer.php';
?>
