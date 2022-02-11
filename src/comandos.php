<?php
use AdielSeffrinBot\Models\Pizza;
use AdielSeffrinBot\Models\Usuario;
use AdielSeffrinBot\Models\Mensagens;
use AdielSeffrinBot\Models\Language;
use AdielSeffrinBot\Models\Records;

function ban($message, $write, $canal)
{
  $mesagemLower = strtolower($message['params']['text']);
  $stack = explode(" ", $mesagemLower);

  switch (count($stack)) {
    case 1:
      if(isset($message['user']))
        $username = str_replace("@", "", $message['user']);
      else
        $username = str_replace(":","",explode("!",$message['prefix'])[0]);
      switch(Language::getLanguage()){
        case 'en':
          $write->ircPrivmsg($canal, "@$username please get outto learn how to use the !ban command! (Just kidding!, You just need to type the user you want to \"ban\" (!ban user)");
          break;
        default:
          $write->ircPrivmsg($canal, "@$username por favor se retire para aprender a usar o !ban (brincadeira, só precisa dizer quem você quer \"banir\" (!ban user)");
      }
      break;
    case 2:
      $username = str_replace("@", "", $stack[1]);
      $reason = Mensagens::getMensagemArray('banReasons', mt_rand(0, count(Mensagens::getMensagem('banReasons',null))),null);
      $action = Mensagens::getMensagemArray('leaveActions', mt_rand(0, count(Mensagens::getMensagem('leaveActions',null))),null);
      switch(Language::getLanguage()){
        case 'en':
          $write->ircPrivmsg($canal, "@$username was $action $reason");
          break;
        default:
          $write->ircPrivmsg($canal, "@$username foi $action $reason");
      }
      break;
    default:
      $username = str_replace("@", "", $stack[1]);
      $motivo = join(" ", array_slice($stack, 2));
      $write->ircPrivmsg($canal, Mensagens::getMensagem('banWithReason',array(':nick'=>$username,':motivo'=>$motivo )));
  }
}


function perguntas($message, $write, $canal)
{
  $mesagemLower = strtolower($message['params']['text']);
  $stack = explode(" ", $mesagemLower);
  if(isset($message['user']))
    $username = str_replace("@", "", $message['user']);
  else
    $username = str_replace(":","",explode("!",$message['prefix'])[0]);

  if (count($stack) > 1) {
    if((strpos($mesagemLower, 'resposta') !== false 
    && strpos($mesagemLower, 'universo')!== false 
    && strpos($mesagemLower, 'vida')!== false 
    && strpos($mesagemLower, 'mais')!== false)||
    (
      strpos($mesagemLower, 'answer') !== false 
      && strpos($mesagemLower, 'universe')!== false 
      && strpos($mesagemLower, 'life')!== false 
      && strpos($mesagemLower, 'more')!== false
    )){
      $write->ircPrivmsg($canal, "@$username 42");
    }elseif (strpos($mesagemLower, ' ou ') > 0 || strpos($mesagemLower, ' or ') > 0) {
      $write->ircPrivmsg($canal, Mensagens::getMensagemArray('orAnswers',rand(0, count(Mensagens::getMensagem('orAnswers', null)) - 1), array(':nick'=>$username)));
    } elseif (strpos($mesagemLower, ' qual ') > 0 || strpos($mesagemLower, ' which ') > 0) {
      $write->ircPrivmsg($canal, Mensagens::getMensagemArray('whichAnswers',rand(0, count(Mensagens::getMensagem('whichAnswers',null)) - 1), array(':nick'=>$username)));
    } else {
      $write->ircPrivmsg($canal, Mensagens::getMensagemArray('generalAnswers',rand(0, count(Mensagens::getMensagem('generalAnswers',null)) - 1), array(':nick'=>$username)));
    }
  } else {
    $write->ircPrivmsg($canal, Mensagens::getMensagem('noQuestion',array(':nick'=>$username)));
  }
}

function comandos($message, $write, $canal)
{
  if(isset($message['user']))
    $username = str_replace("@", "", $message['user']);
  else
    $username = str_replace(":","",explode("!",$message['prefix'])[0]);
  $comandos_p1 = "!ban = 'Bane' um coleguinha do chat (mas é de mentira) || ";
  $comandos_p1 .="!pergunta = Respondo suas dúvidas  mais cabulosas || ";
  $comandos_p1 .="!social = Veja as redes sociais desse streamer || ";
  $comandos_p1 .="!reuniao ou !reunião = Boa reunião pra você || ";
  $comandos_p1 .="!lurk = Obrigado pelo lurk || ";
  $comandos_p1 .="!voltei = Use ao voltar do lurk ou reuniões + ";
  $comandos_p2 .="!foca = Parar de enrolar e focar no código || ";
  $comandos_p2 .="!discord = Venha conhecer o melhor server da galacta || ";
  $comandos_p2 .="!rt = Veja o link do twitter dessa live e ajude aí || ";
  $comandos_p2 .="!prime = Saiba como ajudar a live com seu sub do prime! || ";
  $comandos_p2 .="!fome = Quer ganhar um 'ifood' de até R$20? Acumule pontos e ganhe no final do mês! || ";
  $comandos_p2 .="!ranking = Confira sua posição no nosso jogo!";

  $write->ircPrivmsg($canal, "Ei @$username, confere ai! ->" . $comandos_p1);
  $write->ircPrivmsg($canal, "Seguindo..., ".$comandos_p2);
}

function comandosBD($message, $write, $canal,$usuarioArray){
  $mesagemLower = strtolower($message['params']['text']);
  $stack = explode(" ", $mesagemLower);
  if(isset($message['user']))
    $username = str_replace("@", "", $message['user']);
  else
    $username = str_replace(":","",explode("!",$message['prefix'])[0]);
  if(count($stack) == 1){
    switch($stack[0]){
      case "!fome":
        if(isset($message['user']))
          $username = str_replace("@", "", $message['user']);
        else
          $username = str_replace(":","",explode("!",$message['prefix'])[0]);
        $userObj = $usuarioArray['object'];
        if($userObj->podeJogar()) {
          $pontos = $userObj->jogar();
          if($pontos < 3)
            $msg = Mensagens::getMensagemArray('hungerMessages',"low",array(':nick'=>$username, ':pontos' =>$pontos));
          else if($pontos < 6)
            $msg = Mensagens::getMensagemArray('hungerMessages',"medium",array(':nick'=>$username, ':pontos' =>$pontos));
          else if($pontos < 9.75)
            $msg = Mensagens::getMensagemArray('hungerMessages',"high",array(':nick'=>$username, ':pontos' =>$pontos));
          else 
            $msg = Mensagens::getMensagemArray('hungerMessages',"ultra",array(':nick'=>$username, ':pontos' =>$pontos));
        }
        else 
          $msg = Mensagens::getMensagemArray('hungerMessages',"nomore",array(':nick'=>$username));
        
        $write->ircPrivmsg($canal, $msg);
      break;
      case "!rank":
      case "!ranking":
        $userObj = $usuarioArray['object'];
        $mensagem = $userObj->getRanking();
        $write->ircPrivmsg($canal, $mensagem);
      break;
      case "!pizza":
      case "!🍕":
        $userObj = $usuarioArray['object'];
        if(Pizza::coletaAtiva($userObj->getId()))
          Pizza::executaAcao($userObj);
      break;
      case "!inv":
      case "!inventario":
      case "!inventário":
      case "!🛍":
      case "!bag":
        $userObj = $usuarioArray['object'];
        Pizza::$write = $write;
        Pizza::listarIngredientes($userObj);
        break;
      case "!records":
        $records = new Records();
        $texto = $records->getRecords();
        
        $write->ircPrivmsg($canal, $texto['dia']);
        $write->ircPrivmsg($canal, $texto['mes']);
        $write->ircPrivmsg($canal, $texto['ano']);
        $write->ircPrivmsg($canal, $texto['all']);
        break;
    }
  }elseif(count($stack) == 2){
    switch($stack[0]){
      case "!ranking":
      case "!rank":
        $userObj = new Usuario(str_replace("@", "",$stack[1]));
        $userObj->carregarUsuario();
        if($userObj->getId() > 0){
          $mensagem = $userObj->getRanking();
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
  if(isset($message['user']))
    $username = str_replace("@", "", $message['user']);
  else
    $username = str_replace(":","",explode("!",$message['prefix'])[0]);
  $msg = "Oi @".$username."! Eu sou o Adiel, tenho 32 anos e sou professor universitário e (ex)dev. Sou formado em matemática e pós em computação, mas nas horas vagas faço lives, tipo agora :) Perdemos o foco facilmente, mas sempre tem um !foca pra ajudar";

  $write->ircPrivmsg($canal, $msg);
}

function comandosPvt($message, $twitter, $twitch, $write, $canal, $usuarioArray = null)
{
 
  if(isset($message['user']))
    $username = str_replace("@", "", $message['user']);
  else
    $username = str_replace(":","",explode("!",$message['prefix'])[0]);
  if($username === "adielseffrin"){
    $mesagemLower = strtolower($message['params']['text']);
    $stack = explode(" ", $mesagemLower);
    switch($stack[0]){
      case "!liveon":
        $details = $twitch->getStreamDetails();
        $title = $details['data'][0]['title'];
        $twitter->Tweetar("Oi, sabia que já estamos online para mais uma live que talvez não tenha foco? O título da live de hoje é: \"{$title}\" Chega mais! http://twitch.tv/adielseffrin");
        $write->ircPrivmsg($canal, "Ei @$username, tá postado!");
        $twitter->atualizaRT();
      break;
      case "!tweetapramim":
        unset($stack[0]);
        echo "im here!".PHP_EOL;
        $response = $twitter->Tweetar(implode(" ",$stack) . " (Enviado por adielseffrinbot - lá da twitch!)");
        $write->ircPrivmsg($canal, "Ei @$username, tá postado!");
        break;
      case "!atualizart":
        $twitter->atualizaRT();
        break;
      case "!fomeextra":
          $userObj = $usuarioArray['object'];
          $quantidade = 1;
          if(isset($stack[2])){
            $quantidade = $stack[2];
          }
          if($userObj->addFome($quantidade)){
            // $msg = "Ei @{$userObj->getNick()}, você ganhou mais {$quantidade} !fome extra".($quantidade > 1 ? 's' : '')."!";
            $msg = Mensagens::getMensagem('fomeExtra',array(':nick' => $userObj->getNick(),':quantidade' => $quantidade,':plural' => $quantidade > 1 ? 's' : ''));
            $write->ircPrivmsg($canal, $msg);
          }else{
            $write->ircPrivmsg($canal, "Ei @adielseffrrin, dá um conferes aqui que deu ruim 😂");
          }
        break;
      case "!fomecomprada":
          $userObj = $usuarioArray['object'];
          $quantidade = 1;
          if(isset($stack[2])){
            $quantidade = $stack[2];
          }
          if($userObj->addFomeComprada($quantidade)){
            // $msg = "Ei @{$userObj->getNick()}, você ganhou mais {$quantidade} !fome extra".($quantidade > 1 ? 's' : '')."!";
            $msg = Mensagens::getMensagem('fomeExtra',array(':nick' => $userObj->getNick(),':quantidade' => $quantidade,':plural' => $quantidade > 1 ? 's' : ''));
            $write->ircPrivmsg($canal, $msg);
          }else{
            $write->ircPrivmsg($canal, "Ei @adielseffrrin, dá um conferes aqui que deu ruim 😂");
          }
        break;
      case "!addsub":
        $userObj = $usuarioArray['object'];
        $userObj->addsub();
        $write->ircPrivmsg($canal, "Ei @adielseffrin, @{$userObj->getNick()} agora é um sub 🐱‍🏍!");
        //adicionar pacote prata
        //avisa do pacote para @user
      break;
      case "!testasub":
        $userObj = $usuarioArray['object'];
        Pizza::sorteiaReceitaParaNovoSub($userObj);
        break;
      case "!removesub":
        $userObj = $usuarioArray['object'];
        $userObj->removesub();
        $write->ircPrivmsg($canal, "Ei @adielseffrin, @{$userObj->getNick()} nos deixou 😥");
      break;
      case "!addstreamer":
        $userObj = $usuarioArray['object'];
        $userObj->addStreamer();
        $write->ircPrivmsg($canal, "Ei @adielseffrin, se prepara para acompanhar @{$userObj->getNick()} pois agora sei que é streamer! 📺");
        break;
      case "!removestreamer":
          $userObj = $usuarioArray['object'];
          $userObj->removeStreamer();
          $write->ircPrivmsg($canal, "Ei @adielseffrin, é uma pena mas acho que @{$userObj->getNick()} não faz mais lives! 😢");
        break;
      case "!sechama":
      case "!renomear":
        $userObj = $usuarioArray['object'];
        if($userObj->rename($stack[2]))
          $write->ircPrivmsg($canal, "Ei @adielseffrin, {$userObj->getNick()} agora é @{$stack[2]}");
        break;
      case "!surpresa":
        Pizza::liberaIngrediente(0);
        break;  
      case "!liberapizza":
      case "!freepizza":
        Pizza::sorteiaReceita();
        break; 
      case "!liberaingrediente":
      case "!freeingredient":
          Pizza::liberaIngrediente($stack[1]);
          break;  
      case "!mudaidioma":
        Language::setLanguage($stack[1]);
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
      case "!linkedin":
        $write->ircPrivmsg($canal, "Linkedin: https://www.linkedin.com/in/adielseffrin/");
        break;
      case "!discord":
        $write->ircPrivmsg($canal, "/me Check out our discord server -> https://discord.gg/Cnmr7suCnT");
        // $write->ircPrivmsg($canal, "/me Venha para a caverna! -> https://discord.io/caverna Por favor, não se esqueça de passar no canal #regras para liberar o acesso á todas as salas do nosso servidor ^^");
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
