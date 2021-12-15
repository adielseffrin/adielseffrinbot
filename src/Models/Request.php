<?php
namespace AdielSeffrinBot\Models;

use AdielSeffrinBot\Models\JwtBot;

class Request
{
    // public function __construct()
    // {
    //   return $this;
    // }

    public function httpGet($url, $params = null)
    {
      //  create request
      if ($params)
        $request = $params;
      else
        $request = [];
  
      //  make request
      $options = array(
       // CURLOPT_HTTPHEADER => $header,
        CURLOPT_HEADER => false,
        CURLOPT_URL => $url . "?" . http_build_query($request),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
      );
      
      $feed = curl_init();
      curl_setopt_array($feed, $options);
      $json = curl_exec($feed);
      curl_close($feed);
     
      return json_decode($json, true);
    }

    public function httpPost($url, $body, $params = null, $auth = null)
  {
    if ($params)
      $request = $params;
    else
      $request = [];

    if($auth){
        $jwt = new JwtBot();
        $payload = array("user_id" => $auth['twitch_id']);
        $payload = $jwt->encodeMessage($payload);
    }
    $header = array(
        "Content-Type: application/json",
        "JWT: ".$payload
    );
    
    $data = json_encode($body);
    
    $options = array(
      CURLOPT_HTTPHEADER => $header,
      CURLOPT_HEADER => false,
      CURLOPT_URL => $url . "?" . http_build_query($request),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $data
    );

   
    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);
    return json_decode($json, true);
  }
}