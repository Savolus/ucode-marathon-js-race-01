<?php

    session_start();

    function wait(string $login) {
        $db = new mysqli('127.0.0.1', 'mdorohyi', 'securepass', 'marvel_champions', null);

        $db->query("INSERT INTO waiters (login) VALUE ('$login');");

        // $result = $db->query("SELECT COUNT(*) AS amount FROM waiters;");
        // $result = $result->fetch_all()[0];
        // echo $result["amount"];

        $db->close();
    }

    if (!isset($_SESSION["login"])) {
        echo '
            <script>
                window.location.href = "/pages/login.php" 
            </script>
        ';
    }

    wait($_SESSION["login"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Waiting room</title>
    <link rel="stylesheet" href="/css/waiting-room.css">
    <script src="/js/waiting-room.js" defer></script>
</head>
<body>
    <div class="waiting-room">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
        <g transform="translate(80,50)">
        <g transform="rotate(0)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="1">
        <animateTransform attributeName="transform" type="scale" begin="-0.875s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.875s"></animate>
        </circle>
        </g>
        </g><g transform="translate(71.21320343559643,71.21320343559643)">
        <g transform="rotate(45)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="0.875">
        <animateTransform attributeName="transform" type="scale" begin="-0.75s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.75s"></animate>
        </circle>
        </g>
        </g><g transform="translate(50,80)">
        <g transform="rotate(90)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="0.75">
        <animateTransform attributeName="transform" type="scale" begin="-0.625s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.625s"></animate>
        </circle>
        </g>
        </g><g transform="translate(28.786796564403577,71.21320343559643)">
        <g transform="rotate(135)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="0.625">
        <animateTransform attributeName="transform" type="scale" begin="-0.5s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.5s"></animate>
        </circle>
        </g>
        </g><g transform="translate(20,50.00000000000001)">
        <g transform="rotate(180)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="0.5">
        <animateTransform attributeName="transform" type="scale" begin="-0.375s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.375s"></animate>
        </circle>
        </g>
        </g><g transform="translate(28.78679656440357,28.786796564403577)">
        <g transform="rotate(225)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="0.375">
        <animateTransform attributeName="transform" type="scale" begin="-0.25s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.25s"></animate>
        </circle>
        </g>
        </g><g transform="translate(49.99999999999999,20)">
        <g transform="rotate(270)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="0.25">
        <animateTransform attributeName="transform" type="scale" begin="-0.125s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="-0.125s"></animate>
        </circle>
        </g>
        </g><g transform="translate(71.21320343559643,28.78679656440357)">
        <g transform="rotate(315)">
        <circle cx="0" cy="0" r="6" fill="#0c1e39" fill-opacity="0.125">
        <animateTransform attributeName="transform" type="scale" begin="0s" values="1.5 1.5;1 1" keyTimes="0;1" dur="1s" repeatCount="indefinite"></animateTransform>
        <animate attributeName="fill-opacity" keyTimes="0;1" dur="1s" repeatCount="indefinite" values="1;0" begin="0s"></animate>
        </circle>
        </g>
        </g>
    </svg>
    </div>
</body>
</html>