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