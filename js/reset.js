function mostrarSenha (id, imagem)  {   

    var inputType = $('#' + id)[0];
    var eye = $('#' + imagem)[0];
    if(inputType.type === "password") {
        inputType.type = "text";
        eye.src = "./images/eye.svg";
    }else {
       inputType.type = "password"; 
       eye.src = "./images/eye-slash.svg";
    }

}

 function mimimoCaracter () {
    const senha = $("#senhaReset").val();
    const senhaConfirm = $("#senhaResetConfirm").val();
     if(senha.length >= 8 && senhaConfirm.length >= 8) {
        $("#infoSpan").css("color", "green");
        $("#infoSpan").html('<i class="fas fa-check"></i> Pelo menos 8 caracteres');
    }else {
        $("#infoSpan").css("color", "red");
        $("#infoSpan").html('<i class="fa-solid fa-xmark"></i> Pelo menos 8 caracteres');
    }

 }

 

function senhaIgual() {
    const senha = $("#senhaReset").val();
    const senhaConfirm = $("#senhaResetConfirm").val();

    mimimoCaracter();

    if (senha !== senhaConfirm) {
        $("#resetSpan").text("As senhas devem ser iguais !");
        return false;
    } else {
        $("#resetSpan").text("");
        return true;
    }
}



const form = $('#resetForm').on("submit", async (event) => {
 
    event.preventDefault();


    if(!senhaIgual()){
        return false;
    }

    
    const btn = $('#enviarBtn');
    
        
        btn.addClass("enviarClick")


        setTimeout ( () => {
            btn.removeClass("enviarClick");
            window.location.replace('./login.html');
        }, 500); 

    

    
});

