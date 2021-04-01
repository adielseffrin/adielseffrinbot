<?php

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
        $this->conn = new PDO('mysql:host=localhost;dbname=basedobot', $this->usuario, $this->senha);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
  }

  public function getConn(){
    return $this->conn;
  }

  // public inserirUsuario($user){
  //   $stmt = $conn->prepare('INSERT INTO usuarios (`nick`, `status`) VALUES (:user, :stats)');
  //   $stmt->execute(array(':user' => $user, ':stats'=>1));
  // }

  

  // public function iniciarReuniao($user){
  //   if(verificarExistenciaUsuario($user)){
  //     $stmt = $conn->prepare('UPDATE usuarios SET `status`= :stats');
  //     $stmt->execute(array(':stats'=>2));
  //   }
  // }

  // public function voltar($user){
  //   $stmt = $conn->prepare('UPDATE usuarios SET `status`= :stats');
  //   $stmt->execute(array(':stats'=>0));
  // }


}