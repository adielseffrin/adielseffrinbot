<?php

class Database
{
  private $host;
  private $dbname;
  private $username;
  private $password;

  private $conn;

  public function __contruct()
  {
    $this->host = 'xxx';
    $this->dbname = 'xxx';
    $this->username = 'xxx';
    $this->password = 'xxx';

    $this->conn = new PDO(
      "mysql:host={$this->host};dbname={$this->dbname}",
      $this->username,
      $this->password
    );
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function getConn()
  {
    return $this->conn;
  }
}
