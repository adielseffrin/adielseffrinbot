<?php 
namespace AdielSeffrinBot\Services;

use Amp\Delayed;
use Amp\Websocket;
use Amp\Websocket\Client;

class SocketClient{

    private $host;
    private $port;
    private $endpoint;
    private $connection;
    
    function __construct() {
        $this->host = 'ws.adielseffr.in';
        $this->port = '8080';
        $this->endpoint = 'chat';
    }

    public function sendMessage($message){
       $this->connection->send($message);
    }
    public function disconnect(){
        $this->connection->close();
    }

    public function run(){
        \Amp\Loop::run(function () {
            try{
                $url = "ws://{$this->host}:{$this->port}/{$this->endpoint}";
                $connection = yield Client\connect($url);
            }catch(Exception $e){
                var_dump($e);
            }
            $this->connection = $connection;
            yield $connection->send('Hello!');
            \Amp\Loop::stop();
            
        });
    }


}