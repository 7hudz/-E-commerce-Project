<?php
session_start();

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

// Check if the user is logged in
if (isset($_SESSION['id'])) {
    // Check if the product ID is set and not empty
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $productId = $_GET['id'];

        // SQL to delete the product from the database
        $deleteSql = "DELETE FROM articaldatabase_db WHERE id = $productId";

        if ($conn->query($deleteSql) === TRUE) {
            
            header("Location: index.php");
            
        } else {
            echo "Error deleting product: " . $conn->error;
        }
    } else {
        echo "Product ID is missing or invalid";
    }
} else {
    echo "User not logged in";
}

// Close connection
$conn->close();
?>