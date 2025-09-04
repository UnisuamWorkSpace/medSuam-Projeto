$("#menu").on("click", () => {
    if($("body").hasClass("menuAberto") || $("body").hasClass("menuAberto")) {
        $("body").removeClass("menuAberto");
        $("html").removeClass("menuAberto");

    }else {
    $("body").addClass("menuAberto");
    $("html").addClass("menuAberto");

    }
}); 