<?php
namespace AdielSeffrinBot\Models;
use AdielSeffrinBot\Models\Fome;
session_start();


class Usuario{
    private $nick;
    private $id;
    private $fome;
    private $sub;
    private $streamer;
    private $twitchId;

    static $ranking = array();
    static $ultimaExibicao = null;

    public function __construct($nick)
    {
        $this->id = 0;
        $this->nick = $nick;
        $this->fome = new Fome();

    }

    public function setTwitchId($tid){
      $this->twitchId = $tid;
    }

    public function getNick(){
      return $this->nick;
    }

    public function getId(){
      return $this->id;
    }

    public function getTwitchId(){
      return $this->twitchId;
    }

    public function verificarExistenciaUsuario(){
        $stmt = ConexaoBD::getInstance()->prepare('SELECT id FROM usuarios WHERE nick = :nick');
        $stmt->execute(array(':nick'=>$this->nick));
        $result = $stmt->fetch();
        
        return !empty($result); 
    }

    public function carregarUsuario(){
      $stmt = ConexaoBD::getInstance()->prepare('SELECT id, sub, twitch_id FROM usuarios WHERE nick = :nick');
      $stmt->execute(array(':nick'=>$this->nick));
      $result = $stmt->fetch();
      if(!empty($result)){
        $this->id = $result['id'];
        $this->sub = $result['sub'];
        $this->twitchId = $result['twitch_id'];
      }; 
  }

    public function cadastrarUsuario(){
      $lastId = 0;
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO usuarios (nick) VALUES (:nick)');
        $stmt->execute(array(':nick'=>$this->nick));
        $lastId = ConexaoBD::getInstance()->lastInsertId();
        ConexaoBD::getInstance()->commit();
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
      $this->id = $lastId;
    }

    public function atualizaTwitchId(){
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $sql = "UPDATE usuarios SET twitch_id = :twitch_id WHERE id = :id";
        $stmt = ConexaoBD::getInstance()->prepare($sql);
       
        $stmt->execute(array(':id'=>$this->id, ':twitch_id'=>$this->twitchId));
        ConexaoBD::getInstance()->commit();
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }

    public function podeJogar(){
      return $this->fome->podeJogar($this->id, $this->sub);
    }

    public function jogar(){
      return $this->fome->jogar($this);
    }

    public function addFome($quantidade){
      return $this->fome->addFome($this->id, $quantidade);
    }

    public function addFomeComprada($quantidade){
      return $this->fome->addFomeComprada($this->id, $quantidade);
    }

    public function addSub(){
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE usuarios SET sub = 1 WHERE id = :id');
        $stmt->execute(array(':id'=>$this->id));
        $this->sub = 1;
        ConexaoBD::getInstance()->commit();
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }

    public function addStreamer(){
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE usuarios SET streamer = 1 WHERE id = :id');
        $stmt->execute(array(':id'=>$this->id));
        $this->sub = 1;
        ConexaoBD::getInstance()->commit();
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }

    public function removeSub(){
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE usuarios SET sub = 0 WHERE id = :id');
        $stmt->execute(array(':id'=>$this->id));
        $this->sub = 0;
        ConexaoBD::getInstance()->commit();
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }

    public function removeStreamer(){
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE usuarios SET streamer = 0 WHERE id = :id');
        $stmt->execute(array(':id'=>$this->id));
        $this->sub = 1;
        ConexaoBD::getInstance()->commit();
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }

    public function getPosition(){
      $data = array();
      $stmt = ConexaoBD::getInstance()->prepare("select count(*)+1 as posicao, round(total,2) as pontos from (select id_usuario, sum(pontos) as total from tentativas_fome where data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' group by id_usuario having total > (select sum(pontos) as tot from tentativas_fome where id_usuario = :id) order by total desc) as t;");
      $stmt->execute(array(':id'=>$this->id));
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!empty($result)){
        $data = array('position' => $result['posicao'], 'pontos' => $result['pontos']);
      };
      
      return $data;
    }

    public function getRanking(){
      if(empty(Usuario::$ranking)){
        $mensagem = "Olha o ranking dos esfomeados! ";
        $dadosArray = array();
        $c = 1;
        $pos = 0;
        try{
          ConexaoBD::getInstance()->beginTransaction();
          $sql = "select 0 as posicao, u.nick as nick, t.id_usuario as id_usuario, round(sum(t.pontos),2) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario where t.data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' group by t.id_usuario  order by pontos desc;";
          $stmt = ConexaoBD::getInstance()->prepare($sql);
          $stmt->execute(array(':id'=>$this->id));
          $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
          if(empty($result)){
            $mensagem = "Ué, ninguém jogou ainda?";
          }else{
            foreach($result as $key => $val){
              $result[$key]['posicao'] = $c++;
            }
            for($i = 0; $i< 3; ++$i){
              if($this->id == $result[$i]['id_usuario']) $pos = $result[$i]['posicao'];
              $mensagem .= $result[$i]['posicao']."- ".$result[$i]['nick']." com ".$result[$i]['pontos']." pontos. ";
              array_push($dadosArray, array('posicao'=>$result[$i]['posicao'], 'nick' => $result[$i]['nick'], 'pontos' => $result[$i]['pontos']));
            }
            $index = array_search($this->id,array_column($result, 'id_usuario'));
            if(!!$index){
              if($result[$index]['posicao'] <= 3){
                $mensagem .= "E aí @".$this->nick." , será que você se mantém no pódio?!🥇🥈🥉";
              }else{ 
                if($this->nick != "adielseffrin"){ 
                  $mensagem .= $result[$index]['posicao']."- @".$this->nick." com ".$result[$index]['pontos']." pontos. ";
                }
              }
            }
          }
          
          $executar = false;
          if(!empty($dadosArray)){
            $data = date('Y-m-d H:i:s');
            if(self::$ultimaExibicao == null ){
              self::$ultimaExibicao = date_create($data);
              $executar = true;
            }else{
              $interval = date_diff(self::$ultimaExibicao, date_create($data));
              $minutos = $interval->format('%i');
              $segundos = $interval->format('%s');
              if($minutos >= 1){
                self::$ultimaExibicao = date_create($data);
                $executar = true;
              }
            }
            if($executar){
              $header = array('time' => $data, 'type' => 'ranking');
              $mensagemParaTela = array('header' => $header, 'data' => $dadosArray);
              //array_push($dadosArray,array('time' => $data));
              $file = 'dados_ranking.json';
              file_put_contents($file, json_encode($dadosArray));
              file_put_contents('dados_tela.json', json_encode($mensagemParaTela));
            }else{
              $mensagem .= "Ainda falta ".(60-$segundos)." segundos para exibir em tela.";
            }
          }
          ConexaoBD::getInstance()->commit();
        }catch(PDOExecption $e) {
          ConexaoBD::getInstance()->rollback();
          print "Error!: " . $e->getMessage() . "</br>";
        }
      }
      return $mensagem; 
    }

    public function rename($newNick){
      $status = false;
      try{
        ConexaoBD::getInstance()->beginTransaction();
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE usuarios SET nick = :nick WHERE id = :id');
        $stmt->execute(array(':id'=>$this->id, ':nick'=>$newNick));
        ConexaoBD::getInstance()->commit();
        $status = true;
      }catch(PDOExecption $e) {
        ConexaoBD::getInstance()->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
      return $status;
    }


}