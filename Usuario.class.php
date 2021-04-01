<?php
require_once "./Fome.class.php";

class Usuario{
    private $nick;
    private $id;
    private $fome;
    private $sub;

    public function __construct($nick)
    {
        $this->id = 0;
        $this->nick = $nick;
        $this->fome = new Fome();
        
    }

    public function getNick(){
      return $this->nick;
    }

    public function getId(){
      return $this->id;
    }

    public function verificarExistenciaUsuario($conn){
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE nick = :nick');
        $stmt->execute(array(':nick'=>$this->nick));
        $result = $stmt->fetch();
        
        return !empty($result); 
    }

    public function carregarUsuario($conn){
      $stmt = $conn->prepare('SELECT id, sub FROM usuarios WHERE nick = :nick');
      $stmt->execute(array(':nick'=>$this->nick));
      $result = $stmt->fetch();
      
      if(!empty($result)){
        $this->id = $result['id'];
        $this->sub = $result['sub'];
      }; 
  }

    public function cadastrarUsuario($conn){
      $lastId = 0;
      try{
        $conn->beginTransaction();
        $stmt = $conn->prepare('INSERT INTO usuarios (nick) VALUES (:nick)');
        $stmt->execute(array(':nick'=>$this->nick));
        $lastId = $conn->lastInsertId();
        $conn->commit();
      }catch(PDOExecption $e) {
        $conn->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
      $this->id = $lastId;
    }

    public function podeJogar($conn){
      return $this->fome->quantidadeJogadaHoje($this->id,$conn) <= (!!$this->sub ? 1 : 0);
    }

    public function jogar($conn){
      return $this->fome->jogar($this->id,$conn);
    }

    public function addSub($conn){
      try{
        $conn->beginTransaction();
        $stmt = $conn->prepare('UPDATE usuarios SET sub = 1 WHERE id = :id');
        $stmt->execute(array(':id'=>$this->id));
        $this->sub = 1;
        $conn->commit();
      }catch(PDOExecption $e) {
        $conn->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }

    public function removeSub($conn){
      try{
        $conn->beginTransaction();
        $stmt = $conn->prepare('UPDATE usuarios SET sub = 0 WHERE id = :id');
        $stmt->execute(array(':id'=>$this->id));
        $this->sub = 0;
        $conn->commit();
      }catch(PDOExecption $e) {
        $conn->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      } 
    }

    public function getPosition($conn){
      $data = array();
      $stmt = $conn->prepare("select count(*)+1 as posicao, round(total,2) as pontos from (select id_usuario, sum(pontos) as total from tentativas_fome where data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' group by id_usuario having total > (select sum(pontos) as tot from tentativas_fome where id_usuario = :id) order by total desc) as t;");
      $stmt->execute(array(':id'=>$this->id));
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!empty($result)){
        $data = array('position' => $result['posicao'], 'pontos' => $result['pontos']);
      };
      
      return $data;
    }

    public function getRanking($conn){
      $mensagem = "Olha o ranking dos esfomeados! ";
      $c = 1;
      $achou = false;
      try{
        $conn->beginTransaction();
        $sql = "select u.nick as nick, t.id_usuario as id_usuario, round(sum(t.pontos),2) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario where t.data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' group by t.id_usuario  order by pontos desc LIMIT 3;";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(':id'=>$this->id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result)){
          $mensagem = "Ué, ninguém jogou ainda?";
        }else{
          foreach($result as $val){
            if($this->id == $val['id_usuario']) $achou = true;
            $mensagem .= ($c++)."- ".$val['nick']." com ".$val['pontos']." pontos. ";
          }
          if($achou){
            $mensagem .= "E aí @".$this->nick." , será que você se mantém no pódio?!🥇🥈🥉";
          }else{ 
            $data = $this->getPosition($conn);
            if(!empty($data)){ 
              $mensagem .= $data['position']."- @".$this->nick." com ".$data['pontos']." pontos. ";
            }
          }
        }
        $conn->commit();
      }catch(PDOExecption $e) {
        $conn->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
      }
      return $mensagem; 
    }


}