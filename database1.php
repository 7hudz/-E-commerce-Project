<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "articaldatabase";

    // Create connection
    $conn =  mysqli_connect($servername, $username, $password, $dbname);

    // Query to retrieve data
    $sqlQuery = "SELECT id, title, description, image_path FROM articaldatabase_db";

    if ($result = mysqli_query($conn,$sqlQuery)) {
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='product-item'>";
        echo "<a href='show.php?id=" . $row["id"] . "' class='product-link'>";
        echo "<img src='" . $row["image_path"] . "' alt='" . $row["title"] . "'>";
        echo "<h2>" . $row["title"] . "</h2>";
        echo "</a>"; // Close the link
        echo "<p>" . $row["description"] . "</p>";
        echo "<p>Price: $19.99</p>";

        // Check if the user is logged in
        if (isset($_SESSION['id'])) {
          // Show the "Edit" button and delete button when logged in
          echo "<button onclick='deleteProduct(" . $row["id"] . ")'>Delete</button>     ";
          echo "<button onclick='toggleForms(" . $row["id"] . ", \"" . $row["title"] . "\", \"" . $row["description"] . "\")'>Edit</button>   ";
        }

        echo "</div>";
      }
    } else {
      echo "0 results";
    }

    // Close connection
    mysqli_close($conn);
    ?>