<?php
// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-black">
    <div class="container">
        <a class="navbar-brand" href="index.php">ZIARA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="shop.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" href="shop.php">Shop</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="shop.php">All Products</a></li>
                        <li><a class="dropdown-item" href="men.php">Men</a></li>
                        <li><a class="dropdown-item" href="women.php">Women</a></li>
                        <li><a class="dropdown-item" href="kids.php">Kids</a></li>
                        <li><a class="dropdown-item" href="accessories.php">Accessories</a></li>
                        
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="support.php">Support</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="cart.php"><i class="bi bi-cart-plus"></i></a>
                </li>
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="user.php" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <?php if ($_SESSION["role"] === "admin"): ?>
                                <li><a class="dropdown-item" href="">User name</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/index.php">Admin Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            

                <?php else: ?> 
                      
                    <li class="nav-item dropdown ">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="login.php">Login</a></li>
                            <li><a class="dropdown-item" href="register.php">Register</a></li>
                        </ul>
                    </li>        
                    <?php endif; ?>
            </ul>   
        </div>     
        
    </div>
</nav>

