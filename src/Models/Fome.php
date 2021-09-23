<?php 
namespace AdielSeffrinBot\Models;

class Fome{
   
  private $jogadasHoje;
  private $jogadasExtras;
  private $temExtra;
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
      $this->jogadasExtras = !!$result ? $result['quantidade'] : 0; 
    }
    return $this->jogadasExtras; 
  }

  public function podeJogar($id, $sub){
    $this->temExtra = $this->quantidadeExtra($id) > 0;
    $this->podeHoje = $this->quantidadeJogadaHoje($id) <= (!!$sub ? 1 : 0);
    return $this->temExtra || $this->podeHoje;
  }


  public function jogar($id){
    $pontos = 0;
    try{
      $pontos = mt_rand (0, 9) + mt_rand (0, 99)/100;
      $temExtra = $this->jogadasExtras > 0;
      $tipoJogada = 0;
      ConexaoBD::getInstance()->beginTransaction();
      if(!$this->podeHoje){
        $tipoJogada = 1;
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE tentativas_fome_extras SET quantidade = quantidade - 1 WHERE id_usuario = :id_usuario');
        $stmt->execute(array(':id_usuario'=>$id)); 
        $this->jogadasExtras--;
      }else{
        $this->jogadasHoje++;
      }
      $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO tentativas_fome (id_usuario, pontos, extra) VALUES (:id_usuario, :pontos, :extra)');
      $stmt->execute(array(':id_usuario'=>$id, ':pontos' => $pontos, ':extra' => $tipoJogada));  
      ConexaoBD::getInstance()->commit();
    }catch(PDOExecption $e) {
      ConexaoBD::getInstance()->rollback();
      print "Error!: " . $e->getMessage() . "</br>";
    } 
    return $pontos;  
  }

  public function addFome($id, $quantidade){
    $this->quantidadeExtra($id);
    if(!$this->temExtra){
      $stmt = ConexaoBD::getInstance()->prepare('INSERT INTO tentativas_fome_extras (id_usuario, quantidade) VALUES (:id_usuario, :quantidade)');
      $resultado = $stmt->execute(array(':id_usuario'=>$id, ':quantidade' => $quantidade));  
    }else{
      $stmt = ConexaoBD::getInstance()->prepare('UPDATE tentativas_fome_extras SET quantidade = quantidade + :quantidade WHERE id_usuario = :id_usuario');
      $resultado = $stmt->execute(array(':id_usuario'=>$id, ':quantidade' => $quantidade));  
    }
    return $resultado;
  }
}