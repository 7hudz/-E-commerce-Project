<?php
require __Dir__ . "/database.php";
$errors = []; // Array to collect errors

if (empty($_POST["name"])) {
   $errors[] = "Name is required";
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required";
}

if (strlen($_POST["password"]) < 8) {
    $errors[] = "Password should be at least 8 characters";
}

if( ! preg_match("/[a-z]/i", $_POST["password"]))
{
    $errors[] = "password should contain at least one letter";

}
if( ! preg_match("/[1-9]/i", $_POST["password"]))
{
    $errors[] = "password should contain at least one number";

}
if ($_POST["password"]!==$_POST["password_confirmation"]) {
    $errors[] = "passwords must match";
}
if (empty($errors)) {
    // Assuming $conn is established in database.php and stored in $conn variable
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (name, email, password_hash) VALUES (?, ?, ?)";
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sss", $_POST["name"], $_POST["email"], $password_hash);

        if ($stmt->execute()) {
            echo "User registered successfully!";
            header("Location: login.php");
            exit(); // Ensure to exit after header redirect
        } else {
            echo "Error: Unable to execute the query. Error number: " . $conn->errno;
            // Handle the error, log it, or perform appropriate action
        }
    } else {
        echo "Error: Unable to prepare the statement.";
        // Handle the error, log it, or perform appropriate action
    }
} else {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
}
?>