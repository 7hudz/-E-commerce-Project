<!-- index.php -->

</html>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Your Product Website</title>

</head>

<body>

  <header>
    <?php
    // Check if the user is logged in
    if (isset($_SESSION['id'])) {
      // Show "Logout" button if logged in
      echo '<button onclick="logout()">Logout</button>';
    } else {
      // Show "Login" button if not logged in
      echo '<button onclick="login()">Login</button>';
    }
    ?>
    <div id="logoContainer">
      <img src="logo/Logo1.png" alt="Your Product Logo">
      <br>
      <button onclick="redirectToAddForm()">Add Product</button>

    </div>

  </header>
  <section class="product">
    <?php
    include 'database1.php';
    ?>

  </section>

  <script>
    function login() {
      window.location.href = "login.php";
    }


    function logout() {
      window.location.href = "logout.php";
    }

    function redirectToAddForm() {
      window.location.href = 'add-product.php';
    }

    function toggleForms(id, title, description) {
      window.location.href = 'edit-form.php?id=' + id + '&title=' + encodeURIComponent(title) + '&description=' + encodeURIComponent(description);
    }

    function deleteProduct(productId) {
      var confirmation = confirm('Are you sure you want to delete this product?');

      if (confirmation) {
        window.location.href = 'delete.php?id=' + productId;
      }
    }
  </script>

  <footer>
    &copy; 2023 Your Product Website. All rights reserved.
  </footer>

</body>

</html>