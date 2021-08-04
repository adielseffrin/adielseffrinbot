document.addEventListener("DOMContentLoaded", function() {
    
  // variáveis globais
  const conteudoPizza = document.querySelector('.content-pizza');
  const conteudoRanking = document.querySelector('.ranking');
  const conteudoDebug = document.querySelector('.debug');

  if(typeof(EventSource) !== "undefined") {
    let source = new EventSource("http://127.0.0.1:8090/tela.php", {
        withCredentials: false // adicionando suporte a cors
    });

    source.onmessage = function(event) {
      let result = JSON.parse(event.data);
      // caso ocorra erros
      if (!result.success) {
          source.close();
          return document.body.innerHTML = `<div class="error">${result.msg}</div>`;
      }
      let dadosAll = result.data;           
      let header = dadosAll.header;
      let dados = dadosAll.data;
      //conteudoDebug.innerHTML = event.data;
      //aqui começa a palhaçada
      switch(header.type){
        case 'pizza':
          if(dados.temImagem.toString() == 'true'){
            let img = document.createElement('img');
            img.src = dados.url_imagem;
            img.className = 'comida element-animation';
            conteudoPizza.appendChild(img);
            var rn = (Math.floor(Math.random()*10)%4)+1;
            var audio = new Audio(`./sounds/pizza${rn}.mp3`);
            audio.play();
            setTimeout(()=> {
              conteudoPizza.innerHTML = ''; 
            },7500)
          }
        break;
        case 'ranking':
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
          
          
          conteudoRanking.style.visibility = "visible";
          setTimeout(function(){conteudoRanking.style.visibility = "hidden"},10000)
        break;
      }
    }
  }

   
});