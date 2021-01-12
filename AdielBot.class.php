<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
require_once './comandos.php';
require_once './Twitter.class.php';
require_once './Debugando.class.php';

use Phergie\Irc\Bot\React\PluginInterface;
use React\EventLoop\LoopInterface;
use Phergie\Irc\Client\React\LoopAwareInterface;

class AdielBot
{

  private $config;
  private $connection;
  protected $client;
  private $twitter;
  private $write;
  private $debugando;


  public function __construct()
  {
    $this->config = new Config();
    $this->connection = new \Phergie\Irc\Connection();
    $this->connection
      ->setServerHostname('irc.chat.twitch.tv')
      ->setServerPort(6667)
      ->setPassword($this->config->getPassword())
      ->setNickname($this->config->getBotName())
      ->setUsername($this->config->getBotName());

    $this->client = new \Phergie\Irc\Client\React\Client();
  }

  public function run()
  {
    $this->client->on('connect.after.each', function ($c, $write) {
      $this->onJoin($c, $write);
      //$this->sendRetweet($this->client, $this->config->getRetweetTime(), $write);
      $this->client->addPeriodicTimer($this->config->getRetweetTime(), function () use ($write) {
        retweet($this->twitter, $write, $this->config->getChannelName());
      });
    });

    $this->client->on('irc.received', function ($m, $w, $c, $l) {
      if ($this->write == null) $this->write = $w;
      $this->onMessage($m, $w, $c, $l);
    });



    $this->client->run($this->connection);
  }

  // function sendRetweet($client, $waitTime, $write)
  // {
  //   $client->addTimer($waitTime, function () use ($client, $waitTime, $write) {
  //     retweet($this->twitter, $write, $this->config->getChannelName());
  //     $this->sendRetweet($client, $waitTime + 1, $write);
  //   });
  // }


  function onJoin($connection, $write)
  {
    global $debugando;

    $write->ircJoin($this->config->getChannelName());
    $write->ircPrivmsg($this->config->getChannelName(), 'Sou um bot ou um bug?');

    $this->debugando = new Debugando();
    $this->twitter = new Twitter($this->config->getTwitterKeys());

    //lembreteRetweet($loop, $twitter, $write, $seuCanal);
  }

  function onMessage($message, $write, $connection, $logger)
  {
    //global $seuCanal;
    //global $debugando;
    //global $twitter;

    if ($message['command'] == 'PRIVMSG') {

      $comando = null;
      if (strripos(strtolower($message['params']['text']), "!") === 0) {
        $comando = explode(" ", strtolower($message['params']['text']))[0];
      }

      if (!is_null($comando)) {
        switch ($comando) {
          case "!ban":
            ban($message, $write, $this->config->getChannelName());
            break;
          case "!pergunta":
            perguntas($message, $write, $this->config->getChannelName());
            break;
          case "!social":
          case "!twitter":
          case "!github":
          case "!instagram":
          case "!discord":
            social($message, $write, $this->config->getChannelName());
            break;
          case "!comandos":
            comandos($message, $write, $this->config->getChannelName());
            break;
          case "!rt":
            retweet($this->twitter, $write, $this->config->getChannelName());
            break;
          case "!debugando":
            $this->debugando->tratarComando($message, $write, $this->config->getChannelName());
            break;
          case "!reuniao":
            $username = str_replace("@", "", $message['user']);
            $write->ircPrivmsg($this->config->getChannelName(), "Boa reuniao @" . $username . "!");
            break;
        };
      }
    }
  }
}
