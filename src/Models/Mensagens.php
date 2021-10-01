<?php
namespace AdielSeffrinBot\Models;
use AdielSeffrinBot\Models\Language;
class Mensagens{
  public static $mensagensPT = [
    'onJoin' => 'Sou um bot ou um bug?',
    'newSub' => "Ei @adielseffrin, @{:nick} agora é um sub 🐱‍🏍!",
    'onRename' => "Ei @adielseffrin, {:oldNick} agora é @{:nick}",
    'fomeExtra' => "Ei @{:nick}, você ganhou mais {:quantidade} !fome extra {:plural}!",
    'onMeeting' => "Boa reunião @{:nick}!",
    'onLurk' => "Obrigado pelo lurk @{:nick}!",
    'onReturn' => "Aeeee 🎆🎉🎊, @{:nick}, que bom que você voltou!",
    'lurkMessages' => [
      "Hmmmmmmmmmmm.. tu não estavas de lurk? Hein @{:nick}?",
      "Oh @{:nick}, tu não disse que estavas de lurk? Voltou?",
      "Lurk bom esse hein @{:nick}",
      "Ih alá, @{:nick}, voltou e nem avisou o chat 😋",
    ],
    'onGetIngredient' => "@{:nick} coletou {:quantidade} {:descricao}{:plural}!",
    'onCreatePizza' => "@{:nick} criou uma pizza de {:descricao} deliciosa! Ganhou {:pontos} pontos!!",
    'onMissingIngredient' => "Ei @{:nick} ainda faltam alguns ingredientes para fazer uma pizza de {:descricao}...",
    ];
  public static $mensagensEN = [
    'onJoin' => 'Am I a bot or a bug?',
    'newSub' => "Hey @adielseffrin, @{:nick} is a subscriber now 🐱‍🏍!",
    'onRename' => "Hey @adielseffrin, {:oldNick} is called @{:nick} from now on!",
    'fomeExtra' => "Hey @{:nick}, you won {:quantidade} more !fome !",
    'onMeeting' => "Good meeting @{:nick}!",
    'onLurk' => "Thank you for lurking @{:nick}!",
    'onReturn' => "Weeee 🎆🎉🎊, @{:nick}, we're glad you have returned!",
    'lurkMessages' => [
      "Hmmmmmmmmmmm.. aren't you on lurk? Huh @{:nick}?",
      "Oh @{:nick}, haven't you said that  are you gonna be lurking? Are you back?",
      "Good lurking, rigth @{:nick}?",
      "Look at that, @{:nick} came back and didn't tell us 😋",
    ],
    'onGetIngredient' => "@{:nick} has got {:quantidade} {:descricao}{:plural}!",
    'onCreatePizza' => "@{:nick} has made a delicious pizza of {:descricao}! Won {:pontos} points!!",
    'onMissingIngredient' => "Hey @{:nick}, you don't have all needed ingredients to make a {:descricao} pizza...",

];

  public static function getMensagem($identificador, $params) {
      switch(Language::getLanguage()){
          case 'en':
            return self::replaceOnMessage(self::$mensagensEN[$identificador],$params);
        default:
            return self::replaceOnMessage(self::$mensagensPT[$identificador],$params);
      }
  }

  public static function getMensagemArray($identificador,$position, $params) {
    switch(Language::getLanguage()){
        case 'en':
          return self::replaceOnMessage(self::$mensagensEN[$identificador][$position],$params);
      default:
          return self::replaceOnMessage(self::$mensagensPT[$identificador][$position],$params);
    }
}


  public static function replaceOnMessage($msg, $params){
      if(isset($params)){
        foreach($params as $k => $v){
          $msg = str_replace('{'.$k.'}', $v, $msg);
        }
      }
    return $msg;
  }

  public static function setLanguage($lang) {
    self::$language = $lang;
  }

  public static function resetLanguage() {
    self::$language = $_SERVER['BOTLANGUAGE'] != '' ? $_SERVER['BOTLANGUAGE'] : 'pt_br';
  }
}