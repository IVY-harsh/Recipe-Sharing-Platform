<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe_platform");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['recipe_id']) && isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $recipe_id = intval($_POST['recipe_id']);
    $comment_text = $conn->real_escape_string($_POST['comment']);
    $user_id = intval($_SESSION['user_id']);

    $stmt = $conn->prepare("INSERT INTO comments (recipe_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $recipe_id, $user_id, $comment_text);
    if ($stmt->execute()) {
        echo "Comment added successfully.";
    } else {
        echo "Error adding comment: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Comment, recipe ID, or user session not set.";
}
$conn->close();
?>
