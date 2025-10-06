function mostrarSenha (id, imagem)  {   

    var inputType = $('#' + id)[0];
    var eye = $('#' + imagem)[0];
    console.log(eye.src);
    if(inputType.type === "password") {
        inputType.type = "text";
        eye.src = "./images/eye.svg";
    }else {
       inputType.type = "password"; 
       eye.src = "./images/eye-slash.svg";
    }

}

const form = $('#autenticacaoForm');

form.on("submit", (event) => {
    event.preventDefault();

    const btn = $('#enviarBtn');
    btn.addClass("enviarClick");

    setTimeout(() => {
        btn.removeClass("enviarClick");
        window.location.replace('./userpage.html');
    }, 500);

});
