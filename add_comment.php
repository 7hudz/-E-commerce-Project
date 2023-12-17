<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "articaldatabase";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Failed to connect: " . $conn->connect_error);
    }

    $productId = $_POST['Product_id'];
    $userId = $_SESSION['id'];
    $userName = "User"; // Change this to the actual user name
    $commentText = $_POST['comment'];
    $commentsSql = "SELECT user_name, comment_text, timestamp, rating FROM comments WHERE Product_id = $productId";

    // Insert the comment into the database
    $sql = "INSERT INTO comments (User_id, Product_id, user_name, comment_text, rating) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $userId, $productId, $userName, $commentText, $commentRating);
    $stmt->execute();

    // Close connection
    $conn->close();
}

// Redirect back to show.php
header("Location: show.php?id=$productId");
exit();
?>
