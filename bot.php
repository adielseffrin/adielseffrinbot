<?php

require_once './AdielBot.class.php';
// require_once './Treino.class.php';

// use React\EventLoop\Factory as EventLoopFactory;

$adiel = new AdielBot();
$adiel->run();

// $treino = new Treino();
// $loop = EventLoopFactory::create();
// $treino->setLoop($loop);
// $loop->run();

// $connection = new \Phergie\Irc\Connection();

// use React\EventLoop\Factory as EventLoopFactory;


// $connection
//     ->setServerHostname('irc.chat.twitch.tv')
//     ->setServerPort(6667)
//     ->setPassword($password)
//     ->setNickname($seuBot)
//     ->setUsername($seuBot);

// $client = new \Phergie\Irc\Client\React\Client();
// $loop = EventLoopFactory::create();

// $client->on('connect.after.each', function ($connection, $write) {
//     global $seuCanal;
//     global $debugando;
//     global $twitter;
//     global $twitter_keys;
//     global $loop;

//     $write->ircJoin($seuCanal);
//     $write->ircPrivmsg($seuCanal, 'Cheguei? Depende...');

//     $debugando = new Debugando();
//     $twitter = new Twitter($twitter_keys);


//     lembreteRetweet($loop, $twitter, $write, $seuCanal);
// });

// $client->on('irc.received', function ($message, $write, $connection, $logger) {
//     global $seuCanal;
//     global $debugando;
//     global $twitter;

//     if ($message['command'] == 'PRIVMSG') {

//         $comando = null;
//         if (strripos(strtolower($message['params']['text']), "!") === 0) {
//             $comando = explode(" ", strtolower($message['params']['text']))[0];
//         }

//         if (!is_null($comando)) {
//             switch ($comando) {
//                 case "!ban":
//                     ban($message, $write, $seuCanal);
//                     break;
//                 case "!pergunta":
//                     perguntas($message, $write, $seuCanal);
//                     break;
//                 case "!social":
//                 case "!twitter":
//                 case "!github":
//                 case "!instagram":
//                 case "!discord":
//                     social($message, $write, $seuCanal);
//                     break;
//                 case "!comandos":
//                     comandos($message, $write, $seuCanal);
//                     break;
//                 case "!rt":
//                     retweet($twitter, $write, $seuCanal);
//                     break;
//                 case "!debugando":
//                     $debugando->handleCommand($message, $write, $seuCanal);
//                     break;
//                 case "!reuniao":
//                     $username = str_replace("@", "", $message['user']);
//                     $write->ircPrivmsg($seuCanal, "Boa reuniao @" . $username . "!");
//                     break;
//             };
//         }
//     }
// });


// $client->run($connection);
