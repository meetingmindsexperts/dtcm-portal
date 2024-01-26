<?php

// Include the database connection file
include_once 'includes/db.php';
// Include necessary files
include_once 'includes/header.php';

$errors = $_SESSION['errors'] = [];
$messages = $_SESSION['messages'] = [];

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
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
            $_SESSION['username'] = $row['username'];
            // redirect to the dashboard
            $_SESSION['messages'] = 'Login successful! Redirecting to the dashboard...';
            $url = $baseUrl . '/dashboard.php'; // replace with your actual URL
            echo "
            
            <script>
                setTimeout(function () {
                    window.location.href = '$url';
                }, 1000); // 3 seconds delay
            </script>";
            exit;
        } else {
            // Incorrect password
            $errors[] = 'Incorrect username or password.';
        }
    } else {
        // User not found
        $errors[] = 'Incorrect username or password.';
    }
}
?>
<div class="conatiner py-5 mt-5"> 
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php } ?>
            <?php foreach ($_SESSION['messages'] as $message) { ?>
                <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
            <?php
                unset($message);
            } ?>
            <div class="text-start">
                <img src="https://meetingmindsexperts.com/wp-content/uploads/2018/11/MME-WEB-LOGO-12-11-18.png" alt="logo" style="width: 185px;">
                <h4 class="mt-1 mb-5 pb-1">Meeting Minds Experts</h4>
            </div>
            <h2>Login</h2>
            <form action="login.php" method="post">
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
    </div>
</div>

<?php
// Include necessary files
include_once 'includes/footer.php';
?>
