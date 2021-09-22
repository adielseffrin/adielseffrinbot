<?php

namespace AdielSeffrinBot;

use Phergie\Irc\Bot\React\PluginInterface;
use React\EventLoop\LoopInterface;
use Phergie\Irc\Client\React\LoopAwareInterface;


use AdielSeffrinBot\Models\Twitter;
use AdielSeffrinBot\Models\Twitch;
use AdielSeffrinBot\Models\Usuario;
use AdielSeffrinBot\Models\ConexaoBD;
use AdielSeffrinBot\Models\Pizza;

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
  private $ausenciaArray;
  private $pessoasNoChat;

  public $BD;

  public function __construct()
  {
    $this->connection = new \Phergie\Irc\Connection();
    $this->connection
      ->setServerHostname('irc.chat.twitch.tv')
      ->setServerPort(6667)
      ->setPassword($_SERVER['TWITCH_PASSWORD'])
      ->setNickname($_SERVER['TWITCH_NICKNAME'])
      ->setUsername($_SERVER['TWITCH_USERNAME']);

    $this->client = new \Phergie\Irc\Client\React\Client();
    //$this->socketConnector = new React\Socket\Connector($this->client->getLoop());
    $this->ausenciaArray = array();
    $this->pessoasNoChat = array();
    
  }

  public function run()
  {
    $this->client->on('connect.after.each', function ($c, $write) {
      $this->onJoin($c, $write);
      $this->client->addPeriodicTimer($_SERVER['RETWEETTIME'], function () use ($write) {
        retweet($this->twitter, $write, $_SERVER['TWITCH_CHANNEL']);
      });

      $this->client->addTimer(180, function () use ($write){
        $this->client->addPeriodicTimer($_SERVER['PRIMETIME'], function () use ($write) {
          prime($write, $_SERVER['TWITCH_CHANNEL']);
        });
      });

      /*
      Front
      Lista de ingredientes
      mudar nick
      */
      $tempoPizza = 302;
      Pizza::$write = $write;
      $this->client->addPeriodicTimer($tempoPizza, function () use ($write) {
        Pizza::sorteia();
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

    $write->ircJoin($_SERVER['TWITCH_CHANNEL']);
    $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], 'Sou um bot ou um bug?');
    
    //$this->debugando = new Debugando();
    $this->twitter = new Twitter();
    $this->twitch = new Twitch();
    
  }

  function onMessage($message, $write, $connection, $logger)
  {
    if ($message['command'] == 'PRIVMSG') {
      $comando = null;
      $stack = null;
      $username = str_replace("@", "", $message['user']);
     
      $this->verificaUserNoChat($username);

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
            ban($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!pergunta":
            perguntas($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!social":
          case "!twitter":
          case "!github":
          case "!instagram":
          case "!discord":
            social($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!comandos":
            comandos($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!rt":
            retweet($this->twitter, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          // case "!debugando":
          //   $this->debugando->tratarComando($message, $write, $_SERVER['TWITCH_CHANNEL']);
          //   break;
          case "!pizza":
          case "!🍕":
          case "!fome":
          case "!ranking":
          case "!rank":
          case "!ingredientes":
          case "!inv":
          case "!inventario":
          case "!inventário":
          case "!🛍":
          case "!bag":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
            comandosBD($message, $write, $_SERVER['TWITCH_CHANNEL'], $this->pessoasNoChat[$index]);
            break;
          case "!reuniao":
          case "!reunião":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "Boa reunião @" . $username . "!");
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'reuniao'));
            }
            break;
          case "!lurk":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "Obrigado pelo lurk @" . $username . "!");
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'lurk'));
            }
            break;
          case "!voltei":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index !== false){
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "Aeeee 🎆🎉🎊, @" . $username . ", que bom que você voltou!");
              unset($this->ausenciaArray[$index]);
              $this->ausenciaArray = array_values($this->ausenciaArray);
            }
            break;
          case "!prime"  :
            prime($write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!liveon":
          case "!atualizart":
          case "!tweetapramim":
          case "!surpresa":
            comandosPvt($message,$this->twitter, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!apresentação":
          case "!apresentacao":
            apresentar($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!teste":
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "(Digite !pizza)");
            break;
          case "!addsub":
          case "!removesub":
            if(!empty($stack[1])){
              $username = $stack[1];
              $index = $this->verificaUserNoChat($username);
              comandosPvt($message,null, $write, $_SERVER['TWITCH_CHANNEL'], $this->pessoasNoChat[$index]);
            }
            break;
          case "!sechama":
          case "!renomear":
            if(!empty($stack[1]) && !empty($stack[2])){
              $oldNick = $stack[1];
              $newNick = $stack[2];
              $index = $this->verificaUserNoChat($oldNick);
              comandosPvt($message, null, $write, $_SERVER['TWITCH_CHANNEL'],  $this->pessoasNoChat[$index]);
            }
            break;
        };
      }
    }
  }

  public function verificaUserNoChat($username){
    $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
    if($index === false){
      $user = new Usuario($username);
      $dados_twitch = $this->twitch->getUserDetailsByLogin($username);
      if(!$user->verificarExistenciaUsuario()){
        $user->cadastrarUsuario();
      }else{
        $user->carregarUsuario();
        $user->setTwitchId($dados_twitch['data'][0]['id']);
        $user->atualizaTwitchId();
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
      $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $this->retornaMensagemAusencia($username, $tipoAusencia));
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
//TODO Verificar erro
// PHP Notice:  Undefined index: data in /home/adielseffrin/adielseffrinbot/src/AdielSeffrinBot.php on line 265
// PHP Warning:  Invalid argument supplied for foreach() in /home/adielseffrin/adielseffrinbot/src/AdielSeffrinBot.php on line 265
    if($tipoAusencia === 'lurk'){
      return $mensagensLurk[rand(0,count($mensagensLurk)-1)];
    }else{
      return $mensagensReuniao[rand(0,count($mensagensReuniao)-1)];
    }
  }

  private function atualizaListaSubs($subs){
    echo PHP_EOL."### Atualizando lista de subs... ###".PHP_EOL;
    $subsNames = array();
    if(isset($subs['data'])){
      foreach($subs['data'] as $sub){
        
        array_push($subsNames,$sub['user_login']);
      }
     
    }
    
    $parametros = implode(PHP_EOL, $subsNames);
    if($parametros != ""){
      echo PHP_EOL."### Subs encontrados:".PHP_EOL.$parametros.PHP_EOL."Fim da lista de subs ###".PHP_EOL;
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $this->hasTransactionOpened = 'atualizaListaSubs';
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE usuarios SET sub = 0');
        $stmt->execute();
        
        $stmt = ConexaoBD::getInstance()->prepare("UPDATE usuarios SET sub = 1 WHERE nick IN ({$parametros})");
        $stmt->execute($subsNames);
        
        ConexaoBD::getInstance()->commit();
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }
    echo PHP_EOL."### Rotina de atualização de subs finalizada ###".PHP_EOL;
  }

}
