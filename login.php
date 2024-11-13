<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe_platform");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Login logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query to check the user
    $sql = "SELECT user_id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Set session variable for the logged-in user
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            // Redirect to home page after successful login
            header("Location: home.php");
            exit;
        } else {
            $error = "Incorrect username or password!";
        }
    } else {
        $error = "User not found!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f3f4f6;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        p.error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            font-weight: bold;
            margin-top: 1rem;
            color: #555;
            text-align: left;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.3rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.75rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .form-footer {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #777;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
            }
            button[type="submit"] {
                padding: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to Recipe Platform</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <div class="form-footer">
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </div>
    </div>
</body>
</html>
