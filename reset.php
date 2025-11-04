<?php
    session_start();

    include "dbMedsuam.php";
    if (!isset($_SESSION['email'])) {
    // If the session lost the email, go back to login
    echo "email nao ta legaod";
    exit;
}
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $senhaCliente = mysqli_real_escape_string($conn, $_POST["senha"]);

        $sql = "SELECT * FROM paciente WHERE email_paciente='{$_SESSION['email']}' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        $sql2 = "SELECT * FROM medico WHERE email_medico='{$_SESSION['email']}' LIMIT 1";
        $result2 = mysqli_query($conn, $sql2);

        if(mysqli_num_rows($result) === 1 || mysqli_num_rows($result2) === 1) {
            $status = (mysqli_num_rows($result) === 1 ? '1' : '0') . (mysqli_num_rows($result2) === 1 ? '1' : '0');
            
            switch ($status) {
                case '10':
                    $password_hash = password_hash($senhaCliente, PASSWORD_DEFAULT);
                    $sql = "UPDATE paciente SET senha_paciente = '$password_hash' WHERE email_paciente ='{$_SESSION['email']}'";
                    mysqli_query($conn, $sql);  
                    unset($_SESSION['mudarSenha']);                   
                    header('location: login.php');
                    
                                           
                    break;
                case '01':
                    $password_hash = password_hash($senhaCliente, PASSWORD_DEFAULT);
                    $sql = "UPDATE medico SET senha_medico = '$password_hash' WHERE email_medico ='{$_SESSION['email']}'";
                    mysqli_query($conn, $sql);
                    unset($_SESSION['mudarSenha']);    
                    header('location: login.php');
                    
                    
                    break;

                case '11':
                    echo "erro de login";
                    break;
                
                default:
                    echo "<script>alert('E-mail não encontrado.');</script>";
                    break;
            }
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
    <link rel="stylesheet" href="./css/reset.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>
<body>
    <main>
        <div class="formContainer">
            <a href="./index.html"><img class="logoForm" src="./images/logo_branco.png"/></a>

            <h1>Mude Sua Senha</h1>
            <label class="labelStyle mainLabel">Insira uma senha nova abaixo para mudar sua senha.</label>
        
            <form id="resetForm" action="./reset.php" method="post" >          
                
                <div class="senhaContainer">
                    <input class="inputStyle" type="password" minlength="8" maxlength="12" id="senhaReset" name="senha" placeholder="Nova Senha" onblur="mimimoCaracter()" required>
                    <img id="eyeReset" class="eye-slash" onclick="mostrarSenha('senhaReset', 'eyeReset')" src="./images/eye-slash.svg"/>
                </div>
                <div class="senhaContainer">
                    <input class="inputStyle" type="password" minlength="8" maxlength="12" id="senhaResetConfirm" name="senhaConfirm" placeholder="Confirmar Senha" onblur="senhaIgual()" required>
                    <img id="eyeResetConfirm" class="eye-slash" onclick="mostrarSenha('senhaResetConfirm', 'eyeResetConfirm')" src="./images/eye-slash.svg"/>
                </div>
                <span  id="resetSpan" class="spanStyle"></span>
                <div class="infoSpanContainer">
                    <label class="labelStyle mainLabel infoLabel">Sua senha deve conter:</label>
                    <span id="infoSpan"><i class="fa-solid fa-xmark"></i> Pelo menos 8 caracteres.</span>
                </div>

                <input id="enviarBtn" class="buttonStyle" type="submit" value="Enviar">

              
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/reset.js"></script>

</body>
</html>