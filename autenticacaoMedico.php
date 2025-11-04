<?php
    session_start();
    include "./utils/utilsMail.php";
    

    $email = $_SESSION['email'];

    if (!isset($_SESSION['2fa_code'])) {
        generate2FACode();
        send2FACode($email, $sendCode = true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = $_POST['senha'];
    $result = verify2FACode($inputCode);
    if ($result['success']) {
        clear2FASession();
        is2FAVerified($maxAge = 300);
        if(!isset($_SESSION['mudarSenha'])) {
            header('location: medicopage.php');
            exit;
        }else {
            header('location: reset.php');
            exit;
        }
    } else {
        $result['message'];
    }
}


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedSuam</title>
    <link rel="icon" href="./images/ChatGPT Image 11 de out. de 2025, 20_18_05 (1).png">
    <link rel="stylesheet" href="./css/autenticacao.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>
<body>
    <main>
        <div class="formContainer">
            <a href="./index.html"><img class="logoForm" src="./images/logo_branco.png"/></a>
            <h1>Autenticação</h1>
            <label class="labelStyle mainLabel">Insira o código de 6 dígitos enviado para o seu E-mail para continuar.</label>
            <span class="spanStyle">
                <?php if(isset($result['message'])):?>
                    <?php echo $result['message']?>
                <?php endif;?>
            </span>
        
            <form id="autenticacaoForm" action="autenticacaomedico.php" method="post" >          
                
                <div class="senhaContainer">
                    <input class="inputStyle" type="password" minlength="6" maxlength="6" id="codigo" name="senha" placeholder="Insira o código aqui !"  required>
                    <img id="eyeCodigo" class="eye-slash" onclick="mostrarSenha('codigo', 'eyeCodigo')" src="./images/eye-slash.svg"/>
                </div>
                <input id="enviarBtn" class="buttonStyle" type="submit" value="Enviar">

                <div class="voltarLink">
                    <a href="./index.html"><i class="fa-solid fa-angle-left"></i></a>
                    <a href="./index.html">Voltar para o site</a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/autenticacao.js"></script>

</body>
</html>