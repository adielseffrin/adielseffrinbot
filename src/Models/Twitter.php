<?php
namespace AdielSeffrinBot\Models;

class Twitter
{

  private $api_key;
  private $secret_key;
  private $access_token;
  private $secret_token;
  private $bearer_key;

  private $ultimoTweet;

  public function __construct($keys)
  {
    $this->api_key = $keys['TWITTER_API_KEY'];
    $this->secret_key = $keys['TWITTER_SECRET_KEY'];
    $this->access_token = $keys['TWITTER_ACCESS_TOKEN'];
    $this->secret_token = $keys['TWITTER_SECRET_TOKEN'];
    $this->bearer_key = $keys['TWITTER_BEARER_TOKEN'];

    $this->ultimoTweet = $this->getUltimoTweet();
    
  }

  function getRetweetText()
  {
    if ($this->ultimoTweet == null) {
      $text = "Quer saber das próximas lives ou aleatoriedades? Cola no twitter => http://twitter.com/adielseffrin";
    } else {
      $text = "O QUEEE? Ainda não deu o RT? Ajuda lá que não custa nada pois esses bugs não vão se espalhar sozinhos! https://twitter.com/adielseffrin/status/" . $this->getUltimoTweet();
    }

    return $text;
  }

  public function getUser($user)
  {
    $url = "https://api.twitter.com/2/users/by/username/" . $user;
    return $this->httpGet($url);
  }

  public function atualizaRT(){
    $this->ultimoTweet = $this->getUltimoTweet();
    var_dump($this->ultimoTweet);
  }

  public function getUltimoTweet()
  {
    if ($this->ultimoTweet == null) {
      $dados = $this->makeSearch('adielseffrin');
      
      $meusTweets = array_filter($dados['data'], function ($v, $k) {
        return $v['author_id'] == "15150876"
          && date('Y-m-d', strtotime($v['created_at'])) >= date("Y-m-d")
          && $v["entities"]["urls"][0]['display_url'] == "twitch.tv/adielseffrin";
      }, ARRAY_FILTER_USE_BOTH);
      
      if (count($meusTweets) > 0)
        $this->ultimoTweet = reset($meusTweets)['id'];
    }
    return $this->ultimoTweet;
  }

  public function makeSearch($query)
  {
    $url = "https://api.twitter.com/2/tweets/search/recent";
    $params = array(
      'query' => $query, "tweet.fields" => "author_id,created_at,entities,geo,in_reply_to_user_id,lang,possibly_sensitive,referenced_tweets,source"
    );

    return $this->httpGet($url, $params);
  }

  public function httpGet($url, $params = null)
  {
    $access_token = $this->access_token;
    $token_secret  = $this->secret_token;
    $consumer_key = $this->api_key;
    $consumer_secret  = $this->secret_key;

    $twitter_timeline = "user_timeline";  //  mentions_timeline / user_timeline / home_timeline / retweets_of_me

    //  create request
    if ($params)
      $request = $params;
    else
      $request = [];

    $oauth = array(
      'oauth_consumer_key'        => $consumer_key,
      'oauth_nonce'               => time(),
      'oauth_signature_method'    => 'HMAC-SHA1',
      'oauth_token'               => $access_token,
      'oauth_timestamp'           => time(),
      'oauth_version'             => '1.0'
    );

    //  merge request and oauth to one array
    $oauth = array_merge($oauth, $request);

    //  do some magic
    $base_info              = $this->buildBaseString($url, 'GET', $oauth);
    $composite_key          = rawurlencode($consumer_secret) . '&' . rawurlencode($token_secret);
    $oauth_signature            = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature']   = $oauth_signature;

    //  make request
    $header = array($this->buildAuthorizationHeader($oauth), 'Expect:');
    $options = array(
      CURLOPT_HTTPHEADER => $header,
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

  public function Tweetar($mensagem)
  {
    $url = 'https://api.twitter.com/1.1/statuses/update.json';
    $params = [
      'status' => $mensagem,
      'source' => 'adielseffrinbot'
    ];
    return $this->httpPost($url,$params);
  }

  public function httpPost($url, $params = null)
  {
    $access_token = $this->access_token;
    $token_secret  = $this->secret_token;
    $consumer_key = $this->api_key;
    $consumer_secret  = $this->secret_key;

    if ($params)
      $request = $params;
    else
      $request = [];

    $oauth = array(
      'oauth_consumer_key'        => $consumer_key,
      'oauth_nonce'               => time(),
      'oauth_signature_method'    => 'HMAC-SHA1',
      'oauth_token'               => $access_token,
      'oauth_timestamp'           => time(),
      'oauth_version'             => '1.0'
    );

    //  merge request and oauth to one array
    $oauth = array_merge($oauth, $request);

    //  do some magic
    $base_info              = $this->buildBaseString($url, 'POST', $oauth);
    $composite_key          = rawurlencode($consumer_secret) . '&' . rawurlencode($token_secret);
    $oauth_signature            = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature']   = $oauth_signature;

    //  make request
    $header = array($this->buildAuthorizationHeader($oauth), 'Expect:');
    $options = array(
      CURLOPT_HTTPHEADER => $header,
      CURLOPT_HEADER => false,
      CURLOPT_URL => $url . "?" . http_build_query($request),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_POST => true
    );
   
    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);
   
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

  private function buildAuthorizationHeader($oauth)
  {
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach ($oauth as $key => $value)
      $values[] = "$key=\"" . rawurlencode($value) . "\"";
    $r .= implode(', ', $values);
    return $r;
  }
}
