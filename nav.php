<?php
session_start();
?>

<nav>
    <a href="home.php">Home</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- If the user is logged in, show these links -->
        <a href="upload_recipe.php">Upload Recipe</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <!-- If the user is not logged in, show these links -->
        <a href="register.html">Register</a>
        <a href="login.html">Login</a>
    <?php endif; ?>
</nav>
