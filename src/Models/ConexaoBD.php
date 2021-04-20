<?php
namespace AdielSeffrinBot\Models;

class ConexaoBD{
    
  private $conn;

  public function __construct(){}

  public function connect(){
    try {
        $this->conn = new \PDO("{$_SERVER['DATABASE_DRIVER']}:host={$_SERVER['DATABASE_HOST']};dbname={$_SERVER['DATABASE_NAME']}", $_SERVER['DATABASE_USER'], $_SERVER['DATABASE_PASSWORD']);
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
  }

  public function getConn(){
    return $this->conn;
  }

}