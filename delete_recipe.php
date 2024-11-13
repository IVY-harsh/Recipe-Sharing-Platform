<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe_platform");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Check if recipe ID is provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recipe_id'])) {
    $recipe_id = intval($_POST['recipe_id']);

    // Check if the user is the owner of the recipe
    $stmt = $conn->prepare("SELECT user_id FROM recipes WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->bind_result($owner_id);
    $stmt->fetch();
    $stmt->close();

    if ($owner_id != $_SESSION['user_id']) {
        echo "You are not authorized to delete this recipe.";
        exit();
    }

    // Delete the recipe
    $stmt = $conn->prepare("DELETE FROM recipes WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipe_id);
    
    if ($stmt->execute()) {
        echo "Recipe deleted successfully!";
        header("Location: home.php");
        exit();
    } else {
        echo "Error deleting recipe: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
