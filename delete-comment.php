<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment_id'])) {
    $commentId = $_POST['comment_id'];

    // Check if the user is logged in
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];

        // Add your database connection code here
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "articaldatabase";

        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Failed to connect: " . $conn->connect_error);
        }

        // Use a prepared statement to prevent SQL injection
        $deleteCommentSql = "DELETE FROM comments WHERE id = ? AND User_id = ?";
        $deleteCommentStmt = $conn->prepare($deleteCommentSql);
        $deleteCommentStmt->bind_param("ii", $commentId, $userId);
        $deleteCommentStmt->execute();

        if ($deleteCommentStmt->affected_rows > 0) {
            // Comment deleted successfully
            header("Location: show.php?id=$productId");
            exit();
        } else {
            echo "Error deleting comment: " . $conn->error;
        }

        // Close the prepared statement and database connection
        $deleteCommentStmt->close();
        $conn->close();
    } else {
        echo "User not logged in.";
    }
} else {
    echo "Invalid request.";
}
?>


