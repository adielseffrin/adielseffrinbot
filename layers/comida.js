document.addEventListener("DOMContentLoaded", function() {
    
    // variáveis globais
    const conteudo = document.querySelector('.content');

    if(typeof(EventSource) !== "undefined") {
        let source = new EventSource("http://127.0.0.1:8088/tela.php", {
            withCredentials: false // adicionando suporte a cors
        });

        source.onmessage = function(event) {
            let result = JSON.parse(event.data);
            // caso ocorra erros
            if (!result.success) {
                source.close();
                return document.body.innerHTML = `<div class="error">${result.msg}</div>`;
            }
            let dados = result.data;           

            if(dados.temImagem.toString() == 'true'){
                let img = document.createElement('img');
                img.src = dados.url_imagem;
                img.className = 'comida element-animation';
                conteudo.appendChild(img);
                var rn = (Math.floor(Math.random()*10)%4)+1;
                var audio = new Audio(`./sounds/pizza${rn}.mp3`);
                audio.play();
                setTimeout(()=> {
                    conteudo.innerHTML = ''; 
                },7500)
            }
        }
    }

   
});