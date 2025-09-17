<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../login.php");
    exit;
}

require_once "../includes/config.php";

// Handle Change Role
if (isset($_POST["user_id"]) && isset($_POST["new_role"])) {
    $user_id = $_POST["user_id"];
    $new_role = $_POST["new_role"];

    $sql = "UPDATE users SET role = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $new_role, $user_id);
        if ($stmt->execute()) {
            // Success
        } else {
            echo "Error updating record: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle Delete User
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $sql = "DELETE FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            // Success
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle Edit User
if (isset($_POST["edit_user_id"])) {
    $edit_user_id = $_POST["edit_user_id"];
    $new_username = $_POST["edit_username"];
    $new_email = $_POST["edit_email"];

    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $new_username, $new_email, $edit_user_id);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER["PHP_SELF"] . "?edit=success");
        } else {
            echo "Error updating user: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all users
$users = [];
$sql = "SELECT id, username, email, role, created_at FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - ZIARA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(to right, #010306ff, #070707ff);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
        }
        .role-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        .table > :not(caption) > * > * {
            padding: 1rem 1rem;
        }
        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include "../includes/admin-navbar.php"; ?>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="bi bi-people me-2"></i>Customer Management</h2>
                    <div>
                        <button class="btn btn-outline-primary me-2">
                            <i class="bi bi-download me-2"></i>Export
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-printer me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0">All Customers</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($users)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="customer-avatar bg-dark text-white me-3">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($user["username"]); ?></h6>
                                                    <small class="text-muted">#<?php echo $user["id"]; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user["email"]); ?></td>
                                        <td>
                                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
                                                <select name="new_role" class="form-select form-select-sm w-auto" 
                                                        onchange="this.form.submit()"
                                                        style="border-radius: 20px;">
                                                    <option value="customer" <?php echo ($user["role"] == "customer") ? "selected" : ""; ?>>Customer</option>
                                                    <option value="admin" <?php echo ($user["role"] == "admin") ? "selected" : ""; ?>>Admin</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-event me-2"></i>
                                                <?php echo date('M d, Y', strtotime($user["created_at"])); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="customers.php?delete_id=<?php echo $user["id"]; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <p class="h4 mt-3">No customers found</p>
                        <p class="text-muted">There are no customers registered in the system yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="edit_user_id" id="editUserId">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="edit_username" id="editUsername" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="edit_email" id="editEmail" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function editUser(userId, username, email) {
        // Populate the modal form
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUsername').value = username;
        document.getElementById('editEmail').value = email;
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }

    // Show success message if edit was successful
    <?php if (isset($_GET['edit']) && $_GET['edit'] === 'success'): ?>
        alert('Customer updated successfully!');
        // Remove the query parameter
        window.history.replaceState({}, document.title, window.location.pathname);
    <?php endif; ?>
    </script>
</body>
</html>

