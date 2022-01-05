<?php
    session_start();
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header("Access-Control-Allow-Origin: *");

    $response = array("success"=>true, "msg"=>"", "data"=>"");
    $tempoGlobal = "";
    set_time_limit(500); // 3 minutos

    // variável de comparação
    // utilizada para verificar alterações de estado nos dados
    $ultimaExibicao = null;
    // laço onde acontece a vida útil
    while (true) {

        $result = @file_get_contents('../dados_tela.json');
        $mensagem = json_decode($result, true);
        if($mensagem != ""){        

            if (empty($dados)) $dados = array(); // se a dados estiver nula
            //verificar se tem header
            $header = $mensagem['header'];
            $dados = $mensagem['data'];
            $data = $header["time"];
            $tipo = $header['type'];
            $interval = 0;
            $executar = false;
            
            
            switch($tipo){
                case 'ranking':
                    if($data != null){
                        if($ultimaExibicao == null){
                            $ultimaExibicao = date_create($data);
                            $executar = true;
                        }else{
                            $interval = date_diff($ultimaExibicao, date_create($data))->format('%i');
                            if($interval >= 1){
                                $ultimaExibicao = date_create($data);
                                $executar = true;
                            }
                        }
                    }
                break;
                case 'pizza':
                    @file_put_contents('../dados_tela.json', '');
                    $imagem = $dados["url_imagem"] ?? null;
                    $dados["temImagem"] = file_exists($imagem);
                    $executar = true;
                break;
                case 'banana':
                    @file_put_contents('../dados_tela.json', '');
                    $img_number = rand(1,7);
                    $dados["url_imagem"] = 'banana_'.$img_number.'.jpeg';
                    
                    $executar = true;
                    break;
            }
            
            if ($executar) {
                
                $executar = false;
                // adicionado a response as informações
                $response['success'] = true;
                $response['msg'] = "Tudo certo";
                $response['data'] = array('header' => $header, 'data' => $dados);

                // devolvendo para o js a resposta do event source
                echo "data: " . json_encode($response) . "\n\n";
                set_time_limit(500);
            }

            ob_flush();
            flush();
            sleep(1);
        }
    }
?>