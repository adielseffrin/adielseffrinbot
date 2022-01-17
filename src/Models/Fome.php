<?php 
namespace AdielSeffrinBot\Models;

class Fome{
   
  private $jogadasHoje;
  private $jogadasCompradas;
  private $temComprada;
  private $temRegistroComprada;
  private $jogadasExtras;
  private $temExtra;
  private $temRegistroExtra;
  private $podeHoje;

  public function quantidadeJogadaHoje($id){
    if(!isset($this->jogadasHoje)){
      $stmt = ConexaoBD::getInstance()->prepare('SELECT count(id_usuario) AS total FROM tentativas_fome WHERE id_usuario = :id_usuario AND data_tentativa = curdate() AND extra = 0');
      $stmt->execute(array(':id_usuario'=>$id));
      $result = $stmt->fetch();
      $this->jogadasHoje = $result['total'];
    }
    return $this->jogadasHoje; 
  }
 
  public function quantidadeExtra($id){
    if(!isset($this->jogadasExtras)){
      $stmt = ConexaoBD::getInstance()->prepare('SELECT quantidade FROM tentativas_fome_extras WHERE id_usuario = :id_usuario');
      $stmt->execute(array(':id_usuario'=>$id));
      $result = $stmt->fetch();
      $this->temRegistroExtra = !!$result;
      $this->jogadasExtras = !!$result ? $result['quantidade'] : 0; 
    }
    return $this->jogadasExtras; 
  }

  public function quantidadeComprada($id){
    if(!isset($this->jogadasCompradas)){
      $stmt = ConexaoBD::getInstance()->prepare('SELECT quantidade FROM tentativas_fome_comprada WHERE id_usuario = :id_usuario');
      $stmt->execute(array(':id_usuario'=>$id));
      $result = $stmt->fetch();
      $this->temRegistroComprada = !!$result;
      $this->jogadasCompradas = !!$result ? $result['quantidade'] : 0; 
    }
    return $this->jogadasCompradas; 
  }

  public function podeJogar($id, $sub){
    $this->temExtra = $this->quantidadeExtra($id) > 0;
    $this->temComprada = $this->quantidadeComprada($id) > 0;
    $this->podeHoje = $this->quantidadeJogadaHoje($id) <= (!!$sub ? 1 : 0);
    return $this->temComprada || $this->temExtra || $this->podeHoje;
  }


  public function jogar($user){
    $pontos = 0;
    $ehComprada = false;
    try{
     
      $temExtra = $this->jogadasExtras > 0;
      $tipoJogada = 0;
      ConexaoBD::getInstance()->beginTransaction();
      if($this->podeHoje){
        $this->jogadasHoje++;
      }elseif($this->temExtra){
        $tipoJogada = 1;
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE tentativas_fome_extras SET quantidade = quantidade - 1 WHERE id_usuario = :id_usuario');
        $stmt->execute(array(':id_usuario'=>$user->getId())); 
        $this->jogadasExtras--;
      }else{
        $tipoJogada = 1;
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE tentativas_fome_comprada SET quantidade = quantidade - 1 WHERE id_usuario = :id_usuario');
        $stmt->execute(array(':id_usuario'=>$user->getId())); 
        $this->jogadasCompradas--;
        $ehComprada = true;
      }
      
      if($ehComprada){
        $max_fome = intval(Configs::getConfig('fome_comprada_max'));
        $pontos = mt_rand (7, $max_fome) + mt_rand (0, 99)/100;
        if($pontos == $max_fome + 0.99){
          Configs::setConfig('fome_comprada_max',$max_fome+1);
        }
      }else{
        $max_fome = intval(Configs::getConfig('fome_regular_max'));
        $pontos = mt_rand (0, $max_fome) + mt_rand (0, 99)/100;
        if($pontos == $max_fome + 0.99){
          Configs::setConfig('fome_regular_max',$max_fome+1);
          Configs::setConfig('fome_comprada_max',$max_fome+4);
        }
      }

      $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO tentativas_fome (id_usuario, pontos, extra) VALUES (:id_usuario, :pontos, :extra)');
      $stmt->execute(array(':id_usuario'=>$user->getId(), ':pontos' => $pontos, ':extra' => $tipoJogada));  
      ConexaoBD::getInstance()->commit();
      $body = array(
        "info" => array(
          "pontos" => $pontos
        )
      );
      if($_SERVER['USE_API'] == 'true'){
        $request = new Request();
        $data = $request->httpPost("https://api.adielseffr.in/pizza/notificate",$body,null,array("twitch_id"=>$user->getTwitchId()));
      }
    }catch(PDOExecption $e) {
      ConexaoBD::getInstance()->rollback();
      print "Error!: " . $e->getMessage() . "</br>";
    } 
    return $pontos;  
  }

  public function addFome($id, $quantidade){
    $this->quantidadeExtra($id);
    if(!$this->temRegistroExtra){
      $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO tentativas_fome_extras (id_usuario, quantidade) VALUES (:id_usuario, :quantidade)');
      $resultado = $stmt->execute(array(':id_usuario'=>$id, ':quantidade' => $quantidade));  
    }else{
      $stmt = ConexaoBD::getInstance()->prepare('UPDATE tentativas_fome_extras SET quantidade = quantidade + :quantidade WHERE id_usuario = :id_usuario');
      $resultado = $stmt->execute(array(':id_usuario'=>$id, ':quantidade' => $quantidade));  
    }
    $this->jogadasExtras += $quantidade;
    return $resultado;
  }

  public function addFomeComprada($id, $quantidade){
    $this->quantidadeExtra($id);
    if(!$this->temRegistroComprada){
      $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO tentativas_fome_comprada (id_usuario, quantidade) VALUES (:id_usuario, :quantidade)');
      $resultado = $stmt->execute(array(':id_usuario'=>$id, ':quantidade' => $quantidade));  
    }else{
      $stmt = ConexaoBD::getInstance()->prepare('UPDATE tentativas_fome_comprada SET quantidade = quantidade + :quantidade WHERE id_usuario = :id_usuario');
      $resultado = $stmt->execute(array(':id_usuario'=>$id, ':quantidade' => $quantidade));  
    }
    $this->jogadasExtras += $quantidade;
    return $resultado;
  }
}