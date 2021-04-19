<?php 
namespace AdielSeffrinBot\Models;

class Fome{
    
  public function quantidadeJogadaHoje($id, $conn){

    $stmt = $conn->prepare('SELECT count(id_usuario) AS total FROM tentativas_fome WHERE id_usuario = :id_usuario AND data_tentativa = curdate()');
    $stmt->execute(array(':id_usuario'=>$id));
    $result = $stmt->fetch();
    return $result['total']; 
  }

  public function jogar($id,$conn){
    $pontos = mt_rand (0, 9) + mt_rand (0, 99)/100;
    $stmt = $conn->prepare('INSERT INTO tentativas_fome (id_usuario, pontos) VALUES (:id_usuario, :pontos)');
    $stmt->execute(array(':id_usuario'=>$id, ':pontos' => $pontos));  
    return $pontos;  
  }
}