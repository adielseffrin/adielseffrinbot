<?php
//use AdielSeffrinBot\Models\ConexaoBD;
use AdielSeffrinBot\Models\Language;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

namespace AdielSeffrinBot\Models;

class Jwt{
    private $key;


    public function __construct()
    {
        $this->key = $_SERVER['TWITCH_JWT_SECRET'];
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

    public function encode($message){
        $payload = array(
            "message" => $message
        );
        
        $jwt = JWT::encode($payload, $this->key, 'HS256');
    }
    
    public function decode($jwt){
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