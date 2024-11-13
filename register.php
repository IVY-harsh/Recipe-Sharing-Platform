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
$user = $_POST['username'];
$email = $_POST['email'];
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Insert data into database
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user, $email, $pass);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        .status-container {
            max-width: 400px;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .status-message {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        a {
            display: inline-block;
            margin-top: 1rem;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            transition: color 0.3s;
        }

        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="status-container">
        <?php
        if ($stmt->execute()) {
            echo "<p class='status-message success'>Registration successful! You can now <a href='login.php'>log in</a>.</p>";
        } else {
            echo "<p class='status-message error'>Error: Unable to register. Please try again later.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
