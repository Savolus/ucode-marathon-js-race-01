<?php 
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

require_once 'WebSocket.php';

$server = new WebSocket('127.0.0.1');

$server->settings(true);

$server->handler = function($connect, $data) {
    if (!in_array($data, array('date', 'time', 'country', 'city'))) {
        return WebSocket::response($connect, 'Неизвестная команда');
    }

    switch ($data) {
        case 'date'   : $response = date('d.m.Y'); break;
        case 'time'   : $response = date('H:i:s'); break;
        case 'country': $response = 'Россия';      break;
        case 'city'   : $response = 'Москва';      break;
    }

    WebSocket::response($connect, $response);
};

$server->startServer();
