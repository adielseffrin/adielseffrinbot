<?php
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    $response = array("success"=>true, "msg"=>"", "data"=>"");

    set_time_limit(180); // 3 minuto

    // require_once __DIR__ . "/../gerenciamento/api/src/model/rpg/rpg_dao.php";
    // require_once __DIR__ . "/../gerenciamento/api/src/model/batalha_mob/batalha_mob_dao.php";
    // require_once __DIR__ . "/../gerenciamento/api/src/model/batalha_log/batalha_log_dao.php";

    // codigo que controla a carta do mob no frontend do rpg
    // if (!file_exists(__DIR__ .'/../DB_2')) {
    //     $response['success'] = false;
    //     $response['msg'] = "** OPA! Você precisa modificar o diretório DB_2_mock para DB_2 para ativar o banco de dados! **";
    //     die ("data: ".json_encode($response)."\n\n");
    // }

    // $rpg_dao = new rpg_dao();
    // $batalha_mob_dao = new batalha_mob_dao();
    // $batalha_log_dao = new batalha_log_dao();

    // variável de comparação
    // utilizada para verificar alterações de estado nos dados
    $temp = null;

    // laço onde acontece a vida útil
    while (true) {

        $data = array(
            "oi" => "ola"
        );

        // montando a string de teste que compara alterações de dados
        $test = json_encode($data);

        if ($temp !== $test) {
            // passando data para a controladora temp
            $temp = $test;

            // adicionado a response as informações
            $response['success'] = true;
            $response['msg'] = "Tudo certo";
            $response['data'] = $data;

            // devolvendo para o js a resposta do event source
            echo "data: " . json_encode($response) . "\n\n";
        }

        ob_flush();
        flush();
        sleep(1);
    }
?>