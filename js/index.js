$("#menu").on("click", () => {
    if($("body").hasClass("menuAberto") || $("body").hasClass("menuAberto")) {
        $("body").removeClass("menuAberto");
        $("html").removeClass("menuAberto");

    }else {
    $("body").addClass("menuAberto");
    $("html").addClass("menuAberto");

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