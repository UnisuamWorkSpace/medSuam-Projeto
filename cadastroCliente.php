<?php

    include 'dbMedsuam.php';
    session_start();
    if(isset($_SESSION['id'])) {
        header('location: userpage.php');
        exit;
    }

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $nomeCliente = mysqli_real_escape_string($conn, $_POST["nomecliente"]);
        $nomeCliente = ucwords(strtolower($nomeCliente));
        $cpfCliente = mysqli_real_escape_string($conn, $_POST["cpfcliente"]);            
        $celularCliente = mysqli_real_escape_string($conn, $_POST["celularcliente"]);
        
        $numeroLimpo = preg_replace('/\D/', '', $celularCliente);
        $ddd = substr($numeroLimpo, 0, 2);
        $numero = substr($numeroLimpo, 2);

        $generoCliente = mysqli_real_escape_string($conn, $_POST["generocliente"]);
        $aniversarioCliente = mysqli_real_escape_string($conn, $_POST["aniversariocliente"]);
        $cepCliente = mysqli_real_escape_string($conn, $_POST["cepcliente"]);
        $ruaCliente = mysqli_real_escape_string($conn, $_POST["ruacliente"]);
        $numeroCliente = mysqli_real_escape_string($conn, $_POST["numeroruacliente"]);
        $complementoCliente = mysqli_real_escape_string($conn, $_POST["complementocliente"]);
        $bairroCliente = mysqli_real_escape_string($conn, $_POST["bairrocliente"]);
        $cidadeCliente = mysqli_real_escape_string($conn, $_POST["cidadecliente"]);
        $estadoCliente = mysqli_real_escape_string($conn, $_POST["estado"]);
        /* Faz o email ficar em lowercase */
        $emailCliente = trim($_POST["email"]);                    // remove leading/trailing spaces
        $emailCliente = strtolower($emailCliente);               // normalize to lowercase
        $emailCliente = mysqli_real_escape_string($conn, $emailCliente); // escape for SQL
        $senhaCliente = mysqli_real_escape_string($conn, $_POST["senha"]);
        $senhaConfirmaCliente = mysqli_real_escape_string($conn, $_POST["senhaconfirm"]);

        if($senhaCliente === $senhaConfirmaCliente) {     
        $sql = "SELECT * FROM paciente WHERE email_paciente ='$emailCliente' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        $sql2 = "SELECT * FROM paciente WHERE cpf_paciente ='$cpfCliente' LIMIT 1";
        $result2 = mysqli_query($conn, $sql2);

        $sql3 = "SELECT * FROM Medico WHERE email_medico ='$emailCliente' LIMIT 1";
        $result3 = mysqli_query($conn, $sql3);

        if(mysqli_num_rows($result) === 1 || mysqli_num_rows($result2) === 1 || mysqli_num_rows($result3) === 1) {
            $status = (mysqli_num_rows($result) === 1 ? '1' : '0') . (mysqli_num_rows($result2) === 1 ? '1' : '0') . (mysqli_num_rows($result3) === 1 ? '1' : '0');

            switch ($status) {
                case '000': // nothing found
                    $errorEmail = '';
                    $errorCpf = '';
                break;

                case '100': // only medico email exists
                    $errorEmail = "Email já cadastrado !";
                    $errorCpf = '';
                break;

                case '010': // only medico CPF exists
                    $errorCpf = "CPF já cadastrado !";
                    $errorEmail = '';
                break;

                case '001': // only paciente email exists
                    $errorEmail = "Email já cadastrado !";
                    $errorCpf = '';
                break;

                case '110': // medico email + medico CPF exist
                    $errorEmail = "Email já cadastrado !";
                    $errorCpf = "CPF já cadastrado !";
                break;

                case '101': // medico email + paciente email exist
                    $errorEmail = "Email já cadastrado !";
                    $errorCpf = '';
                break;

                case '011': // medico CPF + paciente email exist
                    $errorEmail = "Email já cadastrado !";
                    $errorCpf = "CPF já cadastrado !";
                break;

                case '111': // everything exists
                    $errorEmail = "Email já cadastrado !";
                    $errorCpf = "CPF já cadastrado !";
                break;
            }
        }else {
                    $password_hash = password_hash($senhaCliente, PASSWORD_DEFAULT);
                
                    $sql = "INSERT INTO paciente (nome_paciente, cpf_paciente, data_nasc_paciente, 	email_paciente, senha_paciente, sexo_paciente ) VALUES('$nomeCliente', '$cpfCliente', '$aniversarioCliente', '$emailCliente', '$password_hash', '$generoCliente')";
        
                    if(mysqli_query($conn, $sql)) {
                        echo "paciente INSERTED";
                    }else {
                        echo 'paciente deu erro';
                    }

                    $sql = "SELECT * FROM paciente WHERE email_paciente='$emailCliente' LIMIT 1";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) === 1) {
                        $account = mysqli_fetch_assoc($result);
                        $sql = "INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, uf_endereco, paciente_id_paciente) VALUES('$cepCliente', '$ruaCliente', '$numeroCliente', '$complementoCliente', '$bairroCliente', '$cidadeCliente', '$estadoCliente', '{$account['id_paciente']}')";
                        if(mysqli_query($conn, $sql)) {
                            echo "endereco INSERTED";
                        }else {
                            echo 'endereco deu erro';
                        }
                    }   

                    $sql = "SELECT * FROM paciente WHERE email_paciente='$emailCliente' LIMIT 1";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) === 1) {
                        $account = mysqli_fetch_assoc($result);

                        $sql = "INSERT INTO telefone (dd, telefone, paciente_id_paciente) VALUES('$ddd', '$numero', '{$account['id_paciente']}')";
                        if(mysqli_query($conn, $sql)) {
                            echo "telefone INSERTED";
                        }else {
                            echo 'telefone deu erro';
                        }
                    }

                    $sql = "SELECT * FROM paciente WHERE email_paciente='$emailCliente' LIMIT 1";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) === 1) {
                        $account = mysqli_fetch_assoc($result);
                        var_dump($account);
                        $sql = "INSERT INTO rg ( paciente_id_paciente) VALUES('{$account['id_paciente']}')";
                    if(mysqli_query($conn, $sql)) {
                        echo "rg INSERTED";
                    }else {
                        echo 'rg deu erro';
                    }
                    }
                
                    header('location: login.php');
                    exit;
                }
    
    
            }
    }

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedSuam</title>
    <link rel="icon" href="./images/ChatGPT Image 11 de out. de 2025, 20_18_05 (1).png">
    <link rel="stylesheet" href="./css/cadastroCliente.css"/>
</head>
<body>
    <main>
        <div class="errorMsgDiv">Preencha todos os campos obrigatórios corretamente!</div>
        <div class="formContainer">
            <a href="./index.html"><img class="logoForm" src="./images/logo_branco.png"/></a>
            <h1>Crie sua conta</h1>
            <label class="labelStyle mainLabel">Informações pessoais - Necessário para Prescrição Digital</label>
            <form id="clienteForm" action="cadastroCliente.php" method="post">  
                <div class="inputContainer">
                        <input class="inputStyle cantBeEmpty" type="text"  id="nomecliente" name="nomecliente" maxlength="70" placeholder="Nome Completo" onkeyup="soLetras(event)" required>
                        <span id="nomeSpan" class="spanStyle"></span>
                        <input class="inputStyle noMarginBot" type="text"  id="cpfcliente" name="cpfcliente" placeholder="CPF" maxlength="14"  onkeyup="cpfMask(event)" required>
                        <span  id="cpfSpan" class="spanStyle">
                            <?php if(isset($errorCpf)): ?>
                            <?php echo $errorCpf?>
                            <?php endif;?>
                        </span>
                        <input class="inputStyle" type="text"  id="celularcliente" name="celularcliente" placeholder="Celular" maxlength="15" onkeyup="phoneMask(event)" required>
                        <select class="inputStyle" id="generocliente" name="generocliente" required>
                            <option value="" disabled selected>Sexo</option>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                        </select>
                        <label class="labelStyle mainLabel">Data de Nascimento</label>
                        <input class="inputStyle birthday" type="date"  id="aniversariocliente" name="aniversariocliente"  onblur="validarDataNascimento()" required>
                        <span  id="dateSpan" class="spanStyle"></span>

                        <label class="labelStyle">Endereço</label>
                        <input class="inputStyle" type="text"  id="cepcliente" name="cepcliente" placeholder="CEP" maxlength="9" onkeyup="cepMask(event)" onblur="consultarCep()" required>
                        <span  id="cepSpan" class="spanStyle"></span>
                        <input class="inputStyle cantBeEmpty" type="text"  id="ruacliente" name="ruacliente" placeholder="Rua" required>
                        <span class="spanStyle"></span>
                        <input class="inputStyle" type="number"  id="numeroruacliente" name="numeroruacliente" placeholder="Número" required>
                        <input class="inputStyle" type="text"  id="complementocliente" name="complementocliente" placeholder="Complemento (opicional)">
                        <input class="inputStyle cantBeEmpty" type="text"  id="bairrocliente" name="bairrocliente" placeholder="Bairro" required>
                        <span class="spanStyle"></span>
                        <input class="inputStyle cantBeEmpty" type="text"  id="cidadecliente" name="cidadecliente" placeholder="Cidade" onkeyup="soLetras(event)" required>
                        <span class="spanStyle"></span>
                        <select id="estadocliente" class="inputStyle" name="estado">
                            <option value="" disabled selected>UF</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                        <label class="labelStyle">Dados da conta</label>
                        <input class="inputStyle" type="text"  id="emailcadastro" name="email" placeholder="Email" onblur="validarEmail()" required>
                        <span  id="emailSpan" class="spanStyle">
                            <?php if(isset($errorEmail)): ?>
                            <?php echo $errorEmail?>
                            <?php endif;?>
                        </span>
                        <div class="senhaContainer">
                            <input class="inputStyle cantBeEmpty" type="password" minlength="8" maxlength="12" id="senhaCadastro" name="senha" placeholder="Senha" required>
                            <img id="eyeSenhaCadastro" class="eye-slash" onclick="mostrarSenha('senhaCadastro', 'eyeSenhaCadastro')" src="./images/eye-slash.svg"/>
                        </div>
                        <span id="senhaSpan" class="spanStyle"></span>
                        <div class="senhaContainer">
                            <input class="inputStyle" type="password" minlength="8" maxlength="12" id="senhaConfirmCadastro" name="senhaconfirm" placeholder="Confirmar Senha" onblur="senhaIgual()" required>
                            <img id="eyeSenhaConfirmCadastro" class="eye-slash" onclick="mostrarSenha('senhaConfirmCadastro', 'eyeSenhaConfirmCadastro')" src="./images/eye-slash.svg"/>
                        </div>
                        <span  id="senhaConfirmSpan" class="spanStyle"></span>
          
                        <input id="enviarBtn" class="buttonStyle" type="submit" value="Enviar">
                        <div class="criarConta">
                            <span>Já tem Conta ?</span>
                            <a href="./login.php">Entrar</a>
                        </div>
                </div>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/cadastroCliente.js"></script>

</body>
</html>