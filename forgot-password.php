<?php
// Include the database connection file
include_once 'includes/db.php';
// Include necessary files
include_once 'includes/header.php';

// Function to display forgot password form with errors
function displayForgotPasswordForm($error = '')
{
    echo '<div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Forgot Password</h2>';

    if ($error != '') {
        echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
    }

    echo '<form action="forgot-password.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </div>
    </div>';
}

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = sanitizeInput($_POST['email']);

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));

        // Update the user's reset_token in the database
        $updateSql = "UPDATE users SET reset_token = '$resetToken' WHERE id = " . $row['id'];

        if ($conn->query($updateSql) === TRUE) {    
            // Provide a link to the reset password page with the token
            echo 'To reset your password, <a href="reset_password.php?token=' . $resetToken . '">click here</a>.';
        } else {
            displayForgotPasswordForm('Error updating reset token.');
        }
    } else {
        // Email not found
        displayForgotPasswordForm('Email not found.');
    }
} else {
    // Display forgot password form
    displayForgotPasswordForm();
}

// Include necessary files
include_once 'includes/footer.php';
?>
