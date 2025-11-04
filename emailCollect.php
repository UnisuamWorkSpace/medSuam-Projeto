<?php
    include "dbMedsuam.php";
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST["email"]);                    // remove leading/trailing spaces
        $email = strtolower($email);

        $sql = "SELECT * FROM paciente WHERE email_paciente='$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        $sql2 = "SELECT * FROM medico WHERE email_medico='$email' LIMIT 1";
        $result2 = mysqli_query($conn, $sql2);

        if(mysqli_num_rows($result) === 1 || mysqli_num_rows($result2) === 1) {
            $status = (mysqli_num_rows($result) === 1 ? '1' : '0') . (mysqli_num_rows($result2) === 1 ? '1' : '0');
            
            switch ($status) {
                case '10':
                    $account = mysqli_fetch_assoc($result);                      
                    $_SESSION['mudarSenha'] = true;
                    $_SESSION['email'] = $account['email_paciente'];
                    header('location: autenticacao.php');
                     exit;                       
                    break;
                case '01':
                    $account = mysqli_fetch_assoc($result2);
                    $_SESSION['mudarSenha'] = true;
                    $_SESSION['email'] = $account['email_medico'];
                    header('location: autenticacaomedico.php');
                    exit;
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
    <link rel="stylesheet" href="./css/emailCollect.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>
<body>
    <main>
        <div class="formContainer">
            <a href="./index.html"><img class="logoForm" src="./images/logo_branco.png"/></a>

            <h1>Mudar Senha</h1>
            <label class="labelStyle mainLabel">Insira o E-mail associado com a sua conta e nós enviaremos instruções para mudar sua senha.</label>
        
            <form id="emailCollectForm" action="emailCollect.php" method="post" >          
                
                <input class="inputStyle noMarginBot " type="text"  id="emailCollect" name="email" placeholder="Insira seu E-mail aqui !" onblur="validarEmail()" required>
                        <span  id="emailCollectSpan" class="spanStyle"></span>
                <input id="enviarBtn" class="buttonStyle" type="submit" value="Enviar">

                <div class="voltarLink">
                    <a href="./index.html"><i class="fa-solid fa-angle-left"></i></a>
                    <a href="./login.html">Retornar para Login</a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/emailCollect.js"></script>

</body>
</html>