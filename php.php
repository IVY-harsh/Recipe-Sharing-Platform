<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_platform";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$email = $_POST['email'];
$pass = $_POST['password'];

// Retrieve user data
$sql = "SELECT user_id, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['password'])) {
        echo "Login successful! Welcome back!";
        // Start session or redirect to another page
    } else {
        echo "Invalid password.";
    }
} else {
    echo "No account found with that email.";
}

$stmt->close();
$conn->close();
?>
