<?php
use AdielSeffrinBot\Models\ConexaoBD;

namespace AdielSeffrinBot\Models;

use AdielSeffrinBot\Models\Language;

class Pizza{
    public static $ingrediente;
    public static $ingredientes;
    public static $id_ingredientes;
    public static $receita;
    public static $timeColeta;
    public static $coletores = [];
    public static $write;
    private static $rodada = 0;
    private static $trigger = 0;
    private static $listaDeIngredientes = [];

    private static function listaDeIngredientes(){
        if(empty(Pizza::$listaDeIngredientes)){
            $stmt = ConexaoBD::getInstance()->prepare("SELECT * FROM ingredientes;");
            $stmt->execute();
            return $stmt->fetchAll();
        }else{
            return Pizza::$listaDeIngredientes;
        }
    }

    public static function sorteia(){
        if(Pizza::$trigger == 0) Pizza::$trigger = mt_rand(1, 3);
        $condicao = Pizza::$rodada++ != Pizza::$trigger;
        
        if($condicao)
            Pizza::liberaIngrediente(mt_rand(0,10));
        else{
            Pizza::$trigger = mt_rand (1, 3);
            Pizza::$rodada = 0;
            Pizza::sorteiaReceita();
        }
        echo PHP_EOL."Rodada: ".Pizza::$rodada." --- Trigger: ".Pizza::$trigger.PHP_EOL;
    }

    public static function liberaIngrediente($numero){
        $result = Pizza::listaDeIngredientes()[$numero];
        Pizza::$ingrediente = $result;
        Pizza::$ingredientes = null;
        Pizza::$id_ingredientes = null;
        Pizza::$receita = null;
        Pizza::$timeColeta = time();
        Pizza::$coletores = [];
        Pizza::avisarChat();
    }

    public static function sorteiaPizzaBuscaIngrerdientes(){
        $stmt = ConexaoBD::getInstance()->prepare("SELECT id FROM pizzas ORDER by rand() LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        $pid = $result["id"];

        $language = Language::getLanguage();
        if($language == 'pt_br'){
        $stmt = ConexaoBD::getInstance()->prepare("
            SELECT p.descricao AS pizza, i.descricao AS ingrediente, ip.id_ingrediente FROM pizzas AS p 
            INNER JOIN ingredientes_pizzas AS ip 
            ON p.id = ip.id_pizza
            INNER JOIN ingredientes AS i
            ON ip.id_ingrediente = i.id
            WHERE p.id = :pid;
         ");
        }else{
            $stmt = ConexaoBD::getInstance()->prepare("
            SELECT p.description AS pizza, i.description AS ingrediente, ip.id_ingrediente FROM pizzas AS p 
            INNER JOIN ingredientes_pizzas AS ip 
            ON p.id = ip.id_pizza
            INNER JOIN ingredientes AS i
            ON ip.id_ingrediente = i.id
            WHERE p.id = :pid;
         "); 
        }
        $stmt->execute(array(":pid" => $pid));
        $result = $stmt->fetchAll();
        $ingredientes = [];
        $id_ingredientes = [];
        if(count($result)>0){
            $pizza = $result[0]["pizza"];  
        }
        $ingr = array();
        foreach($result as $r){
            array_push($ingr,$r["ingrediente"]);
            array_push($ingredientes,$r);
            array_push($id_ingredientes,$r['id_ingrediente']);
        }

        return array(
            'pid'=>$pid,
            'pizza'=>$pizza,
            'ingr'=>$ingr,
            'ingredientes'=>$ingredientes,
            'id_ingredientes'=>$id_ingredientes,
        ); 
    }

    public static function sorteiaReceita(){
        $dadosDaBusca = self::sorteiaPizzaBuscaIngrerdientes();
        $pid = $dadosDaBusca['pid'];
        $pizza = $dadosDaBusca['pizza'];
        $ingr = $dadosDaBusca['ingr'];
        $ingredientes = $dadosDaBusca['ingredientes'];
        $id_ingredientes = $dadosDaBusca['id_ingredientes'];
        
        Pizza::$receita = array("id" => $pid, "descricao" => $pizza." (".implode(", ", $ingr).")");
        Pizza::$ingrediente = null;
        Pizza::$ingredientes = $ingredientes;
        Pizza::$id_ingredientes = $id_ingredientes;
        Pizza::$timeColeta = time();
        Pizza::$coletores = [];
        Pizza::avisarChat();
    }

    public static function sorteiaReceitaParaNovoSub($objUser){
        $dadosDaBusca = self::sorteiaPizzaBuscaIngrerdientes();
        $ingredientes = $dadosDaBusca['ingredientes'];
        $id_ingredientes =  $dadosDaBusca['id_ingredientes'];
        $id_ingredientes_list =  implode(',',$id_ingredientes);
        
        $stmt = ConexaoBD::getInstance()->prepare("SELECT id_ingrediente FROM ingredientes_usuario WHERE id_usuario = :id_usuario AND id_ingrediente IN ($id_ingredientes_list)");
        $stmt->execute(array(':id_usuario'=>$objUser->getId()));
        $ingredientesExistentes = $stmt->fetchAll(\PDO::FETCH_NUM);
        if(!$ingredientesExistentes){
            $ingredientesFaltantes = $id_ingredientes;
        }else{
            var_dump($ingredientesExistentes);
            $ingredientesExistentes = array_map(function($e){return $e[0];},$ingredientesExistentes); 
            $ingredientesFaltantes = array_diff($id_ingredientes,$ingredientesExistentes);
        }
        echo "=======================".PHP_EOL;
        var_dump($objUser->getId());
        var_dump($id_ingredientes);
        echo "=======================".PHP_EOL;
        var_dump($ingredientesExistentes);
        echo "=======================".PHP_EOL;
        var_dump($ingredientesFaltantes);
        echo "=======================".PHP_EOL;
        //preve dif null
        try{
            ConexaoBD::getInstance()->beginTransaction();
            foreach($ingredientesFaltantes as $id_ing){
                $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO ingredientes_usuario (id_usuario, id_ingrediente, quantidade) VALUES (:id_usuario,:id_ingrediente, :quantidade)');
                $stmt->execute(array(':id_usuario'=>$objUser->getId(), ':id_ingrediente' => $id_ing, ':quantidade' => 1));
            }
            foreach($ingredientesExistentes as $id_ing){
                $stmt = ConexaoBD::getInstance()->prepare('SELECT id FROM ingredientes_usuario WHERE id_usuario = :id_usuario AND id_ingrediente = :id_ingrediente');
                $stmt->execute(array(':id_usuario'=>$objUser->getId(), ':id_ingrediente' => $id_ing));
                $result = $stmt->fetch();
                $id_ingrediente_usuario = $result['id'];

                $stmt = ConexaoBD::getInstance()->prepare('UPDATE ingredientes_usuario SET quantidade  = quantidade+1 WHERE id = :id_ingrediente_usuario');
                $stmt->execute(array(':id_ingrediente_usuario' => $id_ingrediente_usuario));    
            }
            
            //todo verificar mensagem
            Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], "@".$objUser->getNick().", obrigado por ser um/a sub maravilhoso/a! Por isso você ganhou essa linda sacola que comtem: ".implode(", ",array_map(function($e){return $e[0];},$ingredientes)));
            ConexaoBD::getInstance()->commit();
        }catch(PDOExecption $e) {
            ConexaoBD::getInstance()->rollback();
            print "Error!: " . $e->getMessage() . "</br>";
        } 
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
        array_push(Pizza::$coletores, $objUser->getId());
        $stmt = ConexaoBD::getInstance()->prepare('SELECT id, quantidade FROM ingredientes_usuario WHERE id_usuario = :id_usuario AND id_ingrediente = :id_ingrediente');
        $ingredienteId = Pizza::$ingrediente['id'];
        
        if(Language::getLanguage() == "en"){
            $ingredienteDescricao = Pizza::$ingrediente['description'];
        }else{
            $ingredienteDescricao = Pizza::$ingrediente['descricao'];
        }
        
        
        if(Pizza::$ingrediente['id'] == 11){
            $numero = rand(0,9);
            $result = Pizza::listaDeIngredientes()[$numero];
            $ingredienteId = $result['id'];
            if(Language::getLanguage() == "en"){
                $ingredienteDescricao = $result['description'];
            }else{
                $ingredienteDescricao = $result['descricao'];
            }
        }
        
        $stmt->execute(array(':id_usuario'=>$objUser->getId(), ':id_ingrediente' => $ingredienteId));
        $result = $stmt->fetch();
        $quantidadeSelector = rand(0,100);
        if($quantidadeSelector > 95)
            $quantidadeColetada = 4;
        elseif($quantidadeSelector > 85)
            $quantidadeColetada = 3;
        elseif($quantidadeSelector > 70)
            $quantidadeColetada = 2;
        else
            $quantidadeColetada = 1;

        if($result && $result['quantidade'] >= 0){
            $quantidade = $result['quantidade'];
            $id_ingrediente_usuario = $result['id'];
            $stmt = ConexaoBD::getInstance()->prepare('UPDATE ingredientes_usuario SET quantidade  = :quantidade WHERE id = :id_ingrediente_usuario');
            $stmt->execute(array(':quantidade'=>$quantidade+$quantidadeColetada, ':id_ingrediente_usuario' => $id_ingrediente_usuario));    
        }else{
            $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO ingredientes_usuario (id_usuario, id_ingrediente, quantidade) VALUES (:id_usuario,:id_ingrediente, :quantidade)');
            $stmt->execute(array(':id_usuario'=>$objUser->getId(), ':id_ingrediente' => $ingredienteId, ':quantidade' => $quantidadeColetada));
        }
        $plural = '';
        if($quantidadeColetada > 1) $plural='s';
        $body = array(
            "ingredientes" => array(
              array("ingrediente_id" => $ingredienteId, "quantidade"=> $quantidadeColetada),
            )
          );
          if($_SERVER['USE_API'] == 'true'){
            $request = new Request();
            $data = $request->httpPost("https://api.adielseffr.in/pizza/notificate",$body,null,array("twitch_id"=>$objUser->getTwitchId()));
          }
        //TODO check ingrer description for english
        $text = Mensagens::getMensagem('onGetIngredient',array(
            ':nick'=>$objUser->getNick(),
            ":quantidade"=>$quantidadeColetada,
            ":descricao"=>$ingredienteDescricao,
            ":plural"=>$plural));
        Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $text);
    }

    public static function preparaReceita($objUser){
        array_push(Pizza::$coletores, $objUser->getId());
        //validar ingredientes
        $ingredientes = Pizza::$ingredientes;
        $ids = implode(',',Pizza::$id_ingredientes);
        $stmt = ConexaoBD::getInstance()->prepare("SELECT MIN(quantidade) as total FROM ingredientes_usuario WHERE id_usuario = :id_usuario and id_ingrediente IN ({$ids})");
        $stmt->execute(array(':id_usuario'=>$objUser->getId()));
        
        $result = $stmt->fetch();
        $podeFazer = $result['total'] > 0;
        if($podeFazer){
            $stmt = ConexaoBD::getInstance()->prepare("update ingredientes_usuario set quantidade = quantidade - 1 where id_usuario = :id_usuario and id_ingrediente in ({$ids});");
            $stmt->execute(array(':id_usuario'=>$objUser->getId()));
            $pontos = Pizza::jogar($objUser);
            $text = "@".$objUser->getNick()." criou uma pizza de ".utf8_encode(Pizza::$receita['descricao']) ." deliciosa! Ganhou $pontos pontos!!";
            Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $text);
            $ingrTemp = array();
            foreach(Pizza::$id_ingredientes as $val){
                array_push($ingrTemp, array("ingrediente_id" => $val, "quantidade"=> -1));
            }
            $body = array(
                "info" => array(
                  "pontos" => $pontos
                ),
                "ingredientes" => $ingrTemp
              );
              if($_SERVER['USE_API'] == 'true'){
                $request = new Request();
                $data = $request->httpPost("https://api.adielseffr.in/pizza/notificate",$body,null,array("twitch_id"=>$objUser->getTwitchId()));
              }
        }else{
            $text = "Ei @".$objUser->getNick()." ainda faltam alguns ingredientes para fazer uma pizza de ".Pizza::$receita['descricao'] ."...";
            Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $text);
        }
    }

    public static function listarIngredientes($objUser){
        $stmt = ConexaoBD::getInstance()->prepare(" select nick, descricao,description, quantidade from ingredientes_usuario as iu inner join ingredientes as i on i.id = iu.id_ingrediente inner join usuarios as u on iu.id_usuario = u.id where u.id = :id_usuario;");
        $stmt->execute(array(':id_usuario'=>$objUser->getId()));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $lista = [];
        foreach($result as $key => $val){
            if(Language::getLanguage() == "en"){
                array_push($lista, "{$val['description']}[{$val['quantidade']}]");
            }else{
                array_push($lista, utf8_encode($val['descricao'])."[{$val['quantidade']}]");
            }
           
          }
        // $mensagem = "Ei @{$objUser->getNick()}! Você tem os seguintes ingredientes guardados: ".implode(" | ",$lista);
        $mensagem =  Mensagens::getMensagem('onListIngredients',array(':nick'=>$objUser->getNick(), ':listOfIngredients' => implode(" | ",$lista)));
        Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $mensagem);
    }

    public static function jogar($objUser){
        $pontos = mt_rand (5, 9) + mt_rand (0, 99)/100;
        $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO tentativas_fome (id_usuario, pontos, extra) VALUES (:id_usuario, :pontos, 1)');
        $stmt->execute(array(':id_usuario'=>$objUser->getId(), ':pontos' => $pontos));  
        return $pontos;  
      }

    public static function avisarChat(){
        //popular url imagem no BD
        //enviar url da imagem pro json
        //buscar imagem pela url
        //procurar mais imagens
        // descobrir pq não rola 2 arquivos na mesma porta
        $file = 'dados_comida.json';
        if(Pizza::$ingrediente !== null){
            if(Language::getLanguage() == "en"){
                $text = utf8_encode(Pizza::$ingrediente["message"]);
            }else{
                $text = utf8_encode(Pizza::$ingrediente["mensagem"]);
            }
              
            file_put_contents($file, json_encode(array("comida" => Pizza::$ingrediente["descricao"],"url_imagem" => Pizza::$ingrediente["url_imagem"], "time" => date('Y-m-d H:i:s'))));
            $data = array("comida" => utf8_encode(Pizza::$ingrediente["descricao"]),"url_imagem" => Pizza::$ingrediente["url_imagem"]);
            $header = array("time" => date('Y-m-d H:i:s'), 'type'=> 'pizza');
            $mensagem = array('header' => $header, 'data' => $data);
            file_put_contents('dados_tela.json', json_encode($mensagem));
        }
        else{
            if(Language::getLanguage() == "en"){
                $desc = utf8_encode(Pizza::$receita["descricao"]);
            }else{
                $desc = utf8_encode(Pizza::$receita["descricao"]);
            }
            $text =  Mensagens::getMensagem('onNewRecipe',array(':desc'=>$desc));
        }

        if(Language::getLanguage() == "en"){
            $compl = " (Type !pizza)";
        }else{
            $compl = " (Digite !pizza)";
        }

        Pizza::$write->ircPrivmsg($_SERVER['TWITCH_CHANNEL'], $text.$compl);
    }

}