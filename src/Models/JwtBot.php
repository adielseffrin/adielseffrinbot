<?php
namespace AdielSeffrinBot\Models;

use AdielSeffrinBot\Models\Language;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class JwtBot{
    private $key;


    public function __construct()
    {
        $this->key = $_SERVER['JWT_SECRET'];
    }

    public function buildMessage($twitch_id, $ingredients_list, $points){
        return json_encode(
            array(
                'twitch_id' => $twitch_id,
                'ingredients_list' => $ingredients_list,
                'points' => $points,
                )
            );
    }

    public function encodeMessage($message){
        $payload = array();
        
        if(is_array($message)){
            foreach ($message as $array_key => $array_value) {
                $payload[$array_key] = $array_value;
            }
        }else{
            $payload = array(
                "message" => $message
            );
        }
       
        $jwt = JWT::encode($payload, base64_decode($this->key), 'HS256');
        var_dump($jwt);
        return $jwt;
    }
    
    public function decodeMessage($jwt){
        $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
        $decoded_array = (array) $decoded;
        return $decoded_array;
    }

    public function decodeLeeWay($jwt){
        JWT::$leeway = 60; // $leeway in seconds
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $decoded_array = (array) $decoded;
        return $decoded_array;
    }
    
}