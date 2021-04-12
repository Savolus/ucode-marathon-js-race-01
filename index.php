<?php

    session_start();

    if (!isset($_SESSION["login"])) {
        echo '
            <script>
                window.location.href = "/pages/login.php" 
            </script>
        ';
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home page</title>
    <link rel="stylesheet" href="/css/home.css">
</head>
<body>
    <?php echo "Hi " . $_SESSION["login"] . "!" ?>
    <a href="/pages/logout.php">Log out</a>
</body>
</html>
