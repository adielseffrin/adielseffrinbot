<?php
namespace AdielSeffrinBot\Models;

class Configs{
  public static $configs = null;

  public static function loadConfigs(){
    $stmt = ConexaoBD::getInstance()->prepare('SELECT config_key, config_value FROM configs');
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach($result as $k => $v){
      self::$configs[$k] = $v;
    }
  }
  
  public static function getConfigs() {
    return self::$configs;
  }
  
  public static function getConfig($config) {
    foreach(self::$configs as $k => $v){
      if($v['config_key'] == $config)
        return $v['config_value'];
    }
    return false;
  }

  public static function setConfig($config, $value) {
    try{
      $stmt = ConexaoBD::getInstance()->prepare('UPDATE configs SET config_value = :config_value WHERE config_key = :config_key');
      $stmt->execute(array(':config_key'=> $config, ':config_value' => $value ));
      foreach(self::$configs as $k => $v){
        if($v['config_key'] == $config)
          $v['config_value'] = $value;
      }
    }catch(PDOExecption $e) {
      echo '## ERRO AO ATUALIZAR CONFIG ##'.PHP_EOL;
      var_dump($e);
      echo '## ## ## ## ##'.PHP_EOL;
    }
    //TODO testar
  }

}