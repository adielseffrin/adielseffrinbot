<?php
class Player
{

  const BUGGER = 0;
  const DEBBUGER = 1;

  public $username;
  public $pontos;
  public $follower;
  public $subscriber;
  public $classe;

  public function __construct()
  {
    $this->pontos = 0;
    $this->follower = 0;
    $this->subscriber = 0;
  }

  public function addPontos($pontos)
  {
    $this->pontos += $pontos;
  }

  public function getPontos()
  {
    return $this->pontos;
  }

  public function setBugger()
  {
    $this->classe = 0;
  }

  public function setDebugger()
  {
    $this->classe = 1;
  }

  public function defineClasse()
  {
  }

  public function perguntaClasse($username)
  {
    return "Aqui vai o texto explicativo das classes como msg para o user : " . $username;
  }
}
