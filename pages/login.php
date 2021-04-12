<?php

    session_start();

    function login(string $login) : void {
        $db = new mysqli('127.0.0.1', 'mdorohyi', 'securepass', 'marvel_champions', null);

        $result = $db->query("SELECT * FROM users WHERE login = '$login';");
        $result = $result->fetch_all();

        if (!empty($result)) {
            return;
        }

        $db->query("INSERT INTO users (login) VALUE ('$login');");
        $db->close();

        $_SESSION["login"] = $login;
    }

    if (isset($_POST["login"])) {
        login($_POST["login"]);
    }

    if (isset($_SESSION["login"])) {
        echo '
            <script>
                window.location.href = "/index.php" 
            </script>
        ';
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <form action="/pages/login.php" method="post" class="join-box">
        <img src="/images/join-logo.png" class="logo">
        <h1>Marvel Champions</h1>
        <div class="credentials">
            <input type="text" name="login" id="login" placeholder="Login...">
            <input type="submit" value="Join">
        </div>
    </form>
</body>
</html>
