<?php

function ban($message, $write, $canal)
{

  $motivos = array(
    "por não se comportar!",
    "por não conversar com os amigos!",
    "pois está muito incoveniente hoje!",
    "pois é amigo do pandadomal!",
    "pois não é amigo do deninho!",
    "por programar em java (e gostar)!!",
    "por falar mal de HTML",
    "porque tinha até camiseta...",
    "por talvez ser um matemático"
  );

  $retiradas = array(
    "banid@",
    "convidad@ a se retirar",
    "ignorad@",
    "chamad@ no SOE"
  );

  $mesagemLower = strtolower($message['params']['text']);
  $stack = explode(" ", $mesagemLower);

  switch (count($stack)) {
    case 1:
      $username = str_replace("@", "", $message['user']);
      $write->ircPrivmsg($canal, "@$username por favor se retire para aprender a usar o !ban");
      break;
    case 2:
      $username = str_replace("@", "", $stack[1]);
      $write->ircPrivmsg($canal, "@$username foi {$retiradas[rand(0, count($retiradas) - 1)]} {$motivos[rand(0, count($motivos) - 1)]}");
      break;
    default:
      $username = str_replace("@", "", $stack[1]);
      $motivo = join(" ", array_slice($stack, 2));
      $write->ircPrivmsg($canal, "@$username foi banido por $motivo");
  }
}


function perguntas($message, $write, $canal)
{
  $respostas = array(
    "Depende...",
    "Talvez...",
    "Pode ser que sim, mas pode ser que não",
    "Vamos ver, quem sabe...",
    "Temos que marcar pra ver isso...",
    "Vou ver e te aviso",
    "E por que tu acha que eu sei isso?",
    "Pode ser que sim, pode ser que não.",
    "Ai.. me dá um tempo"
  );

  $qual = array(
    "Depende...",
    "Vamos ver, quem sabe...",
    "Vou ver e te aviso",
    "Ai.. me dá um tempo"
  );

  $ou = array(
    "Sim",
    "Não",
    "Depende",
  );

  $mesagemLower = strtolower($message['params']['text']);

  $stack = explode(" ", $mesagemLower);

  $username = $message['user'];

  if (count($stack) > 1) {
    if (strpos($mesagemLower, 'ou') > 0) {
      $write->ircPrivmsg($canal, "@$username {$ou[rand(0, count($ou) - 1)]}");
    } elseif (strpos($mesagemLower, 'qual') > 0) {
      $write->ircPrivmsg($canal, "@$username {$qual[rand(0, count($qual) - 1)]}");
    } else {
      $write->ircPrivmsg($canal, "@$username {$respostas[rand(0, count($respostas) - 1)]}");
    }
  } else {
    $write->ircPrivmsg($canal, "Sério @$username ?! Vai só me chamar e não falar nada?");
  }
}

function comandos($message, $write, $canal)
{
  $username = $message['user'];
  $write->ircPrivmsg($canal, "Calma @$username... ainda não sei tudo que tenho :(");
}

function social($message, $write, $canal)
{
  $mesagemLower = strtolower($message['params']['text']);
  $stack = explode(" ", $mesagemLower);
  if (count($stack) <= 1) {
    switch ($mesagemLower) {
      case "!social":
        $write->ircPrivmsg($canal, "Ooopa, perai, mas onde? !twitter !github !instagram");
        break;
      case "!twitter":
        $write->ircPrivmsg($canal, "Twitter: http://twitter.com/adielseffrin");
        break;
      case "!github":
        $write->ircPrivmsg($canal, "Github: https://github.com/adielseffrin/");
        break;
      case "!instagram":
        $write->ircPrivmsg($canal, "Instagram: https://instagram.com/adielseffrin");
        break;
      case "!discord":
        $write->ircPrivmsg($canal, "/me Venha para a caverna! -> https://discord.io/caverna Por favor, não se esqueça de passar no canal #regras para liberar o acesso á todas as salas do nosso servidor ^^");
        break;
    }
  }
}

function retweet($twitter, $write, $canal)
{
  $write->ircPrivmsg($canal,  $twitter->getRetweetText());
}

function errou($message, $write, $canal)
{
  $file = 'errou.txt';
  //$current = file_get_contents($file);
  $current = "Testeeee\n";
  file_put_contents($file, $current, FILE_APPEND);
}
