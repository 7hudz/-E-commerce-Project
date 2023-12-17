<?php
/*$is_valid = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/database.php";
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $email = $mysqli->real_escape_string($_POST["email"]); // Escape the email input to prevent SQL injection

    $sql = sprintf("SELECT * FROM user WHERE email = '%s'", $email);

    $result = $mysqli->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user && password_verify($_POST["password"], $user["password_hash"])) {
            // Authentication successful
            header("Location: dash.html"); // Redirect to the dashboard
            exit(); // Stop further execution
        } else {
            // Invalid credentials
            // Redirect to an error page or display an error message
            header("Location: login_error.html");
            exit();
        }
    }
}
*/
$is_valid = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Establish the database connection
    require __DIR__ . "/database.php";
    $_SESSION['id'] = $dynamicUserId;
    session_start(); 


    $id = $_SESSION['id'];  

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id=$_GET['id'];

    // Use prepared statements to prevent SQL injection
    $email = $_POST["email"];
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user && password_verify($_POST["password"], $user["password_hash"])) {
            // Authentication successful
            $_SESSION['id'] = $user['id']; 
            $_SESSION['user_name'] = $user['name']; 
            header("Location: index.php?id=" . $user['id']); // Redirect to the dashboard
            exit();
        } else {
            // Invalid credentials
            header("Location: login_error.html");
            exit();
        }
    } else {
        // No user found with the provided email
        header("Location: login_error.html");
        exit();
    }
}

// Handling the login result and displaying errors

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css">
    <title>Login</title>
</head>
<body>
    <form method="post" action="login.php">
        <h2 class="text-center mb-4">Login Form</h2>
        <?php 
        if ($is_valid === false && $_SERVER["REQUEST_METHOD"] === "POST") {
            echo "<em>Invalid login</em>";
        }
        ?>
        
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </div>
        <button type="submit">Sign in</button>
    </form>
</body>
</html>