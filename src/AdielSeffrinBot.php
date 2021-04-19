<?php

namespace AdielSeffrinBot;

use Phergie\Irc\Bot\React\PluginInterface;
use React\EventLoop\LoopInterface;
use Phergie\Irc\Client\React\LoopAwareInterface;

use AdielSeffrinBot\Config;
use AdielSeffrinBot\Models\Twitter;
use AdielSeffrinBot\Models\Twitch;
use AdielSeffrinBot\Models\Usuario;
use AdielSeffrinBot\Models\ConexaoBD;


//require_once 'config.php';
require_once 'comandos.php';

class AdielSeffrinBot
{

  private $config;
  private $connection;
  protected $client;
  protected $socketConnector;
  private $twitter;
  private $twitch;
  private $write;
  private $debugando;
  private $conn;
  private $ausenciaArray;
  private $pessoasNoChat;

  public function __construct()
  {
    $this->config = new Config();
    $BD = new ConexaoBD($this->config->getUserBD(),$this->config->getSenhaBD());
    $BD->connect();
    $this->conn = $BD->getConn();
    $this->connection = new \Phergie\Irc\Connection();
    $this->connection
      ->setServerHostname('irc.chat.twitch.tv')
      ->setServerPort(6667)
      ->setPassword($this->config->getPassword())
      ->setNickname($this->config->getBotName())
      ->setUsername($this->config->getBotName());

    $this->client = new \Phergie\Irc\Client\React\Client();
    //$this->socketConnector = new React\Socket\Connector($this->client->getLoop());
    $this->ausenciaArray = array();
    $this->pessoasNoChat = array();
    
  }

  public function run()
  {
    $this->client->on('connect.after.each', function ($c, $write) {
      $this->onJoin($c, $write);
      $this->client->addPeriodicTimer($this->config->getRetweetTime(), function () use ($write) {
        retweet($this->twitter, $write, $this->config->getChannelName());
      });

      $this->client->addTimer(180, function () use ($write){
        $this->client->addPeriodicTimer($this->config->getPrimeTime(), function () use ($write) {
          prime($write, $this->config->getChannelName());
        });
      });

      $this->atualizaListaSubs($this->twitch->getSubs());
      
    });

    $this->client->on('irc.received', function ($m, $w, $c, $l) {
      if ($this->write == null) $this->write = $w;
      $this->onMessage($m, $w, $c, $l);
    });

    $this->client->run($this->connection);
  }

  function onJoin($connection, $write)
  {

    $write->ircJoin($this->config->getChannelName());
    $write->ircPrivmsg($this->config->getChannelName(), 'Sou um bot ou um bug?');
    
    //$this->debugando = new Debugando();
    $this->twitter = new Twitter($this->config->getTwitterKeys());
    $this->twitch = new Twitch($this->config->getTwitchKeys());
    
  }

  function onMessage($message, $write, $connection, $logger)
  {
    if ($message['command'] == 'PRIVMSG') {
      $comando = null;
      $stack = null;
      
      $username = str_replace("@", "", $message['user']);
      $this->verificaUserNoChat($username, $this->conn);

      if (strripos(strtolower($message['params']['text']), "!") === 0) {
        $mesagemLower = strtolower($message['params']['text']);
        $stack = explode(" ", $mesagemLower);
        $comando = $stack[0];
      }
      if(is_null($comando) || $comando !== '!voltei')
        $this->validaAusencia($message,$write);

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
          // case "!debugando":
          //   $this->debugando->tratarComando($message, $write, $this->config->getChannelName());
          //   break;
          case "!fome":
          case "!ranking":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
            comandosBD($message, $write, $this->config->getChannelName(), $this->conn, $this->pessoasNoChat[$index]);
            //$username = str_replace("@", "", $message['user']);
            //$write->ircPrivmsg($this->config->getChannelName(), "Sabia @" . $username . ", que fome é pode ser um estado de espírito?");
            break;
          case "!reuniao":
          case "!reunião":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              $write->ircPrivmsg($this->config->getChannelName(), "Boa reunião @" . $username . "!");
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'reuniao'));
            }
            break;
          case "!lurk":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              $write->ircPrivmsg($this->config->getChannelName(), "Obrigado pelo lurk @" . $username . "!");
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'lurk'));
            }
            break;
          case "!voltei":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index !== false){
              $write->ircPrivmsg($this->config->getChannelName(), "Aeeee 🎆🎉🎊, @" . $username . ", que bom que você voltou!");
              unset($this->ausenciaArray[$index]);
              $this->ausenciaArray = array_values($this->ausenciaArray);
            }
            break;
          case "!prime"  :
            prime($write, $this->config->getChannelName());
            break;
          case "!liveon":
          case "!atualizart":
          case "!tweetapramim":
            comandosPvt($message,$this->twitter, $write, $this->config->getChannelName());
            break;
          case "!apresentação":
          case "!apresentacao":
            apresentar($message, $write, $this->config->getChannelName());
            break;
          case "!teste":
            //$this->atualizaListaSubs($this->twitch->getSubs());
            break;
          case "!addsub":
          case "!removesub":
            if(!empty($stack[1])){
              $username = $stack[1];
              $index = $this->verificaUserNoChat($username, $this->conn);
              comandosPvt($message,null, $write, $this->config->getChannelName(), $this->conn, $this->pessoasNoChat[$index]);
            }
            break;
        };
      }
    }
  }

  public function verificaUserNoChat($username, $conn){
    $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
    if($index === false){
      $user = new Usuario($username);
      if(!$user->verificarExistenciaUsuario($conn)){
        $user->cadastrarUsuario($conn);
      }else{
        $user->carregarUsuario($conn);
      }
      array_push($this->pessoasNoChat,array('user' => $username, 'object'=> $user));
      $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
    }
    return $index;
  }



  public function validaAusencia($message, $write){
    $username = str_replace("@", "", $message['user']);
    $index = array_search($username,array_column($this->ausenciaArray, 'user'));
    if($index !== false){
      $tipoAusencia = $this->ausenciaArray[$index]['event'];
      $write->ircPrivmsg($this->config->getChannelName(), $this->retornaMensagemAusencia($username, $tipoAusencia));
      unset($this->ausenciaArray[$index]);
      $this->ausenciaArray = array_values($this->ausenciaArray);
    }
  }

  private function retornaMensagemAusencia($username, $tipoAusencia){
    $mensagensLurk = [
      "Hmmmmmmmmmmm.. tu não estavas de lurk? Hein @" . $username . "?",
      "Oh @" . $username .", tu não disse que estavas de lurk? Voltou?",
      "Lurk bom esse hein @" . $username,
      "Ih alá, @" . $username .", voltou e nem avisou o chat 😋",
    ];

    $mensagensReuniao = [
      "Hmmmmmmmmmmm.. tu não estavas em reuniao? Hein @" . $username . " ?",
      "Hmmmmmmmmmmm.. e a reunião @" . $username . "?",
      "Ei @" . $username . " a chefia sabe que você está em reunião e aqui ao mesmo tempo?",
      "Ih alá, @" . $username .", voltou e nem avisou o chat 😋",
    ];

    if($tipoAusencia === 'lurk'){
      return $mensagensLurk[rand(0,count($mensagensLurk)-1)];
    }else{
      return $mensagensReuniao[rand(0,count($mensagensReuniao)-1)];
    }
  }

  private function atualizaListaSubs($subs){
    $subsNames = array();
    foreach($subs['data'] as $sub){
      array_push($subsNames,$sub['user_login']);
    }
    $parametros = implode(',', array_fill(0, count($subsNames), '?'));
    try{
      $this->conn->beginTransaction();
      
      $stmt = $this->conn->prepare('UPDATE usuarios SET sub = 0');
      $stmt->execute();
      
      $stmt = $this->conn->prepare("UPDATE usuarios SET sub = 1 WHERE nick IN ({$parametros})");
      $stmt->execute($subsNames);
      
      $this->conn->commit();
    }catch(PDOExecption $e) {
      $this->conn->rollback();
      print "Error!: " . $e->getMessage() . "</br>";
    } 
  
  }

}
