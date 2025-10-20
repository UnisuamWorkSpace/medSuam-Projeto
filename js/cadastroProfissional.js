
function soLetras (event) {
    var value = event.target.value;

    value = value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ ]/g, "");
    
    event.target.value = value;

}

function validaCPF(cpf) {  
    var Soma = 0
    var Resto
  
    var strCPF = String(cpf).replace(/[^\d]/g, '')
    
    if (strCPF.length !== 11)
       return false
    
    if ([
      '00000000000',
      '11111111111',
      '22222222222',
      '33333333333',
      '44444444444',
      '55555555555',
      '66666666666',
      '77777777777',
      '88888888888',
      '99999999999',
      ].indexOf(strCPF) !== -1)
      return false
  
    for (i=1; i<=9; i++)
      Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
  
    Resto = (Soma * 10) % 11
  
    if ((Resto == 10) || (Resto == 11)) 
      Resto = 0
  
    if (Resto != parseInt(strCPF.substring(9, 10)) )
      return false
  
    Soma = 0
  
    for (i = 1; i <= 10; i++)
      Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i)
  
    Resto = (Soma * 10) % 11
  
    if ((Resto == 10) || (Resto == 11)) 
      Resto = 0
  
    if (Resto != parseInt(strCPF.substring(10, 11) ) )
      return false
  
    return true
  }

  function cpfMask (event) {
     let value = event.target.value;

    value = value.replace(/\D/g, "");

    if (value.length > 3) value = value.replace(/(\d{3})(\d)/, "$1.$2");
    if (value.length > 6) value = value.replace(/(\d{3})\.(\d{3})(\d)/, "$1.$2.$3");
    if (value.length > 9) value = value.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3-$4");

    event.target.value = value;
  } 

  $("#cpfProfissional").on("blur", (event) => {
    var value = event.target.value;

    if(validaCPF(value)){
        $("#cpfProfissionalSpan").text("");
    }else{
        $("#cpfProfissionalSpan").text("Insira um CPF válido !");
    }

  });

  function phoneMask(event) {
    let value = event.target.value;

    value = value.replace(/\D/g, "");

    if (value.length > 0) value = value.replace(/^(\d{0,2})/, "($1");
    if (value.length > 2) value = value.replace(/\((\d{2})/, "($1) ");
    if (value.length > 7) value = value.replace(/(\d{4,5})(\d{4})$/, "$1-$2");

    event.target.value = value;
}

function validarEmail() {
    const emailInput = document.getElementById('emailProfissional');
    const email = emailInput.value.trim();

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(email)) {
        $("#emailProfissionalSpan").text('Por favor, digite um e-mail válido.');
        
        return false;
    }

    $("#emailProfissionalSpan").text('');
    return true;
}

const form = $('#profissionalForm').on("submit", async (event) => {
 
    event.preventDefault();

    if(validaCPF($("#cpfProfissional")[0].value)){
        $("#cpfProfissionalSpan").text("");
       
    }else{
        $("#cpfProfissionalSpan").text("Insira um CPF válido !");
        return false;
    }

     if (!validarEmail()) {
        return false;
     } 
    
    const btn = $('#enviarBtn');
        
        btn.addClass("enviarClick")
      

        setTimeout ( () => {
            btn.removeClass("enviarClick");
            window.location.replace("./login.html");
        }, 500); 

});

