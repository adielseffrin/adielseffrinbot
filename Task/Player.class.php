<?php
require_once 'Database.class.php';

class Player
{
  private $nick;
  private $experiencia;
  private $acaoRealizada;
  private $teveResultado;
  private $ultimoResultado;
  private $conhecimentoLinguagemList;
  private $cargoAtual;
  private $subscriber;

  private $db;

  public function __construct($nick)
  {
    $playerBD = $this->selectPlayerBD($nick);
    if ($playerBD != null) {
      $this->loadPlayer($playerBD);
    } else {
      $this->newPlayer($nick);
    }
  }

  public function getNick()
  {
    return $this->nick;
  }

  private function loadPlayer($player)
  {
    return null;
  }

  private function selectPlayerBD($nick)
  {
    return null;
  }

  private function newPlayer($nick)
  {
    $this->nick = $nick;
    $this->experiencia = 0;
    $this->conhecimentoLinguagemList = array();
    $this->cargoAtual = new Cargo(1);
  }

  public function estudar($linguagem, $framework)
  {
  }

  public function resolver($linguagem, $framework)
  {
  }

  public function resetMensal()
  {
  }

  public function resetRodada()
  {
    $this->acaoRealizada = false;
    $this->teveResultado = false;
    $this->ultimoResultado = '';
  }

  public function mudarNick($novoNick)
  {
    $this->nick = $novoNick;
    $this->salvarDadosBD();
  }

  public function salvarDadosBD()
  {
  }
}
