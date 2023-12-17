<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dynamic Widget Example</title>
  <link rel="stylesheet" href="style1.css"> 
</head>

<body>
<div class="widget-form-container"> 
  <!-- Form for Adding Data -->
  <form method="post" enctype="multipart/form-data" class="widget-form" id="add-form">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" rows="4" required></textarea><br>

    <label for="image">Choose Image:</label>
    <input type="file" name="images" id="image" accept="image/*" required><br>

    <input type="hidden" name="action" value="submit">
    <input type="submit" value="Add New Product">
  </form>
  <!-- Form for Updating Data (Initially Hidden) -->
<form method="post" enctype="multipart/form-data" class="widget-form" id="update-form" style="display:none;">
  <label for="edit-title">Edit Title:</label>
  <input type="text" name="edit-title" id="edit-title" required><br>

  <label for="edit-description">Edit Description:</label>
  <textarea name="edit-description" id="edit-description" rows="4" required></textarea><br>

  <label for="edit-image">Choose New Image (Leave blank to keep existing):</label>
  <input type="file" name="edit-image" id="edit-image" accept="image/*"><br>

  <input type="hidden" name="id" id="edit-id">
  <input type="hidden" name="action" value="update">

  <input type="submit" value="Update">
</form>

</div>

<?php
// Retrieve data from the database and display it dynamically
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "articaldatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Failed to connect: " . $conn->connect_error);
}
// Function to handle form submission
function handleFormSubmission($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'submit') {
        if (isset($_POST['title']) && isset($_POST['description'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];

            // Handling image upload
            $targetDir = "images/";

            if (isset($_FILES["images"])) {
                $targetFile = $targetDir . basename($_FILES["images"]["name"]);

                if (move_uploaded_file($_FILES["images"]["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile;

                    // Insert the new data into the database
                    $sqlInsert = "INSERT INTO articaldatabase_db (title, description, image_path) VALUES ('$title', '$description', '$imagePath')";
                    if ($conn->query($sqlInsert) === TRUE) {
                        // Redirect to the newstyle.php page after successful form submission
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "Error: " . $sqlInsert . "<br>" . $conn->error;
                    }
                } else {
                    echo "Error uploading the image.";
                }
            }
        } else {
            echo "Title and description are required fields.";
        }
    }
}
// Call the form submission and update handling functions
handleFormSubmission($conn);
// Query to retrieve data
$sql = "SELECT id, title, description, image_path FROM articaldatabase_db";
$result = $conn->query($sql);
// Close connection
$conn->close();
?>

<!-- JavaScript Function to Toggle Forms -->
<script>
function toggleForms(id, title, description) {
    document.getElementById('edit-title').value = title;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-id').value = id;

    document.getElementById('add-form').style.display = 'none';
    document.getElementById('update-form').style.display = 'block';

    return false; // Add this line to prevent the default form submission
}

function cancelUpdate() {
    document.getElementById('add-form').style.display = 'block';
    document.getElementById('update-form').style.display = 'none';
}
</script>
   

</body>
</html>
