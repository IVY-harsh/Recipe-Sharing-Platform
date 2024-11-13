<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS recipe_platform";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db("recipe_platform");

// Create `users` table
$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table `users` created successfully.<br>";
} else {
    echo "Error creating `users` table: " . $conn->error;
}

// Create `recipes` table
$sql = "CREATE TABLE IF NOT EXISTS recipes (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    steps TEXT NOT NULL,
    category VARCHAR(50),
    image_path VARCHAR(255),
    user_id INT,
    date_uploaded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table `recipes` created successfully.<br>";
} else {
    echo "Error creating `recipes` table: " . $conn->error;
}

// Create `comments` table
$sql = "CREATE TABLE IF NOT EXISTS comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    user_id INT,
    comment_text TEXT NOT NULL,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table `comments` created successfully.<br>";
} else {
    echo "Error creating `comments` table: " . $conn->error;
}

// Create `ratings` table
$sql = "CREATE TABLE IF NOT EXISTS ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    user_id INT,
    rating_value INT CHECK (rating_value BETWEEN 1 AND 5),
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table `ratings` created successfully.<br>";
} else {
    echo "Error creating `ratings` table: " . $conn->error;
}

// Close connection
$conn->close();
?>
