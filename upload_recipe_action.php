<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login page if not logged in
    exit;
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "recipe_platform");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $title = $_POST['title'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];

    // Handle file upload (if any)
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Insert recipe into the database
    $sql = "INSERT INTO recipes (user_id, title, ingredients, instructions, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $_SESSION['user_id'], $title, $ingredients, $instructions, $image);

    if ($stmt->execute()) {
        echo "Recipe uploaded successfully!";
    } else {
        echo "Error uploading recipe: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Recipe</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            margin: auto;
        }
        .container h2 {
            text-align: center;
            color: #333;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="file"] {
            padding: 10px;
            margin: 10px 0;
        }
        button {
            background-color: #0072ff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload Your Recipe</h2>
        <form action="upload_recipe.php" method="POST" enctype="multipart/form-data">
            <label for="title">Recipe Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="ingredients">Ingredients:</label>
            <textarea id="ingredients" name="ingredients" rows="5" required></textarea>

            <label for="instructions">Instructions:</label>
            <textarea id="instructions" name="instructions" rows="5" required></textarea>
            <button type="submit">Upload Recipe</button>
        </form>
    </div>
</body>
</html>
