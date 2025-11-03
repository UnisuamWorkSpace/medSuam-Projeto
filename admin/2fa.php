<?php
// 2fa.php - Página de verificação 2FA;
// session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once __DIR__ . '/../utils/utilsMail.php';

// Redirecionamentos baseados no estado da sessão;
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Se não está logado, volta para o login;
    header('Location: index.php');
    exit;
}

if (isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === true) {
    // Se já verificou 2FA, vai para dashboard;
    header('Location: dashboard.php');
    exit;
}

// Processar envio/reenvio do código 2FA;
if (isset($_POST['send_code'])) {
    $email = $_SESSION['admin_email'] ?? '';
    if (!empty($email)) {
        generate2FACode();
        if (send2FACode($email)) {
            $success = "Código de verificação enviado para seu email!";
        } else {
            $error = "Erro ao enviar código. Tente novamente.";
        }
    }
}

// Processar verificação do código;
if (isset($_POST['verify_code'])) {
    $code = $_POST['code'] ?? '';
    
    if (empty($code)) {
        $error = "Por favor, digite o código de verificação.";
    } else {
        $result = verify2FACode($code);
        
        if ($result['success']) {
            $_SESSION['2fa_verified'] = true;
            $_SESSION['2fa_verified_time'] = time();
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Enviar código automaticamente na primeira visita;
if (!isset($_SESSION['2fa_code_sent']) && isset($_SESSION['admin_email'])) {
    generate2FACode();
    send2FACode($_SESSION['admin_email']);
    $_SESSION['2fa_code_sent'] = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação em Duas Etapas - MedSuam</title>
    <link rel="icon" type="image/x-icon" href="images/logo_medsuam.png">
    <link rel="stylesheet" href="css/2fa.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="twofa-container">
        <div class="twofa-header">
            <div class="logo-container">
                <img src="images/logo_medsuam.png" alt="MedSuam Logo" class="logo-twofa">
            </div>
            <h1>Verificação em Duas Etapas</h1>
            <p>Por segurança, verifique sua identidade</p>
        </div>
        
        <div class="twofa-content">
            <div class="twofa-info">
                <div class="info-box">
                    <h3><i class="fas fa-envelope"></i> Código Enviado por Email</h3>
                    <p>Enviamos um código de 6 dígitos para:</p>
                    <p class="user-email"><?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?></p>
                    <p>Digite o código abaixo para continuar:</p>
                </div>
                
                <div class="security-tips">
                    <h4><i class="fas fa-exclamation-triangle"></i> Dicas de Segurança:</h4>
                    <ul>
                        <li>O código expira em 10 minutos</li>
                        <li>Máximo de 5 tentativas</li>
                        <li>Não compartilhe este código</li>
                    </ul>
                </div>
            </div>
            
            <div class="twofa-form">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="verification-form">
                    <div class="form-group">
                        <label for="code">Código de Verificação (6 dígitos)</label>
                        <input type="text" id="code" name="code" class="form-control code-input" 
                               maxlength="6" pattern="[0-9]{6}" placeholder="000000" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <small>Digite apenas números</small>
                    </div>
                    
                    <button type="submit" name="verify_code" class="btn btn-verify">
                        <i class="fas fa-check"></i> Verificar Código
                    </button>
                </form>
                
                <div class="resend-section">
                    <p>Não recebeu o código?</p>
                    <form method="POST">
                        <button type="submit" name="send_code" class="btn btn-resend">
                            <i class="fas fa-redo-alt"></i> Reenviar Código
                        </button>
                    </form>
                </div>
                
                <div class="logout-section">
                    <a href="index.php?logout=1" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Sair e Voltar ao Login
                    </a>
                </div>
            </div>
        </div>
        
        <div class="twofa-footer">
            <p>
                <img src="images/logo_medsuam_sem_fundo.png" alt="MedSuam" style="height: 40px; vertical-align: middle; margin-right: 10px;">
                &copy; 2025
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('code').focus();
        });
    </script>
</body>
</html>