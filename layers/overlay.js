document.addEventListener("DOMContentLoaded", function() {
    
    // variáveis globais
    const RANKING = document.querySelector('.ranking');
    //document.querySelector('.ranking').style.visibility = "hidden";

    if(typeof(EventSource) !== "undefined") {
        let source = new EventSource("http://127.0.0.1:8088/flush.php", {
            withCredentials: false // adicionando suporte a cors
        });
        
        source.onmessage = function(event) {
            let result = JSON.parse(event.data);
            console.log(result);
            // caso ocorra erros
            if (!result.success) {
                source.close();
                return document.body.innerHTML = `<div class="error">${result.msg}</div>`;
            }
            let dados = result.data;           

            const places = Array.from(document.querySelectorAll("[data-place]"));

            const users = [
            {
                name: dados[0].nick,
                points: dados[0].pontos
            },
            {
                name: dados[1].nick,
                points: dados[1].pontos
            },
            {
                name: dados[2].nick,
                points: dados[2].pontos
            }
            ];

            places.forEach((element) => {
            let index = parseInt(element.dataset.place) - 1;
            element.innerHTML = `<span>pts. ${users[index].points}</span> ${users[index].name}`;
            });
            
            
            document.querySelector('.ranking').style.visibility = "visible";
            setTimeout(function(){document.querySelector('.ranking').style.visibility = "hidden"},10000)

        }
    }

   
});