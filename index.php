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
    <script src="/js/home.js" defer></script>
</head>
<body>
    <div class="main-box">
        <div class="logo-box">
            <img src="/images/join-logo.png" class="logo">
            <h1>Marvel Champions</h1>
            <h2><?php echo $_SESSION["login"] ?></h2>
        </div>
        <div class="control-panel">
            <input type="button" value="Play" class="controller span-two" data-play>
            <input type="button" value="Collection" class="controller" disabled>
            <input type="button" value="Log out" class="controller" data-log-out>
        </div>
    </div>
</body>
</html>
