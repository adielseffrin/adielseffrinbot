<?php
require_once '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:7181', $loop);

$socket->on('connection', function (React\Socket\ConnectionInterface $connection) {
    $connection->write("Hello " . $connection->getRemoteAddress() . "!\n");
    $connection->write("Welcome to this amazing server!\n");
    $connection->write("Here's a tip: don't say anything.\n");
echo "-------------------- AQUI TBM --------------";
    $connection->on('data', function ($data) use ($connection) {
        $connection->close();
    });
});

$loop->run();