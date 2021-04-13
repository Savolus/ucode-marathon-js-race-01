<?php 
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

require_once 'WebSocket.php';

$server = new WebSocket();

$server->settings(true);

$server->handler = function($connect, $data, $server) {
    $json = json_decode($data, true);

    switch ($json['type']) {
        case 'login':
            if (!in_array($json['user'], $server->users)) {
                $server->users[$json['user']] = $connect;
            }
            break;
        case 'find':
            $user = $json['user'];

            if (isset($server->finder)) {
                $user_1 = $server->finder;
                $user_2 = $user;

                if ($user_1 === $user_2) {
                    return;
                }

                echo "NOW: $user_1 AND $user_2" . PHP_EOL;

                $server->lobbies[] = [$user_1, $user_2];

                echo "LOBBIES(I):" . PHP_EOL;
                var_dump($server->lobbies);

                $response_1 = [
                    "type" => "start",
                    "enemy" => $user_2
                ];

                $response_2 = [
                    "type" => "start",
                    "enemy" => $user_1
                ];

                WebSocket::response($server->users[$user_1], json_encode($response_1));
                WebSocket::response($server->users[$user_2], json_encode($response_2));

                $server->finder = null;
            } else {
                $server->finder = $user;
            }

            break;
    }
};

$server->startServer();
