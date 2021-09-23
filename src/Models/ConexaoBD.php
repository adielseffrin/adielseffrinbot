<?php
namespace AdielSeffrinBot\Models;

class ConexaoBD{
  public static $instance;

  public function __construct(){}
  
  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new \PDO("{$_SERVER['DATABASE_DRIVER']}:host={$_SERVER['DATABASE_HOST']};dbname={$_SERVER['DATABASE_NAME']}", $_SERVER['DATABASE_USER'], $_SERVER['DATABASE_PASSWORD']);
      self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      self::$instance->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_EMPTY_STRING);
    }

    return self::$instance;
  }
}