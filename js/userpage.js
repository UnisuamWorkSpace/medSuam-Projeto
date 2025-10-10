$("DOMContentLoaded", () => {
    $("#linkInicio").click();
  });

$('.radioInput').on('click', (event) => {

    var id = event.target.id;
    $("section").removeClass("toggle");
    $('.hideableDiv').removeClass("hide");
    $("#pedidosMedicos, #agendarExames, #agendarVacina, #acessosMedicos").remove();

    switch (id) {
        case "linkInicio":
            $("#cards").addClass("toggle");
            break;
        case "linkExames":
            $("#exames").addClass("toggle");
            break;
        case "linkVacinas":
            $("#vacinas").addClass("toggle");
            break;
        case "linkConsultas":
          $("#consultas").addClass("toggle");
        default:
            break;
    }
});

$('.cardContainer').on('click', (event) => {
  var div = event.currentTarget;
    $('section').removeClass("toggle");
    $('#dynamicSection').addClass("toggle");
    
  switch (true) {
    case $(div).hasClass('pedidosMedicos'):
       
        $("#dynamicSection").append(`
            <div id="pedidosMedicos" class="hide toggleableDiv">
                <button id="backToInicio">
                  <i class="bi bi-arrow-left-circle"></i>
                </button>
                <h1>Pedidos Médicos</h1>
                <div class="alterPacientContainer">
                    <button type="button" id="alterPacientBtn4" class="alterPacientBtn">
                        <strong>Nome</strong>
                        <span>• Alterar</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </button>
                    <div class="pacienteContainer">
                        <h3>Selecione um paciente</h3>
                        <div class="paciente">
                            <strong>Nome Completo</strong>
                            <span>Você, idade</span>
                        </div>
                        <button type="button" class="dependenteBtn">
                            <span>+Cadastrar Menor de Idade</span>
                        </button>
                    </div>
                </div>

                <div class="centralizado">
                    <h2>Nenhum pedido médico encontrado</h2>
                    <p>No momento, você não tem pedidos médicos disponíveis.</p>
                </div>
                
            </div>`);
          
        $('.toggleableDiv').removeClass("hide");  
      break;
      case $(div).hasClass('agendarExames'):
        $("#dynamicSection").append(`
          <div id="agendarExames" class="hide toggleableDiv">
            <button id="backToInicio">
              <i class="bi bi-arrow-left-circle"></i>
            </button>
            <h1>Escolha o Paciente</h1>
            <div class="pacienteBox">
              <h2>Nome do paciente</h2>
              <span>Você, idade</span>
              <div class="arrowRightIconContainer">
                <i class="fa-solid fa-angle-right"></i>
              </div>

            </div>
            <div class="addPacienteContainer">
              <i class="bi bi-plus-lg"></i>
              <span>Adicionar Paciente</span>
            </div>
          </div>
          
          `);
         $('.toggleableDiv').removeClass("hide"); 

      break;

      case $(div).hasClass('agendarVacina'):
      $("#dynamicSection").append(`
        <div id="agendarVacina" class="hide toggleableDiv">
          <button id="backToInicio">
            <i class="bi bi-arrow-left-circle"></i>
          </button>
          <h1>Escolha o Paciente</h1>
          <div class="pacienteBox">
          <h2>Nome do paciente</h2>
          <span>Você, idade</span>
          <div class="arrowRightIconContainer">
            <i class="fa-solid fa-angle-right"></i>
          </div>
        </div>
        <div class="addPacienteContainer">
          <i class="bi bi-plus-lg"></i>
          <span>Adicionar Paciente</span>
        </div>
        </div>
      `);
      $('.toggleableDiv').removeClass("hide"); 
      
      break;

      case $(div).hasClass('acessosMedicos'): 
      $("#dynamicSection").append(`
        <div id="acessosMedicos" class="centralizado">
          <button id="backToInicio">
            <i class="bi bi-arrow-left-circle"></i>
          </button>
          <h2>Gerenciamento de autorizações</h2>
          <p>Aqui você pode gerenciar e autorizar acesso do seu histórico completo de exames a profissionais de saúde da sua escolha.</p>
          <button type="button" class="historyAcess">
            <span>Autorizar acesso ao histórico</span>
          </button>
        </div>
      
      `);
      $('.toggleableDiv').removeClass("hide"); 
      break;

      case $(div).hasClass('consultas_prescricoes_atestados'):
        $("#consultas").addClass("toggle");
      break; 
      default:
      break;
  }
  
});

$(document).on("click", "#backToInicio", () => {
  $("#pedidosMedicos, #agendarExames, #agendarVacina, #acessosMedicos").remove();
  $("#linkInicio").click();
  $("#dynamicSection").removeClass("toggle");
});

$(document).on("click", ".alterPacientBtn", function() {
  const id = $(this).attr("id");

  $(this).toggleClass("showDropdown");
});

/* $(".popUpMaker").on("click", (event) => {
  let spanContent = $(event.currentTarget).find("span").html();
  let h2 = "";

  switch (spanContent) {
    case "Agendar Exames":
        h2 = "Tipo de agendamento";
      break;
  
    default:
      break;
  }

  const $section = $("#exames");
  
  const $div = $("<div>").addClass("popup");
  const $h2 = $("<h2>").text("h2");
  const $icon = $("<i>").addClass("bi bi-x-lg");

  $div.append($h2, $icon);
  $section.append($div);
}); */

window.addEventListener("resize", () => {

if ($("#sidebar").is(":checked")  && window.innerWidth <= 980) {
    $("#sidebar").trigger("click");
}
});



  let baseFontSize = 25;   

  let maxIncrease = 5;     

  let currentIncrease = 0; 

function increaseFont() {
  if (currentIncrease < maxIncrease) {
    currentIncrease++;
    document.body.style.fontSize = (baseFontSize + currentIncrease) + "px";
    $("span:not(.h1Span)").css("font-size", (baseFontSize + currentIncrease) + "px");
    $("p").css("font-size", (baseFontSize + currentIncrease) + "px");
    $("a").css("font-size", (baseFontSize + currentIncrease) + "px");
    

  
  }
}

function decreaseFont() {
  if (currentIncrease > -5) {
    currentIncrease--;
    document.body.style.fontSize = (baseFontSize + currentIncrease) + "px";
    $("span:not(.h1Span)").css("font-size", (baseFontSize + currentIncrease) + "px");
    $("p").css("font-size", (baseFontSize + currentIncrease) + "px");
    $("a").css("font-size", (baseFontSize + currentIncrease) + "px");


  }
}

let isDark = false;

function darkMode () {
  if(isDark) {
    $('body').removeClass("dark");
    isDark = false;
  }else {
    $('body').addClass("dark");
    isDark = true;
  }
}

