<?php
use AdielSeffrinBot\Models\ConexaoBD;

namespace AdielSeffrinBot\Models;

use AdielSeffrinBot\Models\Language;

class Records{
    public $records = array();

    public function getRecords(){
        $sql = "
        (select 'dayMax' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa = '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199) 
        and t.pontos = (select max(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa = '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos desc limit 1) 
        order by pontos desc)
        UNION
        (select 'dayMin' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa = '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199)
        and t.pontos = (select min(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa = '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos asc limit 1) 
        order by pontos asc)
        UNION
        (select 'monthMax' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' and t.pontos > 0 and u.id not in (10,199)
        and t.pontos = (select max(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos desc limit 1) 
        order by pontos desc)
        UNION
        (select 'monthMin' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' and t.pontos > 0 and u.id not in (10,199) 
        and t.pontos = (select min(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-m-01')."' and '".date('Y-m-t')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos asc limit 1) 
        order by pontos asc)
        UNION
        (select 'yearMax' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-01-01')."' and '".date('Y-12-31')."' and t.pontos > 0 and u.id not in (10,199) 
        and t.pontos = (select max(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-01-01')."' and '".date('Y-12-31')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos desc limit 1) 
        order by pontos desc)
        UNION
        (select 'yearMin' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-01-01')."' and '".date('Y-12-31')."' and t.pontos > 0 and u.id not in (10,199) 
        and t.pontos = (select min(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '".date('Y-01-01')."' and '".date('Y-12-31')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos asc limit 1) 
        order by pontos asc)
        UNION
        (select 'allMax' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '2021-01-01' and '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199) 
        and t.pontos = (select max(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '2021-01-01' and '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos desc limit 1)
        order by pontos desc)
        UNION
        (select 'allMin' as type, u.nick as nick, t.pontos as pontos, t.data_tentativa from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where  t.data_tentativa between '2021-01-01' and '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199) 
        and t.pontos = (select min(t.pontos) as pontos from tentativas_fome as t inner join usuarios as u on u.id = t.id_usuario 
        where t.data_tentativa between '2021-01-01' and '".date('Y-m-d')."' and t.pontos > 0 and u.id not in (10,199) and extra = 0 order by pontos asc limit 1) 
        order by pontos asc);
        ";
        $stmt = ConexaoBD::getInstance()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($result as $key => $val){
            $user = new UsuarioRecord($val['nick'], $val['data_tentativa']);
            if(in_array($val['type'], $this->records)){
                $index = array_search($val['type'], $this->records);
                $record = $this->records[$index];
                $record->addUser($user);
            }else{
                $record = new Record($val['type'], $val['pontos']);
                $record->addUser($user);
                array_push($this->records, $record);
            }
            
        }

        return $this->recordsFormat();
    }
    
    public function recordsFormat(){
        $texto = array();
        $diarioMax = array_filter($this->records, function($r){return $r->category == 'dayMax';});
        $diarioMin = array_filter($this->records, function($r){return $r->category == 'dayMin';});
        $mensalMax = array_filter($this->records, function($r){return $r->category == 'monthMax';});
        $mensalMin = array_filter($this->records, function($r){return $r->category == 'monthMin';});
        $anualMax = array_filter($this->records, function($r){return $r->category == 'yearMax';});
        $anualMin = array_filter($this->records, function($r){return $r->category == 'yearMin';});
        $geralMax = array_filter($this->records, function($r){return $r->category == 'allMax';});
        $geralMin = array_filter($this->records, function($r){return $r->category == 'allMin';});

        $texto['dia'] = "Recordes de hoje:";
        
        $texto['dia']  .= " # Tipo mais pontos (".array_values($diarioMax)[0]->pontos."):";
        foreach(array_values($diarioMax)[0]->usuarios as $r){
            $texto['dia'] .= " {$r->nick} em {$r->data_tentativa}";
        };
        
        $texto['dia']  .= " # Tipo menos pontos (".array_values($diarioMin)[0]->pontos."):";
        foreach(array_values($diarioMin)[0]->usuarios as $r){
            $texto['dia'] .= " {$r->nick} em {$r->data_tentativa}";
        };

        $texto['mes'] = "Recordes do mês:";
        $texto['mes']  .= " # Tipo mais pontos (".array_values($mensalMax)[0]->pontos."):";
        foreach(array_values($mensalMax)[0]->usuarios as $r){
            $texto['mes'] .= " {$r->nick} em {$r->data_tentativa}";
        };
        $texto['mes']  .= " # Tipo menos pontos (".array_values($mensalMin)[0]->pontos."):";
        foreach(array_values($mensalMin)[0]->usuarios as $r){
            $texto['mes'] .= " {$r->nick} em {$r->data_tentativa}";
        };

        $texto['ano'] = " Recordes do ano:";
        $texto['ano'] .= " # Tipo mais pontos (".array_values($anualMax)[0]->pontos."):";
        foreach(array_values($anualMax)[0]->usuarios as $r){
            $texto['ano'] .= " {$r->nick} em {$r->data_tentativa}";
        };
        $texto['ano'] .= " # Tipo menos pontos (".array_values($anualMin)[0]->pontos."):";
        foreach(array_values($anualMax)[0]->usuarios as $r){
            $texto['ano'] .= " {$r->nick} em {$r->data_tentativa}";
        };
        
        $texto['all'] = " Recordes de todos os tempos:";
        $texto['all'] .= " # Tipo mais pontos (".array_values($geralMax)[0]->pontos."):";
        foreach(array_values($geralMax)[0]->usuarios as $r){
            $texto['all'] .= " {$r->nick} em {$r->data_tentativa}";
        };
        $texto['all'] .= " # Tipo menos pontos (".array_values($geralMin)[0]->pontos."):";
        
        foreach(array_values($geralMin)[0]->usuarios as $r){
            $texto['all'] .= " {$r->nick} em {$r->data_tentativa}";
        };

        return $texto;
    }
}
    
class Record{
    public $category;
    public $pontos;
    public $usuarios = array();

    public function __construct($c, $p){
        $this->category = $c;
        $this->pontos = $p;
    }

    public function addUser($u){
        array_push($this->usuarios, $u);
    }
}

class UsuarioRecord{
    public $nick;
    public $data_tentativa;

    public function __construct($n, $d){
        $this->nick = $n;
        $this->data_tentativa = $d;
    }
}