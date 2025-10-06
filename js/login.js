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
    const emailInput = document.getElementById('emaillogin');
    const email = emailInput.value.trim();
    
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(email)) {
        $("#emailLoginSpan").text('Por favor, digite um e-mail válido.');
        
        return false;
    }

    $("#emailLoginSpan").text('');
    return true;
}

const form = $('#loginForm').on("submit", (event) => {  

    event.preventDefault();

    if (!validarEmail()) {
        event.preventDefault();
        return false; 
    }
   
    const btn = $('#enviarBtn');
        
        btn.addClass("enviarClick")
    
        setTimeout ( () => {
            btn.removeClass("enviarClick");
            window.location.replace('./autenticacao.html');
        }, 500); 

    
    
    
});
