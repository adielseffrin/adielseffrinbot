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
use AdielSeffrinBot\Models\Request;
use AdielSeffrinBot\Models\Configs;

require_once 'comandos.php';

class AdielSeffrinBot
{

  private $config;
  private $connection;
  protected $client;
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
    Configs::loadConfigs();
    $write->ircJoin($_SERVER['TWITCH_CHANNEL']);
    $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], Mensagens::getMensagem('onJoin',null));
    
    $this->twitter = new Twitter();
    $this->twitch = new Twitch();
  
  }

  function onMessage($message, $write, $connection, $logger)
  {
    if ($message['command'] == 'PRIVMSG') {
      $comando = null;
      $stack = null;
      if(isset($message['user']))
        $username = str_replace("@", "", $message['user']);
      else
        $username = str_replace(":","",explode("!",$message['prefix'])[0]);
        
      $this->verificaUserNoChat($username);
      if (stripos($message['params']['text'], "!") === 0) {
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
          case "!question":
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
            if(isset($message['user']))
              $username = str_replace("@", "", $message['user']);
            else
              $username = str_replace(":","",explode("!",$message['prefix'])[0]);
            $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
            comandosBD($message, $write, $_SERVER['TWITCH_CHANNEL'], $this->pessoasNoChat[$index]);
            break;
          case "!reuniao":
          case "!reunião":
          case "!meeting":
            if(isset($message['user']))
              $username = str_replace("@", "", $message['user']);
            else
              $username = str_replace(":","",explode("!",$message['prefix'])[0]);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], Mensagens::getMensagem('onMeeting',array(":nick" => $username)));
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'reuniao'));
            }
            break;
          case "!lurk":
            if(isset($message['user']))
              $username = str_replace("@", "", $message['user']);
            else
              $username = str_replace(":","",explode("!",$message['prefix'])[0]);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index === false){
              $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], Mensagens::getMensagem('onLurk',array(":nick" => $username)));
              array_push($this->ausenciaArray,array('user' => $username, 'event' => 'lurk'));
              //var_dump($this->ausenciaArray);
            }
            break;
          case "!voltei":
          case "!back":
          case "!imback":
            if(isset($message['user']))
              $username = str_replace("@", "", $message['user']);
            else
              $username = str_replace(":","",explode("!",$message['prefix'])[0]);
            $index = array_search($username,array_column($this->ausenciaArray, 'user'));
            if($index !== false){
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
            comandosPvt($message,$this->twitter, $this->twitch, $write, $_SERVER['TWITCH_CHANNEL']);
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
              comandosPvt($message,null,null, $write, $_SERVER['TWITCH_CHANNEL'], $this->pessoasNoChat[$index]);
            }
            break;
          case "!addsub":
          case "!testasub":
          case "!removesub":
          case "!addstreamer":
          case "!removestreamer":
              if(!empty($stack[1])){
                $username = $stack[1];
                $index = $this->verificaUserNoChat($username);
                comandosPvt($message,null,null, $write, $_SERVER['TWITCH_CHANNEL'], $this->pessoasNoChat[$index]);
              }
              break;
          case "!mudaidioma":
            comandosPvt($message,null,null, $write, $_SERVER['TWITCH_CHANNEL']);
            break;
          case "!sechama":
          case "!renomear":
            if(!empty($stack[1]) && !empty($stack[2])){
              $oldNick = $stack[1];
              $newNick = $stack[2];
              $index = $this->verificaUserNoChat($oldNick);
              comandosPvt($message, null,null, $write, $_SERVER['TWITCH_CHANNEL'],  $this->pessoasNoChat[$index]);
            }
            break;
          case "!au":
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ");
            break;
          case "!🍌":
          case "!banana":
          case "!adoteobanana":
            $header = array("time" => date('Y-m-d H:i:s'), 'type'=> 'banana');
            $mensagem = array('header' => $header, 'data' => null);
            file_put_contents('dados_tela.json', json_encode($mensagem));
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'],"Oi gente, eu sou o Banana 🍌! Fui resgatado por esse humano aí e estou a procura de um novo lar para poder correr e brincar. Sou muito brincalhão, ativo e serelepe. Tenho entre 7 e 9 meses e estou em Joinville/SC (mas posso ir até Florianópolis, Blumenau, Curitiba e região. #adoteobanana");
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ OhMyDog CorgiDerp RalpherZ FrankerZ");
            break;
          case "!records":
            comandosBD($message, $write, $_SERVER['TWITCH_CHANNEL'], null);
            break;
          case "!fdaciuk":
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "!sh fdaciuk");
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'],"Se o @fdaciuk não estivem em live, cola no discord que é sucesso! -> https://discord.gg/x99eevqaHd");
            break;
          case "!mmillecm":
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "!sh mmillecm");
            $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'],"Se a @mmillecm não estivem em live, cola no discord que é sucesso! -> https://discord.gg/f2dFDn4J");
            break;
        };
      }
    }
  }

  public function verificaUserNoChat($username){
    $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
   
    if($index === false){
      $user = new Usuario($username);
      if(!$user->verificarExistenciaUsuario()){
        $user->cadastrarUsuario();
      }else{
        $user->carregarUsuario();
      }
      $dados_twitch = $this->twitch->getUserDetailsByLogin($username);
      if(array_key_exists('data',$dados_twitch) && count($dados_twitch['data']) > 0){
        $user->setTwitchId($dados_twitch['data'][0]['id']);
        $user->atualizaTwitchId();
      }else{
        var_dump("Não consegui acessar a twitch para o user {$username}!\n".json_encode($dados_twitch));
      }
      
      
      array_push($this->pessoasNoChat,array('user' => $username, 'object'=> $user));
      $index = array_search($username,array_column($this->pessoasNoChat, 'user'));
    }
    return $index;
  }

  public function validaAusencia($message, $write){
    if(isset($message['user']))
      $username = str_replace("@", "", $message['user']);
    else
      $username = str_replace(":","",explode("!",$message['prefix'])[0]);
    $index = array_search($username,array_column($this->ausenciaArray, 'user'));
    if($index !== false){
      $tipoAusencia = $this->ausenciaArray[$index]['event'];
      $write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $this->retornaMensagemAusencia($username, $tipoAusencia));
      unset($this->ausenciaArray[$index]);
      $this->ausenciaArray = array_values($this->ausenciaArray);
    }
  }

  private function retornaMensagemAusencia($username, $tipoAusencia){
    if($tipoAusencia === 'lurk'){
      $length = count(Mensagens::getMensagem('lurkMessages',null)); 
      $pos = mt_rand (0, $length-1);
      return Mensagens::getMensagemArray('lurkMessages',$pos, array(":nick" => $username));
    }else{
      // return $mensagensReuniao[rand(0,count($mensagensReuniao)-1)];
      $length = count(Mensagens::getMensagem('meetingMessages',null)); 
      $pos = mt_rand (0, $length-1);
      return Mensagens::getMensagemArray('meetingMessages',$pos, array(":nick" => $username));
    }
  }

  //TODO não está atualizando a lista de subs
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
