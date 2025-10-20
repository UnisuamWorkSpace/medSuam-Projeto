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

  $("#cpfcliente").on("blur", (event) => {
    var value = event.target.value;

    if(validaCPF(value)){
        $("#cpfSpan").text("");
    }else{
        $("#cpfSpan").text("Insira um CPF válido !");
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

function cepMask(event) {
    let value = event.target.value;

    value = value.replace(/\D/g, "");

    if (value.length > 5) value = value.replace(/^(\d{5})(\d)/, "$1-$2");

    event.target.value = value;
}

function consultarCep() {
    return new Promise((resolve) => {
        const cep = document.getElementById('cepcliente').value.replace(/\D/g, '');

        if (cep.length !== 8) {
            $("#cepSpan").text("CEP deve conter 8 dígitos !");
            resolve(false);
            return;
        }

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('ruacliente').value = data.logradouro;
                    document.getElementById('bairrocliente').value = data.bairro;
                    document.getElementById('cidadecliente').value = data.localidade;
                    document.getElementById('estadocliente').value = data.uf;
                    document.getElementById('complementocliente').value = data.complemento;
                    $("#cepSpan").text("");
                    resolve(true);
                } else {
                    $("#cepSpan").text('CEP não encontrado.');
                    resolve(false);
                }
            })
            .catch(error => {
                console.error('Erro ao consultar o CEP:', error);
                alert('Erro ao consultar o CEP.');
                resolve(false);
            });
    });
}


function validarEmail() {
    const emailInput = document.getElementById('emailcadastro');
    const email = emailInput.value.trim();

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(email)) {
        $("#emailSpan").text('Por favor, digite um e-mail válido.');
        
        return false;
    }

    $("#emailSpan").text('');
    return true;
}

function validarDataNascimento() {
    const input = document.getElementById('aniversariocliente');
    const dataValor = input.value;

    if (!dataValor) {
       
        $("#dateSpan").text('Por favor, informe sua data de nascimento.');
        return false;
    }

    const hoje = new Date();
    const nascimento = new Date(dataValor);

    if (isNaN(nascimento.getTime())) {
        $("#dateSpan").text('Data inválida.');
     
        return false;
    }else if (nascimento.getFullYear().toString().length > 4 || nascimento.getFullYear() < 1920){
         $("#dateSpan").text('Data inválida.');
        return false;
    }

    let idade = hoje.getFullYear() - nascimento.getFullYear();
    const mesDiff = hoje.getMonth() - nascimento.getMonth();
    const diaDiff = hoje.getDate() - nascimento.getDate();

    if (mesDiff < 0 || (mesDiff === 0 && diaDiff < 0)) {
        idade--;
    }

    if (idade < 18) {
         $("#dateSpan").text('Você precisa ter pelo menos 18 anos.');
        
        return false;
    }

    $("#dateSpan").text('');

    return true;
}

function senhaIgual () {
    if ($("#senhaCadastro")[0].value !== $("#senhaConfirmCadastro")[0].value) {
        $("#senhaConfirmSpan").text("As senhas devem ser iguais !");
        return false;
    }else {
        $("#senhaConfirmSpan").text("");
        return true;
    }
    
}


const form = $('#clienteForm').on("submit", async (event) => {
 
    event.preventDefault();

    if(validaCPF($("#cpfcliente")[0].value)){
        $("#cpfSpan").text("");
       
    }else{
        $("#cpfSpan").text("Insira um CPF válido !");
        return false;
    }

    if(!validarDataNascimento()) {
        return false;
    }

    const cepValido = await consultarCep(); 
    if (!cepValido) {
        return false;
    }

     if (!validarEmail()) {
        return false;
     } 

     if (!senhaIgual()) {
        return false;
     } 

    let nomeCompleto = $("#nomecliente")[0].value;
    let cpf = $("#cpfcliente")[0].value;
    let celular = $("#celularcliente")[0].value;
    let sexo = $("#generocliente")[0].value;
    let nascimento = $("#aniversariocliente")[0].value;
    let cep = $("#cepcliente")[0].value;
    let rua = $("#ruacliente")[0].value;
    let numeroRua = $("#numeroruacliente")[0].value;
    let complemento = $("#complementocliente")[0].value;     
    let bairro = $("#bairrocliente")[0].value;
    let cidade = $("#cidadecliente")[0].value;
    let estado = $("#cidadecliente")[0].value;
    let email = $("#emailcadastro")[0].value;
    let senha = $("#senhaCadastro")[0].value;
     
    const btn = $('#enviarBtn');
        
    btn.addClass("enviarClick")

    setTimeout ( () => {
        btn.removeClass("enviarClick");
        document.getElementById("clienteForm").submit();
        //window.location.replace('./login.html');
    }, 500); 
   
});

