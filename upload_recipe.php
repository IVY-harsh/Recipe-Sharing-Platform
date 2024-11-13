<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe_platform");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from POST request, using null coalescing to avoid undefined warnings
    $title = $_POST['title'] ?? '';
    $ingredients = $_POST['ingredients'] ?? '';
    $steps = $_POST['steps'] ?? '';
    $category = $_POST['category'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Basic validation to ensure fields are not empty
    if ($title && $ingredients && $steps && $category) {
        // Image upload logic
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_path = 'uploads/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO recipes (title, ingredients, steps, category, image_path, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $title, $ingredients, $steps, $category, $image_path, $user_id);

        if ($stmt->execute()) {
            header("Location: home.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Recipe</title>
    <style>
        /* Add basic styles for form */
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        form { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, textarea, select { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; }
        button { background-color: #28a745; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <h2>Upload a New Recipe</h2>
    <form action="upload_recipe.php" method="POST" enctype="multipart/form-data">
        <label for="title">Recipe Title</label>
        <input type="text" id="title" name="title" required>

        <label for="ingredients">Ingredients</label>
        <textarea id="ingredients" name="ingredients" rows="5" required></textarea>

        <label for="steps">Steps</label>
        <textarea id="steps" name="steps" rows="7" required></textarea>

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="Appetizer">Appetizer</option>
            <option value="Main Course">Main Course</option>
            <option value="Dessert">Dessert</option>
        </select>

        <label for="image">Upload Image (optional)</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Upload Recipe</button>
    </form>
</body>
</html>
