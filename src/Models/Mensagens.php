<?php
namespace AdielSeffrinBot\Models;
use AdielSeffrinBot\Models\Language;
class Mensagens{
  public static $mensagensPT = [
    'onJoin' => 'Sou um bot ou um bug?',
    'newSub' => "Ei @adielseffrin, @{:nick} agora é um sub 🐱‍🏍!",
    'onRename' => "Ei @adielseffrin, {:oldNick} agora é @{:nick}",
    'fomeExtra' => "Ei @{:nick}, você ganhou mais {:quantidade} !fome extra{:plural}!",
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
    'onNewRecipe' => "Uma nova receita precisa ser feita! Será que você tem o que é preciso para fazer uma pizza de {:desc}?",
    'onListIngredients' => "Ei @{:nick}! Você tem os seguintes ingredientes guardados: {:listOfIngredients}",
    'hungerMessages' => [
      "nomore" => "Sabia @{:nick} que fome é ou pode ser um estado de espírito? (E você já jogou hoje 🤐)", 
      "low" => "Ei @{:nick} tá com pouca fome né. Seu nível de fome foi {:pontos}",
      "medium" => "Ei @{:nick} que fominha né. Seu nível de fome foi {:pontos}",
      "high" => "@{:nick} !! Que fome toda é essa?? Seu nível de fome foi {:pontos}",
      "ultra" => "Corram para as colinas, pois @{:nick} está com A fome! Seu nível de fome foi {:pontos}"
    ],
    'meetingMessages' => [
      "Hmmmmmmmmmmm.. tu não estavas em reuniao? Hein @{:nick}?",
      "Hmmmmmmmmmmm.. e a reunião @{:nick}?",
      "Ei @{:nick} a chefia sabe que você está em reunião e aqui ao mesmo tempo?",
      "Ih alá, @{:nick}, voltou e nem avisou o chat 😋",
      ],
    "primeMessage" => "Você sabia que é possível vincular a sua conta Amazon Prime com a Twitch e ter uma inscrição de graça(!!) por mês para ajudar o seu canal favorito, ou até esse aqui? Confira abaixo no painel 'Prime' o passo a passo de como fazer!",
    'banReasons' => [
      "por não se comportar!",
      "por não conversar com os amigos!",
      "pois está muito incoveniente hoje!",
      "pois é amigo do pandadomal!",
      "pois não é amigo do deninho!",
      "por programar em java (e gostar)!!",
      "por falar mal de HTML",
      "por fazer fofocas sobre HTML",
      "por falar que HTML é uma linguagem de programação",
      "por falar que HTML não é uma linguagem de programação",
      "por talvez ser um matemático",
      "por não fazer café com água fervendo",
      "por fazer a chimarrão com água fervendo",
      "por ter muito foco",
      "por querer ser produtivo",
      "por dizer que não procrastinar (mentir é feio)"
    ],
    'leaveActions' =>  [
      "banid@",
      "convidad@ a se retirar",
      "ignorad@",
      "estapeado"
    ],
    'banWithReason' => "@{:nick} foi banido por {:motivo}",
    'generalAnswers' => [
      "Depende...",
      "Talvez...",
      "Pode ser que sim, mas pode ser que não",
      "Vamos ver, quem sabe...",
      "Temos que marcar pra ver isso...",
      "Vou ver e te aviso",
      "E por que tu acha que eu sei isso?",
      "Pode ser que sim, pode ser que não.",
      "Ai.. me dá um tempo",
      "Vish, pergunta pra alguém de verdade aí no chat",
      "Isso eu não sei"
    ],
    'orAnswers' => [
      "Sim",
      "Não",
      "Depende",
    ],
    'whichAnswers'=>[
      "Depende...",
      "Vamos ver, quem sabe...",
      "Vou ver e te aviso",
      "Ai.. me dá um tempo",
      "Como vou saber, sou apenas um bot"
    ],
    'noQuestion' => "Sério @{:nick} ?! Vai só me chamar e não falar nada?"
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
    'onNewRecipe' => "A new recipe need to be build! Would you have what it's needed to make an {:desc} pizza?",
    'onListIngredients' => "Hey @{:nick}! You have the following ingrerdients on your bag: {:listOfIngredients}",
    'hungerMessages' => [
      "nomore" => "Hey @{:nick}, do you know that hungry is or could be a state of spirit? (And you have already played today 🤐 )", 
      "low" => "Hey @{:nick}, you are with almost no hungry, right? You hungry level was {:pontos}",
      "medium" => "Hey @{:nick}, what a tiny hungry, huh? You hungry level was {:pontos}",
      "high" => "@{:nick} !! what A hungry, OMG?  You hungry level was {:pontos}",
      "ultra" => "Run to the hills! @{:nick} is with THE HUNGRY!  You hungry level was {:pontos}"
    ],
    'meetingMessages' => [
      "Ahemmmmmmm.. Don't you on a meeting? Huh @{:nick}?",
      "Hmmmmmmmmmmm.. and the meeting? How is it going @{:nick}?",
      "Hey @{:nick}, do your boss know you are on the meeting and here at the same time?",
      "Look at that, @{:nick} came back and didn't tell us 😋",
      ],
      "primeMessage" => "Do you know you can use your Amazon Prime account on Twitch and have a FREE subscription in one channel each month to support your favorite channels or even this one? Check it out below on the Prime panel to see how to do this!",
      'banReasons' => [
        "for not behaving!",
        "for not talk with our friends!",
        "because s/he is to inconvenient today!",
        "because s/he is pandadomal's friend!",
        "because s/he is not deninhos's friend!",
        "for programming in Java (and like it)!!",
        "for make gossips about HTML",
        "for bad-mouthing HTML",
        "for say that HTML is a programming language",
        "for say that HTML is not a programming language",
        "for maybe be a mathematician",
        "for make coffee with cold water",
        "for make a chimarrão with boiling water",
        "for having so much focus",
        "for want to be productive",
        "for say s/he don't procrastinate (lie is rude)"
      ],
      'leaveActions' => [
        "banned",
        "asked to leave",
        "ignored",
        "kicked out"
      ],
      'banWithReason' => "@{:nick} was banned because {:motivo}",
      'generalAnswers' => [
        "@{:nick}... It depends...",
        "@{:nick}... Maybe...",
        "@{:nick}... Maybe yes, maybe not... I'm not sure",
        "@{:nick}... Let's check it out, who knows...",
        "@{:nick}... We need to set this up to check...",
        "@{:nick}... I'll see and let you know!",
        "@{:nick}... And why do you think I know this?",
        "@{:nick}... Ohh... give me a break",
        "@{:nick}... Ouch, why don't you ask for some real people on chat?",
        "@{:nick}... This, particularly, I don't know"
      ],
      'orAnswers' => [
        "@{:nick}... Yes",
        "@{:nick}... No",
        "@{:nick}... Maybe",
      ],
      'whichAnswers'=>[
        "@{:nick}... It depends...",
        "@{:nick}... Let's check it out, who knows...",
        "@{:nick}... I'll see and let you know!",
        "@{:nick}... Ohh... give me a break",
        "@{:nick}... I don't know, I'm just a bot"
      ],
      'noQuestion' => "Really @{:nick} ?! Are you calling me and don't asking anythiong??"
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