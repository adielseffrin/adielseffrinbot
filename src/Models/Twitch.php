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
    $this->userId = $_SERVER['TWITCH_API_USERID'];
    
  }

  public function getSubs(){
    $url = "https://api.twitch.tv/helix/subscriptions?broadcaster_id=$this->userId";
    $data = $this->httpGet($url, null, 'user'); 
    return $data;
    
  }

  public function getUserDetailsById($id){
    $url = "https://api.twitch.tv/helix/users?id=$id";
    return $this->httpGet($url);
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