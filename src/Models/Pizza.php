<?php
namespace AdielSeffrinBot\Models;

class Pizza{
    public static $conn;
    public static $ingrediente;
    public static $timeIngrediente;
    public static $coletores = [];

    public static function sorteiaIngrediente(){
        $stmt = Pizza::$conn->prepare("SELECT * FROM ingredientes order by rand() limit 1;");
        $stmt->execute();
        $result = $stmt->fetch();
        Pizza::$ingrediente = $result;
        Pizza::$timeIngrediente = time();
        Pizza::$coletores = [];
        return $result; 
    }

    public static function coletaAtiva($id_user){
        $t1 = time();
        $t2 = Pizza::$timeIngrediente;
        return ($t1-$t2 < 30 && array_search($id_user,Pizza::$coletores) === false);
    }

    public static function guardaIngrediente($id_user){
        array_push(Pizza::$coletores, $id_user);
        $stmt = Pizza::$conn->prepare('INSERT INTO ingredientes_usuario (id_usuario, id_ingrediente) VALUES (:id_usuario,:id_ingrediente)');
        $stmt->execute(array(':id_usuario'=>$id_user, ':id_ingrediente' => Pizza::$ingrediente['id']));
    }
}