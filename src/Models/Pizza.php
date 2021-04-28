<?php
namespace AdielSeffrinBot\Models;

class Pizza{
    public static $conn;
    public static $ingrediente;
    public static $receita;
    public static $timeColeta;
    public static $coletores = [];
    public static $write;
    private static $rodada = 0;
    private static $trigger = 0;

    public static function sorteia(){
        echo "Rodada: ".Pizza::$rodada." --- Trigger: ".Pizza::$trigger.PHP_EOL;
        if(Pizza::$trigger == 0) Pizza::$trigger = mt_rand(1, 6);
        $condicao = Pizza::$rodada++ != Pizza::$trigger;

        if($condicao)
            Pizza::sorteiaIngrediente();
        else{
            Pizza::$trigger = mt_rand (1, 6);
            Pizza::$rodada = 0;
            Pizza::sorteiaReceita();
        }
    }

    public static function sorteiaIngrediente(){
        $stmt = Pizza::$conn->prepare("SELECT * FROM ingredientes order by rand() limit 1;");
        $stmt->execute();
        $result = $stmt->fetch();
        Pizza::$ingrediente = $result;
        Pizza::$receita = null;
        Pizza::$timeColeta = time();
        Pizza::$coletores = [];
        Pizza::avisarChat();
        //return $result; 
    }

    public static function sorteiaReceita(){
        $stmt = Pizza::$conn->prepare("SELECT id FROM pizzas ORDER by rand() LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        $pid = $result["id"];
        $stmt = Pizza::$conn->prepare("
            SELECT p.descricao AS pizza, i.descricao AS ingrediente FROM pizzas AS p 
            INNER JOIN ingredientes_pizzas AS ip 
            ON p.id = ip.id_pizza
            INNER JOIN ingredientes AS i
            ON ip.id_ingrediente = i.id
            WHERE p.id = :pid;
         ");
        $stmt->execute(array(":pid" => $pid));
        $result = $stmt->fetchAll();
        if(count($result)>0){
            $pizza = $result[0]["pizza"];  
        }
        $ingr = array();
        foreach($result as $r){
            array_push($ingr,$r["ingrediente"]);
        }
        Pizza::$receita = array("id" => $pid, "descricao" => $pizza." (".implode(", ", $ingr).")");
        Pizza::$ingrediente = null;
        Pizza::$timeColeta = time();
        Pizza::$coletores = [];
        Pizza::avisarChat();
        //return $result; 
    }

    public static function coletaAtiva($id_user){
        $t1 = time();
        $t2 = Pizza::$timeColeta;
        return ($t1-$t2 < 30 && array_search($id_user,Pizza::$coletores) === false);
    }

    public static function executaAcao($objUser){
        if(Pizza::$ingrediente !== null){
            Pizza::guardaIngrediente($objUser);
        }else{
            Pizza::preparaReceita($objUser);
        }
     }

    public static function guardaIngrediente($objUser){
         //alterar tabela para ter a quantidade
         //verificar se usuario tem ingrerdiente
         //se tiver, altera a quantidade
         //senoa, adiciona registro
        array_push(Pizza::$coletores, $objUser->getId());
        $stmt = Pizza::$conn->prepare('INSERT INTO ingredientes_usuario (id_usuario, id_ingrediente) VALUES (:id_usuario,:id_ingrediente)');
        $stmt->execute(array(':id_usuario'=>$objUser->getId(), ':id_ingrediente' => Pizza::$ingrediente['id']));
        
        $text = "@".$objUser->getNick()." coletou ".Pizza::$ingrediente['descricao'] ."!";
        Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $text);
    }

    public static function preparaReceita($objUser){
        array_push(Pizza::$coletores, $objUser->getId());
        //validar ingredientes
        //$stmt = Pizza::$conn->prepare('INSERT INTO ingredientes_usuario (id_usuario, id_ingrediente) VALUES (:id_usuario,:id_ingrediente)');
        //$stmt->execute(array(':id_usuario'=>$id_user, ':id_ingrediente' => Pizza::$receita['id']));
        $pontos = Pizza::jogar($objUser);
        $text = "@".$objUser->getNick()." criou uma pizza de ".Pizza::$receita['descricao'] ." deliciosa! Ganhou $pontos pontos!!";
        Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $text);
    }

    public static function jogar($objUser){
        $pontos = mt_rand (5, 9) + mt_rand (0, 99)/100;
        var_dump($pontos);
        $pontos = 0;
        $stmt = Pizza::$conn->prepare('INSERT INTO tentativas_fome (id_usuario, pontos) VALUES (:id_usuario, :pontos)');
        $stmt->execute(array(':id_usuario'=>$objUser->getId(), ':pontos' => $pontos));  
        return $pontos;  
      }

    public static function avisarChat(){
        //salvar mensagem do ingrerdiente com emoji no banco 
        if(Pizza::$ingrediente !== null)
            $text = Pizza::$ingrediente["mensagem"];
        else
            $text = "Uma nova receita precisa ser feita! Será que você tem o que é preciso para fazer uma pizza de ".Pizza::$receita["descricao"]."?";

        Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $text." (Digite !pizza)");
    }

}