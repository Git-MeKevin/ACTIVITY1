<?php
    session_start();

    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);

    if (empty($_SESSION['csrf_token'])){
        $csrf_token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;
    }
    else{
        $csrf_token = $_SESSION['csrf_token'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity 1</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>SIGN IN</h1>
        <form action="dashboard/admin/authentication/admin-class.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
            <input type="email" name="email" placeholder="Enter email" required>
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit" name="btn-signin">SIGN IN</button>
        </form>

        <h1>REGISTRATION</h1>
        <form action="dashboard/admin/authentication/admin-class.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
            <input type="text" name="username" placeholder="Enter username" required>
            <input type="email" name="email" placeholder="Enter email" required>
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit" name="btn-signup">SIGN UP</button>
        </form>
    </div>
</body>


</html>