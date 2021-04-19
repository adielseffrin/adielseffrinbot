<?php
namespace AdielSeffrinBot\Models;

class ConexaoBD{
    
  private $usuario;
  private $senha;
  private $conn;

  public function __construct($user, $pass)
  {
      $this->usuario = $user;
      $this->senha = $pass;
  }

  public function connect(){
    try {
        $this->conn = new \PDO('mysql:host=localhost;dbname=basedobot', $this->usuario, $this->senha);
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
  }

  public function getConn(){
    return $this->conn;
  }

}