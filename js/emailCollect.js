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

function validarEmail() {
    const emailInput = document.getElementById('emailCollect');
    const email = emailInput.value.trim();
    
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(email)) {
        $("#emailCollectSpan").text('Por favor, digite um e-mail válido.');
        
        return false;
    }

    $("#emailCollectSpan").text('');
    return true;
}

const form = $('#emailCollectForm');

form.on("submit", (event) => {
        event.preventDefault();
        if (!validarEmail()) {
        
        return false; 
    }

    const btn = $('#enviarBtn');
    btn.addClass("enviarClick");

    setTimeout(() => {
        btn.removeClass("enviarClick");
        document.getElementById("emailCollectForm").submit();
    }, 500);

   

});
