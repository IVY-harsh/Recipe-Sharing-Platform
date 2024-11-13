<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe_platform");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['recipe_id'], $_POST['rating'], $_SESSION['user_id'])) {
    $recipe_id = intval($_POST['recipe_id']);
    $rating = intval($_POST['rating']);
    $user_id = intval($_SESSION['user_id']);

    $stmt = $conn->prepare("INSERT INTO ratings (recipe_id, user_id, rating_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating_value = ?");
    $stmt->bind_param("iiii", $recipe_id, $user_id, $rating, $rating);

    if ($stmt->execute()) {
        echo "Rating submitted successfully.";
    } else {
        echo "Error submitting rating: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Rating, recipe ID, or user session not set.";
}
$conn->close();
?>
