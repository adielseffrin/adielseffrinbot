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


  public function jogar($id){
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
        $stmt->execute(array(':id_usuario'=>$id)); 
        $this->jogadasExtras--;
      }else{
        $tipoJogada = 1;
        $stmt = ConexaoBD::getInstance()->prepare('UPDATE tentativas_fome_comprada SET quantidade = quantidade - 1 WHERE id_usuario = :id_usuario');
        $stmt->execute(array(':id_usuario'=>$id)); 
        $this->jogadasCompradas--;
        $ehComprada = true;
      }
      if($ehComprada){
        $pontos = mt_rand (7, 12) + mt_rand (0, 99)/100;
      }else{
        $pontos = mt_rand (0, 9) + mt_rand (0, 99)/100;
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
    echo "========================".PHP_EOL;
    var_dump($quantidade);
    echo "========================".PHP_EOL;
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