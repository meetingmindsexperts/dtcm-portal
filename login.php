<?php
// Include necessary files
include_once 'includes/header.php';

// Include the database connection file
include_once 'includes/db.php';

// Function to sanitize input
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

// Function to redirect to another page
function redirectTo($page)
{
    header("Location: $page");
    exit();
}

// Function to display login form with errors
function displayLoginForm($error = '')
{
    echo '<div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Login</h2>';

    if ($error != '') {
        echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
    }

    echo '<form action="login.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <p class="mt-3"><a href="forgot-password.php">Forgot Password?</a></p>
            </form>
        </div>
    </div>';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

    // Check user credentials
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the entered password with the MD5 hashed password from the database
        if (md5($password) === $row['password']) {
            // Successful login
            // You can set session variables or redirect to the dashboard
            echo 'Login successful! Redirecting to the dashboard...';
            $url =  $baseUrl .'/dashboard.php'; // replace with your actual URL
            header("Location: $url");
            exit;
        } else {
            // Incorrect password
            displayLoginForm('Incorrect username or password.');
        }
    } else {
        // User not found
        displayLoginForm('Incorrect username or password.');
    }
} else {
    // Display login form
    displayLoginForm();
}


// Include necessary files
include_once 'includes/footer.php';
?>
