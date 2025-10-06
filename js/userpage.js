$("DOMContentLoaded", () => {
    $("#linkInicio").click();
  });

$('.radioInput').on('click', (event) => {

    var id = event.target.id;
    $("section").removeClass("toggle");

    switch (id) {
        case "linkInicio":
            $("#cards").addClass("toggle");
            break;
        case "linkExames":
            $("#exames").addClass("toggle");
            break;
    
        default:
            break;
    }
});

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

$("#alterPacientBtn").on("click", (event) => {
  let id = event.currentTarget.id;

  if($(`#${id}`).hasClass("showDropdown")){
    $(`#${id}`).removeClass("showDropdown");

  }else {
  $(`#${id}`).addClass("showDropdown");
  }
  console.log(id);
});