<?php
require_once 'Database.class.php';
require_once 'Player.class.php';

class Rodada
{

  private $linguagesFrameworks;
  private $linguagemAtiva;
  private $frameworkAtivo;
  private $rodadaAtiva;
  private $estudosDisponiveis;
  private $playerList;
  private $db;


  public function __construct()
  {
    $this->linguagesFrameworks = array();
    $this->playerList = array();
    $db = new Database();
    $this->db = $db->getConn();
    $this->loadPlayersBD();
  }

  public function tratarComando($comando, $write, $canal)
  {
    switch ($comando) {
      case "!job":
    }
  }

  /*Ações para linguagens */

  public function loadLinguagensFrameworks()
  {
    foreach ($this->buscaLinguagensBD() as $ling) {
      array_push($this->linguagesFrameworks, new Linguagem($ling));
    }
  }

  private function buscaLinguagensBD()
  {
    return array('php', 'java', 'c#', 'go');
  }

  public function selecionaLinguagem()
  {
    $this->linguagemAtiva = $this->linguagesFrameworks[0];
  }

  /* Ações para players */

  private function loadPlayersBD()
  {
    $sql = 'SELECT * FROM players';
    $data = $this->db->query($sql);
    foreach ($data as $row) {
      array_push($this->playerList, new Player($row['nick']));
    };
  }

  public function getPlayer($nick)
  {
    $player = array_filter($this->playerList, function ($val) use ($nick) {
      return $val->getNick() === $nick;
    });
  }
}
