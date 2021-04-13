<?php 
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

require_once 'WebSocket.php';

$server = new WebSocket('127.0.0.1');

$server->settings(true);

$finders = [];
$lobbies = [];

/**
 * finders
 * [
 *      [
 *          "sock1",
 *          "user1"
 *      ],
 *      [
 *          "sock2",
 *          "user2"
 *      ]
 * ]
 */


/**
 * lobies
 * [
 *      [
 *          [
 *              "sock1",
 *              "user1"
 *          ],
 *          [
 *              "sock2",
 *              "user2"
 *          ]
 *      ],
 *      [
 *          [
 *              "sock3",
 *              "user3"
 *          ],
 *          [
 *              "sock4",
 *              "user4"
 *          ]
 *      ]
 * ]
 */

function start($server) {
    $user_1 = array_shift($server->finders);
    $user_2 = array_shift($server->finders);

    $server->lobbies[] = [$user_1, $user_2];

    $response_1 = [
        "type" => "start",
        "enemy" => $user_2[1]
    ];

    $response_2 = [
        "type" => "start",
        "enemy" => $user_1[1]
    ];

    WebSocket::response($user_1[0], json_encode($response_1));
    WebSocket::response($user_2[0], json_encode($response_2));
}


 $server->handler = function($connect, $data, $server) {
    $json = json_decode($data, true);

    switch ($json['type']) {
        case 'find':
            $server->finders[] = [$connect, $json['user']];

            if (sizeof($server->finders) > 1) {
                start();
            }

            break;
    }
};

$server->startServer();
