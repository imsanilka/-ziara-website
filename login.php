<?php
session_start();

// Redirect if already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: " . ($_SESSION["role"] === "admin" ? "admin/index.php" : "index.php"));
    exit;
}

require_once 'includes/config.php';

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $password = '';
$username_err = $password_err = $login_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter username.';
    } else {
        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        $sql = 'SELECT id, username, password, role FROM users WHERE username = ?';
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $param_username);
            $param_username = $username;
            
            try {
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows == 1) {
                        $user = $result->fetch_assoc();
                        
                        if (password_verify($password, $user['password'])) {
                            // Password is correct, start a new session
                            session_regenerate_id(true);
                            
                            // Store data in session variables
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['role'] = $user['role'];

                            // Update last login timestamp
                            $update_sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
                            if ($update_stmt = $conn->prepare($update_sql)) {
                                $update_stmt->bind_param('i', $user['id']);
                                $update_stmt->execute();
                                $update_stmt->close();
                            }

                            // Redirect user
                            header('location: ' . ($user['role'] === 'admin' ? 'admin/index.php' : 'index.php'));
                            exit;
                        } else {
                            $login_err = 'Invalid username or password.';
                        }
                    } else {
                        $login_err = 'Invalid username or password.';
                    }
                } else {
                    throw new Exception('Database query failed.');
                }
            } catch (Exception $e) {
                error_log("Login Error: " . $e->getMessage());
                $login_err = 'An error occurred. Please try again later.';
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header fw-bold">Login</div>
                    <div class="card-body">
                        <?php
                        if (!empty($login_err)) {
                            echo '<div class="alert alert-danger">' . $login_err . '</div>';
                        }
                        ?>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark">Login</button>
                            </div>
                            <p class="mt-3">Don't have an account? <a href="register.php">Sign up now</a>.</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const username = document.querySelector('input[name="username"]').value.trim();
        const password = document.querySelector('input[name="password"]').value.trim();
        
        if (!username || !password) {
            e.preventDefault();
            alert('Please fill in all fields.');
            return false;
        }
    });
    </script>
</body>
</html>

