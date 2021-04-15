<?php

class WebSocket {
    private $ip;                        // server id
    private $port;                      // server post
    private $connection;                // server connection

    public $handler;                    // server handler

    public $users = [];                 // array of online users
    public $finder = null;              // user that finding room
    public $lobbies = [];               // array of lobbies

    private $connects;                  // array of all connections

    private $startTime;                 // time of server start
    private $timeLimit = 0;             // time limit of server working
   
    private $verbose = false;           // output to console ?
    private $logging = false;           // output to log file ?
    private $logFile = 'ws-log.txt';    // log file
    private $resource;                  // resource of log file

    public function __construct($ip = '127.0.0.1', $port = 8080) {
        $this->ip = $ip;
        $this->port = $port;

        $this->handler = function($connection, $data, $self) {
            $message = '[' . date('r') . '] Получено сообщение от клиента: ' . $data . PHP_EOL;

            if ($this->verbose) {
                echo $message;
            }
            if ($this->logging) {
                fwrite($this->resource, $message);
            }
        };
    }
    public function __destruct() {
        if (is_resource($this->connection)) {
            $this->stopServer();
        }
        if ($this->logging) {
            fclose($this->resource);
        }
    }
    public function settings($verbose = false, $logging = false, $logFile = 'ws-log.txt') {
        $this->verbose = $verbose;
        $this->logging = $logging;
        $this->logFile = $logFile;

        if ($this->logging) {
            $this->resource = fopen($this->logFile, 'a');
        }
    }
    private function debug($message) {
        $message = '[' . date('r') . '] ' . $message . PHP_EOL;

        if ($this->verbose) {
            echo $message;
        }
        if ($this->logging) {
            fwrite($this->resource, $message);
        }
    }
    public static function response($connect, $data) {
        if (!$connect) {
            return;
        }

        socket_set_nonblock($connect);
        socket_write($connect, self::encode($data));
    }
    public function startServer() {
        $this->debug('Try start server...');

        $this->connection = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->connection === false) {
            return $this->debug('Error socket_create(): ' . socket_strerror(socket_last_error()));
        }

        $bind = socket_bind($this->connection, $this->ip, $this->port);

        if ($bind === false) {
            return $this->debug('Error socket_bind(): ' . socket_strerror(socket_last_error()));
        }

        $option = socket_set_option($this->connection, SOL_SOCKET, SO_REUSEADDR, 1);

        if ($option === false) {
            return $this->debug('Error socket_set_option(): ' . socket_strerror(socket_last_error()));
        }

        $listen = socket_listen($this->connection);

        if ($listen === false) {
            return $this->debug('Error socket_listen(): ' . socket_strerror(socket_last_error()));
        }

        $this->debug('Server is running...');

        $this->connects = array($this->connection);
        $this->startTime = time();

        while (true) {
            $this->debug('Waiting for connections...');

            $read = $this->connects;
            $write = $except = null;

            if (!socket_select($read, $write, $except, null)) {
                break;
            }

            if (in_array($this->connection, $read)) {
                if (($connect = socket_accept($this->connection)) && $this->handshake($connect)) {
                    $this->debug('New connection accepted');
                    $this->connects[] = $connect;
                }

                unset($read[array_search($this->connection, $read)]);
            }

            foreach ($read as $connect) {
                $data = socket_read($connect, 100000);
                $decoded = self::decode($data);

                if (false === $decoded || 'close' === $decoded['type']) {
                    $this->debug('Connection closing');

                    // Clear lobby (need to add game status)
                    $user = array_search($connect, $this->users);

                    if ($this->finder === $user) {
                        $this->finder = null;
                    }

                    foreach ($this->lobbies as $lobby_key => &$lobby) {
                        if (isset($lobby)){
                            if (is_array($lobby[$lobby_key])) {
                                foreach ($lobby as $user_key => &$users) {
                                    if (in_array($user, $users)) {
                                        $responce_key = 0;
            
                                        if ($user_key === 0) {
                                            $responce_key = 1;
                                        }
        
                                        $responce_user = $lobby[$responce_key]["login"];
            
                                        $responce = [
                                            "type" => "end",
                                            "status" => "win"
                                        ];
            
                                        WebSocket::response($this->users[$responce_user], json_encode($responce));
                                    
                                        $this->debug('lobby destroed successfully');
            
                                        unset($this->lobbies[$lobby_key]);
            
                                        break 2;
                                    }
                                }
                            }
                        }
                    }

                    socket_write($connect, self::encode('  Closed on client demand', 'close'));
                    socket_shutdown($connect);
                    socket_close($connect);

                    unset($this->users[$user]);

                    unset($this->connects[array_search($connect, $this->connects)]);

                    $this->debug('Closed successfully');

                    continue;
                }

                if (is_callable($this->handler)) {
                    call_user_func($this->handler, $connect, $decoded['payload'], $this);
                }
            }

            // stop server after time limit 
            // if ($this->timeLimit && time() - $this->startTime > $this->timeLimit) {
            //     $this->debug('Time limit. Stopping server.');
            //     return $this->StopServer();
            // }
        }
    }
    public function stopServer() {
        socket_close($this->connection);

        if (!empty($this->connects)) {
            foreach ($this->connects as $connect) {
                if (is_resource($connect)) {
                    socket_write($connect, self::encode('  Closed on server demand', 'close'));
                    socket_shutdown($connect);
                    socket_close($connect);
                }
            }
        }
    }
    private static function encode($payload, $type = 'text', $masked = false) {
        $frameHead = array();
        $payloadLength = strlen($payload);

        switch ($type) {
            case 'text':
                // first byte indicates FIN, Text-Frame (10000001):
                $frameHead[0] = 129;
                break;
            case 'close':
                // first byte indicates FIN, Close Frame(10001000):
                $frameHead[0] = 136;
                break;
            case 'ping':
                // first byte indicates FIN, Ping frame (10001001):
                $frameHead[0] = 137;
                break;
            case 'pong':
                // first byte indicates FIN, Pong frame (10001010):
                $frameHead[0] = 138;
                break;
        }

        if ($payloadLength > 65535) {
            $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 255 : 127;

            for ($i = 0; $i < 8; $i++) {
                $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
            }

            if ($frameHead[2] > 127) {
                return array('type' => '', 'payload' => '', 'error' => 'frame too large (1004)');
            }
        } else if ($payloadLength > 125) {
            $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 254 : 126;
            $frameHead[2] = bindec($payloadLengthBin[0]);
            $frameHead[3] = bindec($payloadLengthBin[1]);
        } else {
            $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
        }

        foreach (array_keys($frameHead) as $i) {
            $frameHead[$i] = chr($frameHead[$i]);
        }

        if ($masked === true) {
            $mask = array();

            for ($i = 0; $i < 4; $i++) {
                $mask[$i] = chr(rand(0, 255));
            }

            $frameHead = array_merge($frameHead, $mask);
        }

        $frame = implode('', $frameHead);

        for ($i = 0; $i < $payloadLength; $i++) {
            $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
        }

        return $frame;
    }
    private static function decode($data) {
        if (!strlen($data)) {
            return false;
        }

        $unmaskedPayload = '';
        $decodedData = array();

        $firstByteBinary = sprintf('%08b', ord($data[0]));
        $secondByteBinary = sprintf('%08b', ord($data[1]));
        $opcode = bindec(substr($firstByteBinary, 4, 4));
        $isMasked = ($secondByteBinary[0] == '1') ? true : false;
        $payloadLength = ord($data[1]) & 127;

        if (!$isMasked) {
            return array(
                'type' => '',
                'payload' => '',
                'error' => 'protocol error (1002)'
            );
        }

        switch ($opcode) {
            // text frame:
            case 1:
                $decodedData['type'] = 'text';
                break;
            case 2:
                $decodedData['type'] = 'binary';
                break;
            // connection close frame:
            case 8:
                $decodedData['type'] = 'close';
                break;
            // ping frame:
            case 9:
                $decodedData['type'] = 'ping';
                break;
            // pong frame:
            case 10:
                $decodedData['type'] = 'pong';
                break;
            default:
                return array('type' => '', 'payload' => '', 'error' => 'unknown opcode (1003)');
        }

        if ($payloadLength === 126) {
            $mask = substr($data, 4, 4);
            $payloadOffset = 8;
            $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
        } else if ($payloadLength === 127) {
            $mask = substr($data, 10, 4);
            $payloadOffset = 14;
            $tmp = '';
            for ($i = 0; $i < 8; $i++) {
                $tmp .= sprintf('%08b', ord($data[$i + 2]));
            }
            $dataLength = bindec($tmp) + $payloadOffset;
            unset($tmp);
        } else {
            $mask = substr($data, 2, 4);
            $payloadOffset = 6;
            $dataLength = $payloadLength + $payloadOffset;
        }

        if (strlen($data) < $dataLength) {
            return false;
        }

        if ($isMasked) {
            for ($i = $payloadOffset; $i < $dataLength; $i++) {
                $j = $i - $payloadOffset;
                if (isset($data[$i])) {
                    $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
                }
            }
            $decodedData['payload'] = $unmaskedPayload;
        } else {
            $payloadOffset = $payloadOffset - 4;
            $decodedData['payload'] = substr($data, $payloadOffset);
        }

        return $decodedData;
    }
    private function handshake($connect) {
        $info = array();

        $data = socket_read($connect, 1000);
        $lines = explode("\r\n", $data);

        foreach ($lines as $i => $line) {
            if ($i) {
                if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                    $info[$matches[1]] = $matches[2];
                }
            } else {
                $header = explode(' ', $line);
                $info['method'] = $header[0];
                $info['uri'] = $header[1];
            }

            if (empty(trim($line))) {
                break;
            }
        }

        $ip = $port = null;

        if (!socket_getpeername($connect, $ip, $port)) {
            return false;
        }

        $info['ip'] = $ip;
        $info['port'] = $port;

        if (empty($info['Sec-WebSocket-Key'])) {
            return false;
        }

        $SecWebSocketAccept = base64_encode(
            pack(
                'H*', sha1(
                    $info['Sec-WebSocket-Key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11'
                )
            )
        );

        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
                   "Upgrade: websocket\r\n" .
                   "Connection: Upgrade\r\n" .
                   "Sec-WebSocket-Accept:".$SecWebSocketAccept."\r\n\r\n";

        socket_write($connect, $upgrade);

        return true;
    }
}
