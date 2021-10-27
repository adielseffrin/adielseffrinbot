<?php
namespace AdielSeffrinBot\Models;

class Language{
  public static $language;

  public static function startLanguage(){
    if(!isset(self::$language))
      self::$language = $_SERVER['BOTLANGUAGE'] != '' ? $_SERVER['BOTLANGUAGE'] : 'pt_br';
    
    echo PHP_EOL."===========================================".PHP_EOL;
    echo "Bot started on mode: ".self::$language;
    echo PHP_EOL."===========================================".PHP_EOL;
  }
  
  public static function getLanguage() {
    return self::$language;
  }

  public static function setLanguage($lang) {
    
    self::$language = $lang;
   
  }

  public static function resetLanguage() {
    self::$language = $_SERVER['BOTLANGUAGE'] != '' ? $_SERVER['BOTLANGUAGE'] : 'pt_br';
  }
}