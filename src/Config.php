<?php

namespace AdielSeffrinBot;

class Config
{

  private $password;
  private $seuBot;
  private $seuCanal;
  private $twitter_keys;
  private $retweetTime;
  private $primeTime;
  private $usuarioBD;
  private $senhaBD;
  //configurações do app
  private $twitch_keys;
  






  public function __construct()
  {
    $this->password = 'oauth:g8t0i611rubw77uz1fb4ksrisu7d0n';
    $this->seuBot = 'adielseffrinbot'; //substituir pelo nick do seu bot
    $this->seuCanal = '#adielseffrin'; //substituir pelo nome da sua live exemplo #pokemaobr
    $this->retweetTime = 10 * 60;
    $this->primeTime = 10 * 60;
    $this->usuarioBD = 'adielseffrin';
    $this->senhaBD = 'password';
    
    $this->twitch_keys = [
      //informações em dev.twitch.tv
      'clientId' => 'mtz17wt04kh5v7u2h2hj9m4gg5stv8',
      'clientSecret' => 'wr62oi29gg6rqigtxxbtnwnrgrfpo8',
      //App Access Token
      //POST https://id.twitch.tv/oauth2/authorize?client_id=mtz17wt04kh5v7u2h2hj9m4gg5stv8&redirect_uri=https://127.0.0.1:8080&response_type=token&scope=channel:read:subscriptions user:read:subscriptions chat:edit chat:read channel_subscriptions
      'clientToken' => '4k8urlq0udst5bh6r8khxjg8cgb2bc',
      //GET https://id.twitch.tv/oauth2/token?client_id=mtz17wt04kh5v7u2h2hj9m4gg5stv8&scope=channel:read:subscriptions user:read:subscriptions chat:edit chat:read channel_subscriptions&client_secret=wr62oi29gg6rqigtxxbtnwnrgrfpo8&grant_type=client_credentials
      'userToken' => 'n82jtkrz2z8ckm46gy8mdxyyo4sckm',
      //info pública
      'userId' => '89302205',
    ];
  
    $this->twitter_keys = [
      'TWITTER_API_KEY' => "n0B5XOgaoCKKOxKn88Iy0uTYl",
      'TWITTER_SECRET_KEY' => "Tcl7m4yXgmjiiQuROjWGQhPuQArDCIaQPz780PrHtHgjsyyvzn",
      'TWITTER_ACCESS_TOKEN' => "15150876-JKXQGeCOF9fFfG4UseT2KcxETxVG09ViQ64N43eMU",
      'TWITTER_SECRET_TOKEN' => "n8dmFzfb3RVFyb1wGh5ITrQpsxvyOKmVETaTymBjTc32H",
      'TWITTER_BEARER_TOKEN' => "AAAAAAAAAAAAAAAAAAAAAG%2BKJwEAAAAA4SIXUYX1zl2OdPoz0V%2B8uBc6%2Fkg%3Dw0TeFKUTkCE7ia2FfTjeWSjtZGaInRtJiLPgxCBn8K8l2pPQeC"
    ];
  }

  public function getPassword()
  {
    return $this->password;
  }

  public function getBotName()
  {
    return $this->seuBot;
  }

  public function getChannelName()
  {
    return $this->seuCanal;
  }

  public function getTwitterKeys()
  {
    return $this->twitter_keys;
  }

  public function getRetweetTime()
  {
    return $this->retweetTime;
  }

  public function getTwitchKeys()
  {
    return $this->twitch_keys;
  }

  public function getPrimeTime()
  {
    return $this->primeTime;
  }

  public function getUserBD()
  {
    return $this->usuarioBD;
  }

  public function getSenhaBD()
  {
    return $this->senhaBD;
  }
}
