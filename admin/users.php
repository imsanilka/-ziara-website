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
    <title>User Management - ZIARA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-5">
        <h1 class="mb-4">User Management</h1>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user["id"]; ?></td>
                            <td><?php echo htmlspecialchars($user["username"]); ?></td>
                            <td><?php echo htmlspecialchars($user["email"]); ?></td>
                            <td>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
                                    <select name="new_role" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                        <option value="customer" <?php echo ($user["role"] == "customer") ? "selected" : ""; ?>>Customer</option>
                                        <option value="admin" <?php echo ($user["role"] == "admin") ? "selected" : ""; ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo $user["created_at"]; ?></td>
                            <td>
                                <a href="users.php?delete_id=<?php echo $user["id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>

