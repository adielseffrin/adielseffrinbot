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
    "por talvez ser um matemático",
    "por não fazer café com água fervendo",
    "por ter muito foco",
    "por querer ser produtivo",
    "por dizer que não procrastinas (mentir é feio)"
  );

  $retiradas = array(
    "banid@",
    "convidad@ a se retirar",
    "ignorad@",
    "chamad@ no SOE",
    "ignorad@"
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
    "Ai.. me dá um tempo",
    "Vish, pergunta pra alguém de verdade aí no chat",
    "Isso eu não sei"
  );

  $qual = array(
    "Depende...",
    "Vamos ver, quem sabe...",
    "Vou ver e te aviso",
    "Ai.. me dá um tempo",
    "Como vou saber, sou apenas um bot"
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
    if(strpos($mesagemLower, 'resposta') !== false 
    && strpos($mesagemLower, 'universo')!== false 
    && strpos($mesagemLower, 'vida')!== false 
    && strpos($mesagemLower, 'mais')!== false){
      $write->ircPrivmsg($canal, "@$username 42");
    }elseif (strpos($mesagemLower, 'ou') > 0) {
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
  $comandos_p1 = "!ban = 'Bane' um coleguinha do chat (mas é de mentira) || ";
  $comandos_p1 .="!pergunta = Respondo suas dúvidas  mais cabulosas || ";
  $comandos_p1 .="!social = Veja as redes sociais desse streamer || ";
  $comandos_p1 .="!reuniao ou !reunião = Boa reunião pra você || ";
  $comandos_p1 .="!lurk = Obrigado pelo lurk || ";
  $comandos_p1 .="!voltei = Use ao voltar do lurk ou reuniões + ";
  $comandos_p2 .="!foca = Parar de enrolar e focar no código || ";
  $comandos_p2 .="!discord = Venha conhecer o melhor server da galacta || ";
  $comandos_p2 .="!rt = Veja o link do twitter dessa live e ajude aí || ";
  $comandos_p2 .="!prime = Saiba como ajudar a live com seu sub do prime! ";

  $write->ircPrivmsg($canal, "Ei @$username, confere ai! ->" . $comandos_p1);
  $write->ircPrivmsg($canal, "Seguindo..., ".$comandos_p2);
}

function comandosBD($message, $write, $canal, $conn, $usuarioArray){
  $mesagemLower = strtolower($message['params']['text']);
  $stack = explode(" ", $mesagemLower);
  $username = $message['user'];
  if(count($stack) == 1){
    switch($stack[0]){
      case "!fome":
        $username = str_replace("@", "", $message['user']);
        $userObj = $usuarioArray['object'];
        if($userObj->podeJogar($conn)) {
          $pontos = $userObj->jogar($conn);
          if($pontos < 3)
            $write->ircPrivmsg($canal, "Ei @" . $username . " tá com pouca fome né. Seu nível de fome foi {$pontos}");
          else if($pontos < 6)
            $write->ircPrivmsg($canal, "Ei @" . $username . " que fominha né. Seu nível de fome foi {$pontos}");
          else if($pontos < 9.75)
            $write->ircPrivmsg($canal, "@" . $username . " !! Que fome toda é essa?? Seu nível de fome foi {$pontos}");
          else 
            $write->ircPrivmsg($canal, "Corram para as colinas, pois @" . $username . " está com A fome! Seu nível de fome foi {$pontos}");
        }
        else $write->ircPrivmsg($canal, "Sabia @" . $username . " que fome é ou pode ser um estado de espírito? (E você já jogou hoje 🤐)");
      break;
      case "!ranking":
        $userObj = $usuarioArray['object'];
        $mensagem = $userObj->getRanking($conn);
        $write->ircPrivmsg($canal, $mensagem);
      break;
    }
  }elseif(count($stack) == 2){
    switch($stack[0]){
      case "!ranking":
        $userObj = new Usuario(str_replace("@", "",$stack[1]));
        $userObj->carregarUsuario($conn);
        if($userObj->getId() > 0){
          $mensagem = $userObj->getRanking($conn);
          $write->ircPrivmsg($canal, $mensagem);
        }else{
          $write->ircPrivmsg($canal, "Pois olha, não achei essa pessoa aí não 😥");
        }

      break;
    }
  }
}

function apresentar($message, $write, $canal)
{
  $username = $message['user'];
  $msg = "Oi @".$username."! Eu sou o Adiel, tenho 32 anos e sou professor universitário e (ex)dev. Sou formado em matemática e pós em computação, mas nas horas vagas faço lives, tipo agora :) Perdemos o foco facilmente, mas sempre tem um !foca pra ajudar";

  $write->ircPrivmsg($canal, $msg);
}

function comandosPvt($message, $twitter, $write, $canal, $conn = null, $usuarioArray = null)
{
 
  $username = $message['user'];
  if($username === "adielseffrin"){
    $mesagemLower = strtolower($message['params']['text']);
    $stack = explode(" ", $mesagemLower);
    switch($stack[0]){
      case "!liveon":
        $twitter->Tweetar("Oi, sabia que já estamos online para mais uma live que talvez não tenha foco? Chega mais! http://twitch.tv/adielseffrin");
        $write->ircPrivmsg($canal, "Ei @$username, tá postado!");
        $twitter->atualizaRT();
      break;
      case "!tweetapramim":
        unset($stack[0]);
        $response = $twitter->Tweetar(implode(" ",$stack) . " (Enviado por adielseffrinbot - lá da twitch!)");
        //var_dump($response);
        $write->ircPrivmsg($canal, "Ei @$username, tá postado!");
        break;
      case "!atualizart":
        $twitter->atualizaRT();
        break;
      case "!addsub":
        $userObj = $usuarioArray['object'];
        $userObj->addsub($conn);
        $write->ircPrivmsg($canal, "Ei @adielseffrin, @{$userObj->getNick()} agora é um sub 🐱‍🏍!");
      break;
      case "!removesub":
        $userObj = $usuarioArray['object'];
        $userObj->removesub($conn);
        $write->ircPrivmsg($canal, "Ei @adielseffrin, @{$userObj->getNick()} nos deixou 😥");
      break;
    }

  }
  //$write->ircPrivmsg($canal, "Calma @$username... ainda não sei tudo que tenho :(");
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

function prime($write, $canal)
{
  $text = "Você sabia que é possível vincular a sua conta Amazon Prime com a Twitch e ter uma inscrição de graça(!!) por mês para ajudar o seu canal favorito, ou até esse aqui? Confira abaixo no painel 'Prime' o passo a passo de como fazer!";
  $write->ircPrivmsg($canal, $text);
}

function testSocket($connector){
  echo "############### TO AQUI ###############";
  $connector->connect('127.0.0.1:7181')->then(function (React\Socket\ConnectionInterface $connection)  {
    //$connection->pipe(new React\Stream\WritableResourceStream(STDOUT, $loop));
    $connection->write("Hello World!\n");
});
}

function overlay(){
  $url = "http://127.0.0.1:3333";
  $data = array('acao'=>'teste');
  httpPost($url,$data);
}

function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function errou($message, $write, $canal)
{
  $file = 'errou.txt';
  //$current = file_get_contents($file);
  $current = "Testeeee\n";
  file_put_contents($file, $current, FILE_APPEND);
}
