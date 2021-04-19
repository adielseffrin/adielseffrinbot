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
    $this->password = 'oauth:thcqnzq1d50pm4vrnx2lycbjo47ydj';
    $this->seuBot = 'adielseffrinbot'; //substituir pelo nick do seu bot
    $this->seuCanal = '#adielseffrin'; //substituir pelo nome da sua live exemplo #pokemaobr
    $this->retweetTime = 10 * 60;
    $this->primeTime = 10 * 60;
    $this->usuarioBD = 'adielseffrin';
    $this->senhaBD = 'password';
    
    $this->twitch_keys = [
      'clientId' => 'mtz17wt04kh5v7u2h2hj9m4gg5stv8',
      'clientSecret' => 'xholx5gq0c9yt413tk5yohqmre6bl8',
      'clientToken' => 'ycg589pcat5sahtvq96gspf0n014jd',
      'userToken' => 'gn4w8rf5s6ss9mk49fksg0thalir1v',
      'userId' => '89302205',
    ];

    $this->twitter_keys = [
      'TWITTER_API_KEY' => "fqSXgmiogC9slJ2wzl3Uk0YOG",
      'TWITTER_SECRET_KEY' => "DNRgelwLggV3q827tG3Vjf07LxAqieKWZq7paLGcvRmCw0vIyW",
      'TWITTER_ACCESS_TOKEN' => "15150876-z0EANFmaR4s61AW6K03bCq2Sw8pWzTAlZR1AF1njQ",
      'TWITTER_SECRET_TOKEN' => "OihFNp7mEWtY4fO1IZH8hnQthMAqBKYxUyRN7slyu3HIJ",
      'TWITTER_BEARER_TOKEN' => "AAAAAAAAAAAAAAAAAAAAAG%2BKJwEAAAAA%2FWJGBFpBHbPghQfeXNNXNxo9pLg%3D4XikaqbE2Ju86OXRHXFnTBykOh1ENppCAjkWM3jBlXeglHZtpQ"
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
