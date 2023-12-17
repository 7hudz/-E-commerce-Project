<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>signup</title>
</head>
<body>
 
    <form action="process.php" method="post" novalidate>
        <h2 class="text-center mb-4">Sign Up Form</h2>
        
        <div>
            
            <label for="name">Username</label>
            <input type="text" id="name" name="name">
    
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email"  name="email">
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </div>
        <div>
            <label for="password_confirmation">repeat Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>
        <button>submit</button>
    </form>
</body>
</html>