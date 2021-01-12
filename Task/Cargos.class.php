<?php

class Cargo
{

  private $descricao;
  private $taxaSucesso;
  private $nivel;

  public function __construct($n)
  {
    $this->nivel = $n;
    $this->loadNivel();
  }

  public function getNivel()
  {
    return $this->nivel;
  }

  public function getDescricao()
  {
    return $this->descricao;
  }

  public function getTaxaSucesso()
  {
    return $this->taxaSucesso;
  }

  private function loadNivel()
  {
    $this->taxaSucesso = $this->nivel * 0.07;
    switch ($this->nivel) {
      case 2:
        $this->descricao = 'Junior/Sandy I';
        break;
      case 3:
        $this->descricao = 'Junior/Sandy II';
        break;
      case 4:
        $this->descricao = 'Junior/Sandy III';
        break;
      case 5:
        $this->descricao = 'Pleno I';
        break;
      case 6:
        $this->descricao = 'Pleno II';
        break;
      case 7:
        $this->descricao = 'Pleno III';
        break;
      case 8:
        $this->descricao = 'Sênior I';
        break;
      case 9:
        $this->descricao = 'Sênior II';
        break;
      case 10:
        $this->descricao = 'Sênior III';
        break;
      default:
        $this->descricao = 'Treinee';
        $this->taxaSucesso = 0.07;
    }
  }
}
