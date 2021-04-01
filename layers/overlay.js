document.addEventListener("DOMContentLoaded", function() {
    
    // variáveis globais
    const TIMER = document.querySelector('.timer');
    const TURN = document.querySelector('.turn');
    const MOB = document.querySelector('.mob');
    const CHAT = document.querySelector('.chat-box');
    const TURNOS = document.querySelector('.turnos');
    let BATALHA_LOG = null;
    let BATALHA_MOB = null;
    let KEYS_RPG = null;
    let chat = [];
    let MOB_SHOW = false;

    if(typeof(EventSource) !== "undefined") {
        let source = new EventSource("flush.php", {
            withCredentials: true // adicionando suporte a cors
        });

        source.onmessage = function(event) {
            let result = JSON.parse(event.data);

            // caso ocorra erros
            if (!result.success) {
                source.close();
                return document.body.innerHTML = `<div class="error">${result.msg}</div>`;
            }

            BATALHA_LOG = result.data.batalha_log;
            BATALHA_MOB = result.data.batalha_mob;
            KEYS_RPG = result.data.keys_rpg;

            // set config overlay
            if(KEYS_RPG.POSITION_TIMER!==undefined && KEYS_RPG.POSITION_CHAT!==undefined) { 
                TIMER.classList.add(getPosition (KEYS_RPG.POSITION_TIMER, 'timer'));
                CHAT.classList.add(getPosition (KEYS_RPG.POSITION_CHAT, 'chat'));
            }

            // verificando se o rpg está ativo
            // if (KEYS_RPG.RPG_ACTIVE === 'NAO') return false;

            // resolveOverlays();

            // verificando o turno
            if (KEYS_RPG.RPG_TURN === 'FINALIZADO') {
                // atualizando tempo do relógio (convert segundo para horas)
                TIMER.innerHTML = segParaHora(KEYS_RPG.COUNTER_MOB_EMERGE);
                
                // caso a batalha tenha sido finalizada com a derrota do mob
                if (BATALHA_MOB != null && BATALHA_MOB.vida_mob<=0) {
                    if (MOB.querySelector('.img').querySelector('img').src.indexOf(BATALHA_MOB.img_death)<=0) {
                        MOB.querySelector('.img').querySelector('img').src = './../img/' + BATALHA_MOB.img_death;
                    }
                    setTimeout(()=>{ resolveOverlays(); }, 9500);
                }else {
                    resolveOverlays();
                }
            }else{
                showTurn ();
                chatUpdate();
                mobUpdate();
                resolveOverlays();
                let counter = (KEYS_RPG.RPG_TURN === 'ALISTAMENTO') ? KEYS_RPG.COUNTER_REGISTRATION : KEYS_RPG.COUNTER_TURN;
                TIMER.innerHTML = segParaHora(counter);
            }

            // if (KEYS_RPG.RPG_TURN === 'ALISTAMENTO') {
            //     // construindo chat
            //     showTurn ();
            //     chatUpdate();
            //     mobUpdate();
            //     resolveOverlays();
            //     TIMER.innerHTML = segParaHora(KEYS_RPG.COUNTER_REGISTRATION);
            // }

            // if (KEYS_RPG.RPG_TURN === 'PLAYER') {
            //     showTurn ();
            //     chatUpdate();
            //     mobUpdate();
            //     resolveOverlays();
            //     // atualizando tempo do relógio (convert segundo para horas)
            //     TIMER.innerHTML = segParaHora(KEYS_RPG.COUNTER_TURN);
            // }

            // if (KEYS_RPG.RPG_TURN === 'MOB') {
            //     showTurn ();
            //     chatUpdate();
            //     mobUpdate();
            //     resolveOverlays();
            //     TIMER.innerHTML = segParaHora(KEYS_RPG.COUNTER_TURN);
            // }
        }
    }

    function getPosition (postion, el) {
        let pos = [
            {key: 'DIREITA SUPERIOR', value: `${el}_right_top`},
            {key: 'DIREITA CENTRO', value: `${el}_right_center`},
            {key: 'DIREITA INFERIOR', value: `${el}_right_bottom`},
            {key: 'ESQUERDA SUPERIOR', value: `${el}_left_top`},
            {key: 'ESQUERDA CENTRO', value: `${el}_left_center`},
            {key: 'ESQUERDA INFERIOR', value: `${el}_left_bottom`}
        ];

        let result = '';
        pos.forEach(e => {
            if (e.key === postion) result = e.value;
        });

        return result;
    }

    function showTurn () {
        if (KEYS_RPG.RPG_TURN!==undefined) {
            let turn_name = `turno_${KEYS_RPG.RPG_TURN.toLowerCase()}.png`;
            
            if (TURN.querySelector('img').src.indexOf(turn_name)<=0) {
                let song = (KEYS_RPG.RPG_TURN==='ALISTAMENTO') ? 'epic song.mp3' : 'turno.mp3';
                let audio = new Audio(song);
                audio.volume = KEYS_RPG.VOLUME_SONG_TURN;//0.08;
                audio.play();  

                TURN.style.transform = 'translateY(0%)';
                TURN.querySelector('img').src = `./../img/icons/${turn_name}`;
                setTimeout(()=>{
                    TURN.style.transform = 'translateY(-220%)';
                }, 8000);

                // colorindo bonequinhos do turno
                if (KEYS_RPG.RPG_TURN==='PLAYER') {
                    TURNOS.querySelector('#turno_player').classList.add('turno_ativo');
                    TURNOS.querySelector('#turno_mob').classList.remove('turno_ativo');
                    TURNOS.style.display = 'block';
                }else if (KEYS_RPG.RPG_TURN==='MOB'){
                    TURNOS.style.display = 'block';
                    TURNOS.querySelector('#turno_player').classList.remove('turno_ativo');
                    TURNOS.querySelector('#turno_mob').classList.add('turno_ativo');
                }else{
                    KEYS_RPG.RPG_TURN==='player'
                }
            }
        }
    }

    function resolveOverlays() {
        if (KEYS_RPG.RPG_TURN==='FINALIZADO') {
            // mostrando mob e chatbox
            MOB.style.display = 'none';
            CHAT.style.display = 'none';
            TIMER.style.display = 'block';
            TURN.querySelector('img').src = '';
            TURNOS.style.display = 'none';
            document.querySelector('body').style.backgroundImage = '';
            MOB_SHOW = false;
            chat = []; // limpando o chat
        }else{
            // mostrando mob e chatbox
            MOB.style.display = 'block';
            CHAT.style.display = 'block';
            if (KEYS_RPG.SHOW_BACKGROUND==='SIM') document.querySelector('body').style.backgroundImage = "url('./../img/fundo_rpg.png')";
            if (KEYS_RPG.SHOW_TIMER_TURN==='NAO') TIMER.style.display = 'none';
        }
    }

    function mobUpdate () {
        if (!MOB_SHOW) {
            document.querySelector('.nome').innerHTML = BATALHA_MOB.nome;
            document.querySelector('.img').querySelector('img').src = './../img/' + BATALHA_MOB.img;
            MOB_SHOW = true;
        }
        document.querySelector('.level').innerHTML = 'LVL ' + ((BATALHA_MOB.level_mob<=0) ? '???' : BATALHA_MOB.level_mob);
        // mob levando hit
        if (Number(document.querySelector('.vida_indicador').innerHTML)<BATALHA_MOB.vida_mob &&
        KEYS_RPG.RPG_TURN !== 'ALISTAMENTO') {
            document.querySelector('.img').querySelector('img').src = './../img/' + BATALHA_MOB.img_hit;
            setTimeout(()=>{
                document.querySelector('.img').querySelector('img').src = './../img/' + BATALHA_MOB.img;
            }, 1000);
        }
        document.querySelector('.vida_indicador').innerHTML = BATALHA_MOB.vida_mob;
        document.querySelector('.barra_verde').style.width = BATALHA_MOB.vida_mob + '%';
    }

    function chatUpdate () {
        BATALHA_LOG.forEach((e)=>{
            setTimeout(()=>{
                let f = false;
                chat.forEach(c => {
                    if (Number(c.id) === Number(e.id)) f = true;
                });
                if (!f) {
                    let li = document.createElement('li');
                    li.id = e.id;
                    li.style.transform = 'translateX(103%)';
                    li.innerHTML = `<div class="chat-left">${e.log.texto1}</div>
                    <div class="chat-center">
                        <img src="./../img/icons/${getAcaoIcon(e.log.acao)}">
                    </div>
                    <div class="chat-right">${e.log.texto2}</div>
                    <div class="chat-valor">${e.log.valor}</div>`;
                    chat.push(li);

                    CHAT.querySelector('ul').appendChild(li);
                    setTimeout(()=>{ 
                        li.style.transform = 'translateX(0)'
                        setTimeout(()=>{
                            CHAT.scrollTo({top:  CHAT.offsetHeight, behavior: 'smooth'})
                            setTimeout(()=>{
                                li.style.transform = 'translateX(103%)';
                                setTimeout(()=>{ li.parentElement.removeChild(li) }, 500);
                            }, KEYS_RPG.SHOW_CHAT_TIME);
                        }, 500);
                    }, 300);
                }
            }, 500);
        });
    }

    function getAcaoIcon (acao) {
        const ACTION_ICONS = [
            {action: 'surgir_procrastinador', 'icon': 'procrastinador.png'},
            {action: 'surgir_sonolento', 'icon': 'sonolento.png'},
            {action: 'alistar', 'icon': 'alistar.png'},
            {action: 'ataque', 'icon': 'ataque.png'},
            {action: 'ataque_critico', 'icon': 'ataque_critico.png'},
            {action: 'ataque_magico', 'icon': 'ataque_magico.png'},
            {action: 'finalizacao', 'icon': 'finalizacao.png'},
            {action: 'revive', 'icon': 'revive.png'},
            {action: 'regenera', 'icon': 'regenera.png'},
            {action: 'subiu_level', 'icon': 'subiu_level.png'},
            {action: 'esquiva', 'icon': 'esquiva.png'}

        ];

        let icon = '';
        ACTION_ICONS.forEach(e => {
            if (e.action===acao) {
                icon = e.icon;
            }
        });
        return icon;
    }

    function segParaHora(time, with_seg = true){
        
        var hours = Math.floor( time / 3600 );
        var minutes = Math.floor( (time % 3600) / 60 );
        var seconds = time % 60;
        
        minutes = minutes < 10 ? '0' + minutes : minutes;      
        seconds = seconds < 10 ? '0' + seconds : seconds;
        hours = hours < 10 ? '0' + hours : hours;
        
        if(with_seg){
        return  hours + ":" + minutes + ":" + seconds;
        }
        
        return  hours + ":" + minutes;
    }
});