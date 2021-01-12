<?php

class ConhecimentoLinguagem
{
  private $nome;
  private $experiencia;
  private $conhecimentoFrameworkList;

  public function __construct($n, $e)
  {
    $this->nome = $n;
    $this->experiencia = $e;
    $this->conhecimentoFrameworkList = null;
  }
}
