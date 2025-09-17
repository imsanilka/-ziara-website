<?php
session_start();
require_once 'includes/config.php';

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $email = $password = $confirm_password = '';
$username_err = $email_err = $password_err = $confirm_password_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter a username.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['username']))) {
        $username_err = 'Username can only contain letters, numbers, and underscores.';
    } else {
        try {
            $sql = 'SELECT id FROM users WHERE username = ?';
            if ($stmt = $conn->prepare($sql)) {
                $param_username = trim($_POST['username']);
                $stmt->bind_param('s', $param_username);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows == 1) {
                        $username_err = 'This username is already taken.';
                    } else {
                        $username = $param_username;
                    }
                } else {
                    throw new Exception($stmt->error);
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            $username_err = 'An error occurred. Please try again later.';
        }
    }

    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter an email.';
    } elseif (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $email_err = 'Please enter a valid email address.';
    } else {
        try {
            $sql = 'SELECT id FROM users WHERE email = ?';
            if ($stmt = $conn->prepare($sql)) {
                $param_email = trim($_POST['email']);
                $stmt->bind_param('s', $param_email);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows == 1) {
                        $email_err = 'This email is already registered.';
                    } else {
                        $email = $param_email;
                    }
                } else {
                    throw new Exception($stmt->error);
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            $email_err = 'An error occurred. Please try again later.';
        }
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = 'Password must have at least 6 characters.';
    } else {
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = 'Please confirm password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = 'Password did not match.';
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        try {
            $sql = 'INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, "user", NOW())';
            if ($stmt = $conn->prepare($sql)) {
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bind_param('sss', $username, $email, $param_password);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Registration successful! Please login.";
                    header('location: login.php');
                    exit;
                } else {
                    throw new Exception($stmt->error);
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            echo '<div class="alert alert-danger">Something went wrong. Please try again later.</div>';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header fw-bold">Register</div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark">Register</button>
                            </div>
                            <p class="mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>

