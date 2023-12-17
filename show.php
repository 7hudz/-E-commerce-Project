<!-- show.php -->
<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "articaldatabase";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);



// Check if the product ID is set and not empty
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $productId = $_GET['id'];

    // Query to retrieve product information
    $productSql = "SELECT id, title, description, image_path FROM articaldatabase_db WHERE id = $productId";
    $productResult =mysqli_query($conn,$productSql);

    if ($row = mysqli_fetch_assoc($productResult)) {
        $productTitle = $row["title"];
        $productDescription = $row["description"];
        $productImagePath = $row["image_path"];
    

        // Query to retrieve comments for the product, including user_id
        $commentsSql = "SELECT id, User_id, user_name, comment_text, timestamp, rating FROM comments WHERE Product_id = $productId";
        $commentsResult = mysqli_query($conn,$commentsSql);

        if (!$commentsResult) {
            echo "Error retrieving comments: " . mysqli_connect_errno();
            exit();
        }

        // Check if the user is logged in
        if (isset($_SESSION['id'])) {
            $userId = $_SESSION['id'];

            // Query to retrieve user information
            $userSql = "SELECT name FROM login_db.user WHERE id = $userId";
            $userResult = mysqli_query($conn,$userSql);

            if ($userResult && $userResult->num_rows > 0) {
                $userRow = mysqli_fetch_assoc($userResult);
                $userName = $userRow["name"];
            } else {
                echo "User not found";
                exit();
            }

            // Handle the form submission to add comments and ratings
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                if (isset($_POST['comment']) && isset($_POST['rating'])) {
                    $commentText = $_POST['comment'];
                    $rating = $_POST['rating'];

                    // Prevent multiple submissions from the same user within a short time (e.g., 10 seconds)
                    if (!isset($_SESSION['last_submission']) || (time() - $_SESSION['last_submission']) > 10) {
                        // Insert comment into the database
                        $insertCommentSql = "INSERT INTO comments (Product_id, User_id, user_name, comment_text, rating) VALUES (?, ?, ?, ?, ?)";
                        $insertCommentStmt = mysqli_prepare($conn,$insertCommentSql);
                        $insertCommentStmt->bind_param("iisss", $productId, $userId, $userName, $commentText, $rating);
                        $insertCommentStmt->execute();

                        // Update the last submission timestamp
                        $_SESSION['last_submission'] = time();

                        // Redirect after form submission to prevent resubmission on page refresh
                        header("Location: show.php?id=$productId");
                        exit();
                    } else {
                        echo "Please wait before submitting another comment.";
                    }
                } elseif (isset($_POST['comment_id']) && isset($_POST['reply_text'])) {
                    // Handle reply submission
                    $commentId = $_POST['comment_id'];
                    $replyText = $_POST['reply_text'];

                    // Insert reply into the database
                    $insertReplySql = "INSERT INTO replies (comment_id, user_id, user_name, reply_text) VALUES (?, ?, ?, ?)";
                    $insertReplyStmt = mysqli_prepare($conn,$insertReplySql);
                    $insertReplyStmt->bind_param("iiss", $commentId, $userId, $userName, $replyText);
                    $insertReplyStmt->execute();

                    // Redirect after form submission to prevent resubmission on page refresh
                    header("Location: show.php?id=$productId");
                    exit();
                } elseif (isset($_POST['delete_comment_id'])) {
                    // Handle comment deletion
                    $commentId = $_POST['delete_comment_id'];
                    $deleteCommentSql = "DELETE FROM comments WHERE id = ? AND Product_id = ?";
                    $deleteCommentStmt = mysqli_prepare($conn,$deleteCommentSql);
                    $deleteCommentStmt->bind_param("ii", $commentId, $productId);
                    $deleteCommentStmt->execute();

                    // Redirect after comment deletion
                    header("Location: show.php?id=$productId");
                    exit();
                }
            }
        } else {
            echo "User not logged in.";
        }
    } else {
        echo "Product not found";
        // You may add a link to go back to the index.php or handle the situation differently
        // Don't perform a redirect here to avoid the loop
        exit();
    }
} else {
    // Handle the situation when the product ID is not set or empty
    echo "Invalid product ID";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Product - <?php echo $productTitle; ?></title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body id="bodystyle">

    <header>
        <!-- Add your header content here if needed -->
    </header>

    <section class="product-item">
        <img src="<?php echo $productImagePath; ?>" alt="<?php echo $productTitle; ?>">
        <h2><?php echo $productTitle; ?></h2>
        <p><?php echo $productDescription; ?></p>
    </section>

    <?php
    // Check if the user is logged in to allow adding comments
    if (isset($_SESSION['id'])) {
        echo "<form action='show.php?id=$productId' method='post' class='comment-form'>";
        echo "<input type='hidden' name='Product_id' value='$productId'>";
        echo "<label for='comment'>Add Comment:</label>";
        echo "<textarea id='comment' name='comment' required></textarea>";

        // Add a rating input field with stars
        echo "<div class='rating-stars'>";
        echo "<label for='rating'>Rating:</label>";

        // Loop to generate stars
        for ($i = 5; $i >= 1; $i--) {
            echo "<input type='radio' id='star$i' name='rating' value='$i'>";
            echo "<label for='star$i'></label>";
        }

        echo "</div>";

        echo "<button type='submit'>Submit Comment</button>";
        echo "</form>";
    }
    ?>

    <div class="comments-section">
        <h3>Verified Customer Feedback:</h3>

        <?php
        // Initialize $userName when the user is not logged in
        $userName = isset($userName) ? $userName : "";

        // Check if $commentsResult is set before using it
        if (isset($commentsResult) && $commentsResult->num_rows > 0) {
            while ($commentRow = mysqli_fetch_assoc($commentsResult)) {
                $commentId = $commentRow['id'];
                $commentDate = $commentRow['timestamp'];

                echo "<div class='comment-rating' id='ratingnew'>Rating: ";
                $ratingStars = isset($commentRow["rating"]) ? $commentRow["rating"] : 0;
                for ($i = 1; $i <= 5; $i++) {
                    echo "<span class='star " . ($i <= $ratingStars ? 'filled' : 'empty') . "'>&#9733;</span>";
                }
                // Add delete button for comments created by the currently logged-in user
                if (isset($_SESSION['id']) && isset($commentRow['User_id']) && $commentRow['User_id'] == $_SESSION['id']) {
                    echo "<form action='show.php?id=$productId' method='post' id='form-comment'>";
                    echo "<input type='hidden' name='delete_comment_id' value='$commentId'>";
                    echo "<button type='submit' class='delete-comment-btn'><i class='fas fa-times'></i></button>";
                    echo "</form>";
                }
                echo "</div>";

                // Display the comments 
                echo "<div class='comment'>";
                echo "<p>" . $commentRow["comment_text"] . "</p>";
                echo "<div class='comment-meta'>";
                echo "<div class='comment-by'>$commentDate by $userName</div>";
                echo "</div>";
                echo "</div>";


                // Add the show/hide button for replies
                echo "<button class='show-replies-btn' onclick='toggleReplies(\"replies-{$commentRow['id']}\")' id='reply-btn'>Replies</button>";
                // Display the reply form for each comment
                echo "<form action='show.php?id=$productId' method='post' class='reply-form'>";
                echo "<input type='hidden' name='comment_id' value='{$commentRow['id']}'>";
                echo "<label for='reply_text'>Add Reply:</label>";
                echo "<textarea id='reply_text' name='reply_text' required></textarea>";
                echo "<button type='submit'> >> </button>";
                echo "</form>";

                // Display existing replies with a container div that can be toggled
                echo "<div id='replies-{$commentRow['id']}' class='replies-container'>";
                

                $repliesSql = "SELECT * FROM replies WHERE comment_id = $commentId";
                $repliesResult = mysqli_query($conn,$repliesSql);

                if ($repliesResult->num_rows > 0) {
                    // Display existing replies
                    while ($replyRow = mysqli_fetch_assoc($repliesResult)) {
                        // Display the reply content and details
                        echo "<div class='reply'>";
                        echo "<p>{$replyRow['reply_text']}</p>";
                        echo "<div class='reply-meta'>";
                        echo "<div class='reply-by'>{$replyRow['user_name']} on {$replyRow['timestamp']}</div>";
                        echo "</div>";
                        echo "</div>";
                        
                    }
                }

                echo "</div>"; // Close the replies container div
            }
        } else {
            echo "No comments yet.<br>";
        }
        ?>
    </div>

</body>

</html>
<script>
    function toggleReplies(containerId) {
        var container = document.getElementById(containerId);
        container.style.display = container.style.display === 'none' ? 'block' : 'none';
    }
</script>
