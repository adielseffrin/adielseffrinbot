<?php
require_once './player.php';

class Debugando{
  public $players = [];

  public function __construct()
  {
    
  }
    
  
  public function handleCommand($message, $write, $canal){
    $mesagemLower = strtolower($message['params']['text']);
    $stack = explode(" ",$mesagemLower);
    $username = str_replace("@","",$message['user']);
    
    if(!$this->checkPlayer($username))
      $this->addPlayer($username, $write, $canal);
       

  }



  function addPlayer($username,$write, $canal)
  {
    $player = new Player();
    $player->username = $username;
    $write->ircPrivmsg($canal, $player->perguntaClasse($username));
    array_push($this->players,$player);
    var_dump($player->perguntaClasse($username));
  }
  
  function checkPlayer($username)
  {
    return in_array($username,array_map(function($p){return $p->username;},$this->players));
  }

}