<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recipe_platform");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("Recipe ID not provided.");
}

$recipe_id = intval($_GET['id']);
$sql = "SELECT * FROM recipes WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();

if (!$recipe) {
    die("Recipe not found.");
}

// Fetch comments
$comments_sql = "SELECT c.comment_text, c.date_posted, u.username 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.user_id 
                 WHERE c.recipe_id = ? ORDER BY c.date_posted DESC";
$comments_stmt = $conn->prepare($comments_sql);
$comments_stmt->bind_param("i", $recipe_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();

// Fetch average rating
$rating_sql = "SELECT AVG(rating_value) as avg_rating FROM ratings WHERE recipe_id = ?";
$rating_stmt = $conn->prepare($rating_sql);
$rating_stmt->bind_param("i", $recipe_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();
$average_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 'Not rated yet';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipe Details</title>
    <style>
        /* Basic Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            flex-direction: column;
        }

        .recipe-card {
            background: #fff;
            width: 100%;
            max-width: 700px;
            border-radius: 10px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            overflow: hidden;
            transform: translateY(10px);
            animation: fadeInUp 0.7s ease-out;
        }

        h1 {
            font-size: 2.5rem;
            color: #ff5722;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .section-title {
            font-size: 1.2rem;
            color: #444;
            margin-top: 20px;
            text-transform: uppercase;
            border-bottom: 2px solid #ff5722;
            padding-bottom: 5px;
            font-weight: bold;
        }

        p {
            line-height: 1.6;
            color: #666;
            margin-top: 10px;
        }

        .rating {
            margin-top: 20px;
            font-size: 1.1rem;
            color: #333;
        }

        .rating span {
            color: #ff5722;
            font-weight: bold;
        }

        .rating-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .rating-form input[type="radio"] {
            margin-right: 5px;
            cursor: pointer;
        }

        .comments {
            margin-top: 40px;
        }

        .comment {
            background-color: #fafafa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
        }

        .comment strong {
            color: #ff5722;
            font-weight: 600;
        }

        .comment-form textarea {
            width: 100%;
            height: 100px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            margin-top: 10px;
            margin-bottom: 15px;
            transition: border-color 0.3s;
        }

        .comment-form textarea:focus {
            border-color: #ff5722;
        }

        .button {
            background-color: #ff5722;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .button:hover {
            background-color: #ff4500;
            transform: scale(1.05);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="recipe-card">
        <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>

        <div class="section-title">Ingredients</div>
        <p><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>

        <div class="section-title">Steps</div>
        <p><?php echo nl2br(htmlspecialchars($recipe['steps'])); ?></p>

        <div class="rating">
            <div>Average Rating: <span><?php echo $average_rating; ?> / 5</span></div>
        </div>

        <div class="rating-form">
            <form action="add_rating.php" method="POST" style="display: inline;">
                <label for="rating">Rate this Recipe:</label>
                <input type="radio" name="rating" value="1"> 1
                <input type="radio" name="rating" value="2"> 2
                <input type="radio" name="rating" value="3"> 3
                <input type="radio" name="rating" value="4"> 4
                <input type="radio" name="rating" value="5"> 5
                <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                <button type="submit" class="button">Submit Rating</button>
            </form>
        </div>

        <div class="comments">
            <div class="section-title">Comments</div>
            <?php while ($comment = $comments_result->fetch_assoc()): ?>
                <div class="comment">
                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                    <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                </div>
            <?php endwhile; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="comment-form">
                    <form action="add_comment.php" method="POST" style="width: 100%;">
                        <textarea name="comment" placeholder="Add your comment..." required></textarea>
                        <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                        <button type="submit" class="button">Post Comment</button>
                    </form>
                </div>
            <?php else: ?>
                <p>Please <a href="login.php">log in</a> to submit a rating or comment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
