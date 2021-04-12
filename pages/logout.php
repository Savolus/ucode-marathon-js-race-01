<?php

session_start();

function logout(string $login) : void {
    $db = new mysqli('127.0.0.1', 'mdorohyi', 'securepass', 'marvel_champions', null);

    $db->query("DELETE FROM users WHERE login = '$login';");

    $db->close();

    session_destroy();
    session_start();
}

if (isset($_SESSION["login"])) {
    logout($_SESSION["login"]);
}

echo '
    <script>
        window.location.href = "/pages/login.php" 
    </script>
';
