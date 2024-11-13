<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe_platform");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Execute the query to fetch all recipes
$sql = "SELECT recipe_id, title FROM recipes ORDER BY recipe_id DESC"; // Show latest recipes first
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipe Sharing Platform - Home</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
        }
        a {
            text-decoration: none;
            color: #0056b3;
        }
        a:hover {
            color: #004080;
        }

        /* Navigation Bar */
        .navbar {
            background-color: #333;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            color: #ffffff;
            margin: 0 20px;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        .navbar a:hover {
            color: #ffcc00;
        }

        /* Header Banner */
        .banner {
            background: linear-gradient(to bottom right, #00c6ff, #0072ff);
            color: #ffffff;
            text-align: center;
            padding: 80px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .banner h1 {
            font-size: 3em;
            margin: 0;
            font-weight: 700;
        }
        .banner p {
            font-size: 1.4em;
            margin-top: 10px;
        }

        /* Recipes Container */
        .recipes-container {
            padding: 40px 20px;
            text-align: center;
        }
        .recipes-container h2 {
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #333;
        }

        /* Recipe Grid */
        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .recipe-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 250px; /* Fixed height for consistency */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .recipe-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
        .recipe-card h3 {
            font-size: 1.8em;
            color: #333;
            margin: 20px 0 10px;
        }
        .recipe-card .read-more {
            display: inline-block;
            margin-top: 15px;
            color: #ffffff;
            background-color: #ff5733;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .recipe-card .read-more:hover {
            background-color: #e04e2a;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="home.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="upload_recipe.php">Upload Recipe</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="register.html">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>

    <!-- Header Banner -->
    <header class="banner">
        <h1>Welcome to the Recipe Sharing Platform!</h1>
        <p>Discover, share, and enjoy recipes from around the world.</p>
    </header>

    <!-- Recipes Section -->
    <section class="recipes-container">
        <h2>Our Latest Recipes</h2>
        <div class="recipe-grid">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="recipe-card">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>Explore this recipe to learn more!</p>
                    <a href="view_recipe.php?id=<?php echo $row['recipe_id']; ?>" class="read-more">Read More</a>
                </div>
            <?php } ?>
        </div>
    </section>
</body>
</html>

<?php $conn->close(); ?>
