// Allow password visibility toggle in forms
function setupPasswordStrengthCheck(fieldId) {
    let field = document.getElementById(fieldId);
    if (!field) return;

    field.addEventListener('keyup', function() {
        let password = this.value;

        // Use edit indicator for edit fields, regular indicator for others
        let strengthIndicator;
        if (fieldId.includes('edit') || fieldId.includes('_edit')) {
            strengthIndicator = document.getElementById('passwordStrength_edit');
        } else {
            strengthIndicator = document.getElementById('passwordStrength');
        }

        if (!strengthIndicator) return;

        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
        if (password.match(/\d+/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;

        if (score < 2) {
            strengthIndicator.textContent = 'Weak';
            strengthIndicator.style.color = 'red';
        } else if (score < 4) {
            strengthIndicator.textContent = 'Medium';
            strengthIndicator.style.color = 'orange';
        } else {
            strengthIndicator.textContent = 'Strong';
            strengthIndicator.style.color = 'green';
        }
    });
}

// Set up password strength checking for both fields
setupPasswordStrengthCheck('register-password');
setupPasswordStrengthCheck('password');
setupPasswordStrengthCheck('add-password');
setupPasswordStrengthCheck('edit-password');

// Toggle password visibility in the password form field
function togglePasswordVisibility(icon) {
    var fields = [
        document.getElementById("register-password"),
        document.getElementById("register-confirm-password"),
        document.getElementById("password"),
        document.getElementById("confirm_password"),
        document.getElementById("edit-password"),
        document.getElementById("edit-confirm-password"),
        document.getElementById("add-password"),
        document.getElementById("add-confirm-password")
    ];

    // Find the first field that actually exists on this page
    var activeField = fields.find(function(field) {
        return field !== null;
    });

    if (!activeField) return; // No password fields found

    var isPassword = activeField.type === "password";

    fields.forEach(function(field) {
        if (field) { // Only toggle if field exists
            field.type = isPassword ? "text" : "password";
        }
    });

    icon.className = isPassword ? "fas fa-eye-slash password-toggle-icon" : "fas fa-eye password-toggle-icon";
}