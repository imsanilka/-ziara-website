<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../login.php");
    exit;
}

require_once "../includes/config.php";

$name = $category = $description = $price = $stock = $image = "";
$name_err = $category_err = $description_err = $price_err = $stock_err = $image_err = "";

// Handle Add/Edit Product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a product name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["category"]))) {
        $category_err = "Please enter a category.";
    } else {
        $category = trim($_POST["category"]);
    }

    $description = trim($_POST["description"]);

    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter a price.";
    } elseif (!is_numeric(trim($_POST["price"]))) {
        $price_err = "Price must be a number.";
    } else {
        $price = trim($_POST["price"]);
    }

    if (empty(trim($_POST["stock"]))) {
        $stock_err = "Please enter stock quantity.";
    } elseif (!ctype_digit(trim($_POST["stock"]))) {
        $stock_err = "Stock must be an integer.";
    } else {
        $stock = trim($_POST["stock"]);
    }

    // Handle image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../assets/images/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check file size
        if ($_FILES["image"]["size"] > 500000) {
            $image_err = "Sorry, your file is too large.";
        }

        if (empty($image_err)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $image_name;
            } else {
                $image_err = "Sorry, there was an error uploading your file.";
            }
        }
    } else if (isset($_POST["current_image"])) {
        $image = $_POST["current_image"]; // Keep existing image if not uploading new one
    }

    // Check input errors before inserting/updating in database
    if (empty($name_err) && empty($category_err) && empty($price_err) && empty($stock_err) && empty($image_err)) {
        if (isset($_POST["product_id"]) && !empty($_POST["product_id"])) {
            // Update product
            $product_id = $_POST["product_id"];
            $sql = "UPDATE products SET name=?, category=?, description=?, price=?, stock=?, image=? WHERE id=?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssdssi", $name, $category, $description, $price, $stock, $image, $product_id);
                if ($stmt->execute()) {
                    header("location: inventory.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            // Add new product
            $sql = "INSERT INTO products (name, category, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssdss", $name, $category, $description, $price, $stock, $image);
                if ($stmt->execute()) {
                    header("location: inventory.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Handle Delete Product
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if product exists in any orders
        $check_sql = "SELECT COUNT(*) as count FROM order_items WHERE product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $delete_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        $check_stmt->close();

        if ($row['count'] > 0) {
            // Product is referenced in orders - show error message
            echo "<script>
                alert('Cannot delete this product because it is associated with existing orders.');
                window.location.href='inventory.php';
            </script>";
            exit();
        }

        // If no references exist, delete the product
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $conn->commit();
            echo "<script>
                alert('Product deleted successfully!');
                window.location.href='inventory.php';
            </script>";
            exit();
        } else {
            throw new Exception("Error deleting product");
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href='inventory.php';
        </script>";
        exit();
    }
}

// Handle Edit Product
if (isset($_GET["edit_id"])) {
    $edit_id = $_GET["edit_id"];
    $sql = "SELECT * FROM products WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $name = $row["name"];
            $category = $row["category"];
            $description = $row["description"];
            $price = $row["price"];
            $stock = $row["stock"];
            $image = $row["image"];
        }
        $stmt->close();
    }
}

// Fetch all products
$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - ZIARA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .card-header {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-custom {
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 500;
        }
        .stock-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include "../includes/admin-navbar.php"; ?>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="bi bi-box-seam me-2"></i>Inventory Management</h2>
                    <button class="btn btn-dark btn-custom" data-bs-toggle="collapse" data-bs-target="#addProductForm">
                        <i class="bi bi-plus-lg me-2"></i>Add New Product
                    </button>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card collapse" id="addProductForm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-<?php echo isset($_GET["edit_id"]) ? "pencil" : "plus-circle"; ?> me-2"></i>
                            <?php echo isset($_GET["edit_id"]) ? "Edit Product" : "Add New Product"; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <?php if (isset($_GET["edit_id"])): ?>
                                <input type="hidden" name="product_id" value="<?php echo $_GET["edit_id"]; ?>">
                                <input type="hidden" name="current_image" value="<?php echo $image; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? "is-invalid" : ""; ?>" value="<?php echo $name; ?>">
                                        <div class="invalid-feedback"><?php echo $name_err; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <input type="text" name="category" class="form-control <?php echo (!empty($category_err)) ? "is-invalid" : ""; ?>" value="<?php echo $category; ?>">
                                        <div class="invalid-feedback"><?php echo $category_err; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo $description; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Price ($)</label>
                                        <input type="text" name="price" class="form-control <?php echo (!empty($price_err)) ? "is-invalid" : ""; ?>" value="<?php echo $price; ?>">
                                        <div class="invalid-feedback"><?php echo $price_err; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Stock</label>
                                        <input type="text" name="stock" class="form-control <?php echo (!empty($stock_err)) ? "is-invalid" : ""; ?>" value="<?php echo $stock; ?>">
                                        <div class="invalid-feedback"><?php echo $stock_err; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Product Image</label>
                                        <input type="file" name="image" class="form-control <?php echo (!empty($image_err)) ? "is-invalid" : ""; ?>">
                                        <div class="invalid-feedback"><?php echo $image_err; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save me-2"></i>Save Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>#<?php echo $product["id"]; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="../assets/images/<?php echo htmlspecialchars($product["image"]); ?>" class="product-img me-3">
                                                    <span><?php echo htmlspecialchars($product["name"]); ?></span>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-info"><?php echo htmlspecialchars($product["category"]); ?></span></td>
                                            <td>$<?php echo number_format($product["price"], 2); ?></td>
                                            <td>
                                                <span class="stock-badge <?php echo $product["stock"] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                                    <?php echo htmlspecialchars($product["stock"]); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="inventory.php?edit_id=<?php echo $product["id"]; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="inventory.php?delete_id=<?php echo $product["id"]; ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Are you sure you want to delete this product?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox display-4 text-muted"></i>
                                            <p class="mt-2 mb-0">No products found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show form if editing
        <?php if (isset($_GET["edit_id"])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            var addProductForm = document.getElementById('addProductForm');
            var bsCollapse = new bootstrap.Collapse(addProductForm, {
                toggle: false
            });
            bsCollapse.show();
        });
        <?php endif; ?>
    </script>
</body>
</html>

