<?php 
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

require_once 'WebSocket.php';

$server = new WebSocket('10.11.7.10');

$server->settings(true);

$server->handler = function($connect, $data, $server) {
    $json = json_decode($data, true);

    switch ($json['type']) {
        case 'login':
            $response = [
                "type" => "logged",
                "status" => true
            ];

            if (!in_array($json['user'], $server->users)) {
                $server->users[$json['user']] = $connect;
            } else {
                $response["status"] = false;
            }

            WebSocket::response($connect, json_encode($response));

            break;
        case 'find':
            $user = $json['user'];

            if (isset($server->finder)) {
                $user_1 = $server->finder;
                $user_2 = $user;

                if ($user_1 === $user_2) {
                    return;
                }

                $server->lobbies[] = [$user_1, $user_2];

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
        case 'draw_hand':
            $db = new mysqli(
                '127.0.0.1',
                'mdorohyi',
                'securepass',
                'marvel_champions'
            );

            $request = $db->query("SELECT * FROM deck;");
            $deck = $request->fetch_all(); // all deck

            shuffle($deck);

            $user = array_search($connect, $server->users);

            foreach ($server->lobbies as &$lobby) {
                if (in_array($user, $lobby)) {
                    $user = array_shift($lobby);

                    $card_1 = array_shift($deck);
                    $card_2 = array_shift($deck);
                    $card_3 = array_shift($deck);
                    $card_4 = array_shift($deck);

                    $turn_first = is_array($lobby[0]);

                    array_push($lobby, [
                        "login" => $user,
                        "board" => [],
                        "hand" => [
                            $card_1,
                            $card_2,
                            $card_3,
                            $card_4
                        ],
                        "deck" => $deck,
                        "coin" => $turn_first
                    ]);

                    $response = [
                        "type" => "draw_hand",
                        "hand" => $lobby[1]["hand"],
                        "coin" => $turn_first
                    ];

                    WebSocket::response($server->users[$user], json_encode($response));

                    break;
                }
            }

            break;
        case 'draw':
            $user = array_search($connect, $server->users);

            foreach ($server->lobbies as &$lobby) {
                foreach ($lobby as $key => &$users) {
                    $find_lobby = array_search($user, $users);

                    if ($find_lobby !== false) {
                        $deck = $users["deck"];

                        $card = array_shift($deck);

                        array_push($users["hand"], $card);

                        $users["deck"] = $deck;

                        echo "Draw card";

                        $response = [
                            "type" => "draw",
                            "card" => $card
                        ];

                        WebSocket::response($server->users[$user], json_encode($response));
                    }
                }
            }
            break;
    }
};

$server->startServer();
