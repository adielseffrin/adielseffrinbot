<?php
    session_start();
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header("Access-Control-Allow-Origin: *");
    //header('Access-Control-Allow-Credentials: false');

    require_once '../config.php';
    require_once "../ConexaoBD.class.php";

    //$config = new Config();
    //$BD = new ConexaoBD($config->getUserBD(),$config->getSenhaBD());
    //$BD->connect();
    //$conn = $BD->getConn();

    $response = array("success"=>true, "msg"=>"", "data"=>"");
    $tempoGlobal = "";
    set_time_limit(180); // 3 minutos

    // variável de comparação
    // utilizada para verificar alterações de estado nos dados
    $ultimaExibicao = null;

    // laço onde acontece a vida útil
    while (true) {

        $result = @file_get_contents('../dados_ranking.json');
        $dados = json_decode($result, true);
        if (empty($dados)) $dados = array(); // se a dados estiver nula
        $data = isset($dados[3]["time"]) ? $dados[3]["time"] : null;
        $interval = 0;
        $executar = false;
        
        if($data != null){
            if($ultimaExibicao == null){
                $ultimaExibicao = date_create($data);
                $executar = true;
            }else{
                $interval = date_diff($ultimaExibicao, date_create($data))->format('%s');
                if($interval > 60){
                    $ultimaExibicao = date_create($data);
                    $executar = true;
                }
            }
        }
        // montando a string de teste que compara alterações de dados
        $test = json_encode($data);
        
        if ($executar) {
            $executar = false;
            // adicionado a response as informações
            $response['success'] = true;
            $response['msg'] = "Tudo certo";
            $response['data'] = $dados;

            // devolvendo para o js a resposta do event source
            echo "data: " . json_encode($response) . "\n\n";
        }

        ob_flush();
        flush();
        sleep(1);
    }
?>