<?php
namespace AdielSeffrinBot\Models;

class Twitch{
    private $clientId;
    private $clientSecret;
    private $clientToken;
    //configurações de usuário (AdielSeffrin)
    private $userToken;
    private $userId;

    public function __construct()
  {
    $this->clientId = $_SERVER['TWITCH_API_CLIENTID'];
    $this->clientSecret = $_SERVER['TWITCH_API_CLIENTSECRET'];
    $this->clientToken = $_SERVER['TWITCH_API_CLIENTTOKEN'];
    $this->userToken = $_SERVER['TWITCH_API_USERTOKEN'];
    //$this->userToken = $this->getToken();
    $this->userId = $_SERVER['TWITCH_API_USERID'];
    
  }

  // private function getAcessToken(){
  //   $callback_url = "https://127.0.0.1:8080";
  //   $callback_url = "https://531b-2804-14c-f281-8c08-5d6f-5ca8-21c9-70cb.ngrok.io";
  //   $url = "https://id.twitch.tv/oauth2/authorize?client_id=mtz17wt04kh5v7u2h2hj9m4gg5stv8&redirect_uri={$callback_url}&response_type=token&scope=channel:read:subscriptions";
  //   echo PHP_EOL."### Twitch -> getAcessToken() ###".PHP_EOL;
  //   $data = $this->httpPost($url, null); 
  //   var_dump($data);
  // }

  private function getToken(){
    $url = "https://id.twitch.tv/oauth2/token?client_id=mtz17wt04kh5v7u2h2hj9m4gg5stv8&scope=channel:read:subscriptions&client_secret=dw5huedyhv8i7ktabxt1q2wg46ps89&grant_type=client_credentials";
    $data = $this->httpPost($url, null); 
    return $data['access_token'];
  }

  public function getSubs(){
    echo PHP_EOL."### Twitch -> getSubs() ###".PHP_EOL;
    $url = "https://api.twitch.tv/helix/subscriptions?broadcaster_id=$this->userId";
    $data = $this->httpGet($url, null, 'app'); 
    if($_SERVER['DEBUG_MODE']=='all'){
      var_dump($data);
    }
    return $data;
  }

  public function getStreamDetails(){
    $url = "https://api.twitch.tv/helix/channels?broadcaster_id=89302205";
    return $this->httpGet($url);
  }

  public function getUserDetailsById($id){
    $url = "https://api.twitch.tv/helix/users?id=$id";
    return $this->httpGet($url);
  }

  public function getUserDetailsByLogin($id){
    $url = "https://api.twitch.tv/helix/users?login=$id";
    return $this->httpGet($url, null, "user");
  }

  public function httpGet($url, $params = null, $tokenType = 'app')
  {
    //  create request
    if ($params)
      $request = $params;
    else
      $request = [];

    $auth = array(
      'Authorization'        => 'Bearer '.($tokenType == 'user' ? $this->userToken : $this->clientToken),
      'client-id'               => $this->clientId,
    );

    //  merge request and oauth to one array
    $auth = array_merge($auth, $request);

    
    $curl = curl_init();
    //curl_setopt_array($feed, $options);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => $this->buildAuthorizationHeader($auth),
    ));

    $json = curl_exec($curl);
    curl_close($curl);
   
    return json_decode($json, true);
  }

  public function httpPost($url, $params = null, $tokenType = 'app')
  {
    //  create request
    if ($params)
      $request = $params;
    else
      $request = [];

    $auth = array(
      'Authorization'        => 'Bearer '.($tokenType == 'user' ? $this->userToken : $this->clientToken),
      'client-id'               => $this->clientId,
    );

    //  merge request and oauth to one array
    $auth = array_merge($auth, $request);

    
    $curl = curl_init();
    //curl_setopt_array($feed, $options);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => $this->buildAuthorizationHeader($auth),
    ));

    $json = curl_exec($curl);
    curl_close($curl);
   
    return json_decode($json, true);
  }

  private function buildBaseString($baseURI, $method, $params)
  {
    $r = array();
    ksort($params);
    foreach ($params as $key => $value) {
      $r[] = "$key=" . rawurlencode($value);
    }
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
  }

  private function buildAuthorizationHeader($header)
  {
    $r = '';
    $values = array();
    foreach ($header as $key => $value)
      $values[] = "$key: " . $value;
    $r .= implode(", ", $values);
    return $values;
  }



}