<?php

class Linguagem
{
  private $descricao;

  public function __construct($d)
  {
    $this->descricao = $d;
  }

  public function getDescricao()
  {
    return $this->descricao;
  }
}
