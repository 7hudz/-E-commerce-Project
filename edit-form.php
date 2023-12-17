<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Product Website - Add Form</title>
    <link rel="stylesheet" href="style1.css">

</head>
<body>

    
<form method="post" enctype="multipart/form-data" class="widget-form" id="add-form" onsubmit="return toggleForms(/*pass your parameters here*/);">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" rows="4" required></textarea><br>

    <label for="image">Choose Image:</label>
    <input type="file" name="images" id="image" accept="image/*" required><br>

    <input type="hidden" name="action" value="submit">
    <input type="submit" value="Submit">
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

    <input type="submit" value="Add">
</form>
    <?php
    // Your PHP code to retrieve and display product data
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Your PHP code to retrieve and display product data
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

    // Query to retrieve data
    $sql = "SELECT id, title, description, image_path FROM articaldatabase_db";
    $result = $conn->query($sql);
    handleFormSubmission($conn);
    handleUpdate($conn);

    // Close connection
    $conn->close();
    function handleFormSubmission($conn)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'submit') {
            if (isset($_POST['title'], $_POST['description'])) {
                $title = $_POST['title'];
                $description = $_POST['description'];
    
                // Handling image upload
                $targetDir = "images/";
    
                if (isset($_FILES["images"])) {
                    $targetFile = $targetDir . basename($_FILES["images"]["name"]);
    
                    if (move_uploaded_file($_FILES["images"]["tmp_name"], $targetFile)) {
                        $imagePath = $targetFile;
    
                        // Use prepared statement to prevent SQL injection
                        $stmt = $conn->prepare("INSERT INTO articaldatabase_db (title, description, image_path) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $title, $description, $imagePath);
    
                        if ($stmt->execute()) {
                            // Redirect to the current page after successful form submission
                            header("Location: your_add_product_page.php");
                            exit();
                        } else {
                            echo "Error executing SQL statement: " . $stmt->error;
                        }
    
                        $stmt->close();
                    } else {
                        echo "Error uploading the image. Check directory permissions.";
                    }
                } else {
                    echo "Image not set in the form.";
                }
            } else {
                echo "Title and description are required fields.";
            }
        } 
    }
    function handleUpdate($conn)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
            if (isset($_POST['id'], $_POST['edit-title'], $_POST['edit-description'])) {
                $id = $_POST['id'];
                $title = $_POST['edit-title'];
                $description = $_POST['edit-description'];
    
                // Check if a new image is uploaded
                $imagePath = null;
    
                if (isset($_FILES["edit-image"]) && $_FILES["edit-image"]["size"] > 0) {
                    $targetDir = "images/";
                    $targetFile = $targetDir . basename($_FILES["edit-image"]["name"]);
    
                    if (move_uploaded_file($_FILES["edit-image"]["tmp_name"], $targetFile)) {
                        $imagePath = $targetFile;
                    } else {
                        echo "Error uploading the new image.";
                        return;
                    }
                }
    
                // Update the existing data in the database
                if ($imagePath !== null) {
                    $sqlUpdate = "UPDATE articaldatabase_db SET title=?, description=?, image_path=? WHERE id=?";
                } else {
                    $sqlUpdate = "UPDATE articaldatabase_db SET title=?, description=? WHERE id=?";
                }
    
                $stmt = $conn->prepare($sqlUpdate);
    
                if ($stmt === false) {
                    echo "Error preparing SQL statement: " . $conn->error;
                    return;
                }
    
                // Bind parameters
                if ($imagePath !== null) {
                    $stmt->bind_param("sssi", $title, $description, $imagePath, $id);
                } else {
                    $stmt->bind_param("ssi", $title, $description, $id);
                }
    
                if ($stmt->execute()) {
                    // Redirect to the dashboard.php page after successful update
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Error updating record: " . $stmt->error;
                }
    
                $stmt->close();
            }
        }
    }
    ?>
    <script>
        // Function to get URL parameters
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        // Function to update the form based on parameters
        function updateFormFields() {
            var id = getParameterByName('id');
            var title = getParameterByName('title');
            var description = getParameterByName('description');

            // Update form fields
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-id').value = id;

            // Show the update form
            document.getElementById('add-form').style.display = 'none';
            document.getElementById('update-form').style.display = 'block';
        }

        // Call the function when the page loads
        window.onload = updateFormFields;
    </script>
 
</body>

</html>
