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

use AdielSeffrinBot\Models\Language;
use AdielSeffrinBot\Models\Mensagens;

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
      $tempoPizza = 301;
      Pizza::$write = $write;
      $this->client->addPeriodicTimer($tempoPizza, function () use ($write) {
        Pizza::sorteia();
      });

      $this->atualizaListaSubs($this->twitch->getSubs());
      Language::startLanguage();
      
    });

    $this->client->on('irc.received', function ($m, $w, $c, $l) {
      if ($this->write == null) $this->write = $w;
      $this->onMessage($m, $w, $c, $l);
    });

    $this->client->run($this->connection);
  }

  function onJoin($connection, $write)
  {
    Language::startLanguage();
    $write->ircJoin($_SERVER['TWITCH_CHANNEL']);
    $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], Mensagens::getMensagem('onJoin',null));
    
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
      if(is_null($comando) || ( $comando !== '!voltei' && $comando !== '!back' && $comando !== '!imback') )
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
          case "!linkedin":
          case "!discord":
            social($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!comandos":
            comandos($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!rt":
            retweet($this->twitter, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
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
          case "!meeting":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              // $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "Boa reunião @" . $username . "!");
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], Mensagens::getMensagem('onMeeting',array(":nick" => $username)));
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'reuniao'));
            }
            break;
          case "!lurk":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              // $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "Obrigado pelo lurk @" . $username . "!");
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], Mensagens::getMensagem('onLurk',array(":nick" => $username)));
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'lurk'));
              var_dump($this->ausenciaArray);
            }
            break;
          case "!voltei":
          case "!back":
          case "!imback":
            $username = str_replace("@", "", $message['user']);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index !== false){
              // $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "Aeeee 🎆🎉🎊, @" . $username . ", que bom que você voltou!");
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], Mensagens::getMensagem('onReturn',array(":nick" => $username)));
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
          case "!liberapizza":
          case "!freepizza":
          case "!liberaingrediente":
          case "!freeingredient":
            comandosPvt($message,$this->twitter, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!apresentação":
          case "!apresentacao":
            apresentar($message, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!fomeextra":
          case "!fomecomprada":
            if(!empty($stack[1])){
              $username = $stack[1];
              $index = $this->verificaUserNoChat($username);
              comandosPvt($message,null, $write, $_SERVER['TWITCH_CHANNEL'], $this->pessoasNoChat[$index]);
            }
            break;
          case "!addsub":
          case "!removesub":
            if(!empty($stack[1])){
              $username = $stack[1];
              $index = $this->verificaUserNoChat($username);
              comandosPvt($message,null, $write, $_SERVER['TWITCH_CHANNEL'], $this->pessoasNoChat[$index]);
            }
            break;
          case "!mudaidioma":
            comandosPvt($message,null, $write, $_SERVER['TWITCH_CHANNEL']);
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
    $mensagensReuniao = [
      "Hmmmmmmmmmmm.. tu não estavas em reuniao? Hein @" . $username . " ?",
      "Hmmmmmmmmmmm.. e a reunião @" . $username . "?",
      "Ei @" . $username . " a chefia sabe que você está em reunião e aqui ao mesmo tempo?",
      "Ih alá, @" . $username .", voltou e nem avisou o chat 😋",
    ];

    if($tipoAusencia === 'lurk'){
      $length = count(Mensagens::getMensagem('lurkMessages',null)); 
      $pos = mt_rand (0, $length-1);
      return Mensagens::getMensagemArray('lurkMessages',$pos, array(":nick" => $username));
    }else{
      return $mensagensReuniao[rand(0,count($mensagensReuniao)-1)];
      $length = count(Mensagens::getMensagem('meetingMessages',null)); 
      $pos = mt_rand (0, $length-1);
      return Mensagens::getMensagemArray('meetingMessages',$pos, array(":nick" => $username));
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
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE usuarios SET sub = 0 where id > 0');
        $stmt->execute();
        $in  = str_repeat('?,', count($subsNames) - 1) . '?';
        $stmt = ConexaoBD::getInstance()->prepare("UPDATE usuarios SET sub = 1 WHERE nick IN ($in)");
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
