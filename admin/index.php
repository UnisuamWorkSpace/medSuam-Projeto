<?php
// index.php - Página inicial do Painel Administrativo;
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Processar logout;
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Se já está logado, redireciona para dashboard;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedSuam - Painel Administrativo</title>
    <link rel="icon" type="image/x-icon" href="images/logo_medsuam.png" type="image/png">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="landing-container">
        <div class="landing-header">
            <div class="logo-container">
                <img src="images/logo_medsuam.png" alt="MedSuam Logo" class="logo-landing">
            </div>
            <h1>MedSuam Admin</h1>
        </div>
        
        <div class="landing-content">
            <div class="features">
                <div class="admin-info">
                    <h4>Recursos do Administrador:</h4>
                    <ul>
                        <li>Gerenciar todos os usuários do sistema</li>
                        <li>Visualizar e editar registros médicos</li>
                        <li>Controlar agendamentos de consultas</li>
                        <li>Gerar relatórios e estatísticas</li>
                        <li>Monitorar atividades do sistema</li>
                    </ul>
                </div>
            </div>
            
            <div class="login-side">
                <h2>Acessar Painel</h2>
                <p style="text-align: center; color: #666; margin-bottom: 20px;">
                    Faça login para acessar o painel administrativo completo
                </p>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" style="background-color: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 12px 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #e74c3c;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="login" value="1">
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="email" style="display: block; margin-bottom: 5px; font-weight: 500; color: #2c3e50;">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="senha" style="display: block; margin-bottom: 5px; font-weight: 500; color: #2c3e50;">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;" required>
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>
            </div>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef;">
            <p style="color: #666; margin: 0;">
                <img src="images/logo_medsuam_sem_fundo.png" alt="MedSuam" style="height: 40px; vertical-align: middle; margin-right: 10px;">
                &copy; 2025
            </p>
        </div>
    </div>

    <script>
        // Adicionar números dinâmicos nas estatísticas (opcional)
        document.addEventListener('DOMContentLoaded', function() {
            // Aqui você pode adicionar uma chamada AJAX para buscar estatísticas reais
            // Por enquanto, vamos usar números estáticos como exemplo
            const stats = [
                { element: '.stat:nth-child(1) .stat-number', target: 24, suffix: '' },
                { element: '.stat:nth-child(2) .stat-number', target: 156, suffix: '' },
                { element: '.stat:nth-child(3) .stat-number', target: 89, suffix: '' }
            ];

            stats.forEach(stat => {
                const element = document.querySelector(stat.element);
                if (element) {
                    // Remover o ícone temporariamente para a animação
                    const icon = element.innerHTML;
                    element.innerHTML = '0';
                    
                    // Animar contador
                    let current = 0;
                    const increment = stat.target / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= stat.target) {
                            element.innerHTML = icon + Math.floor(stat.target) + stat.suffix;
                            clearInterval(timer);
                        } else {
                            element.innerHTML = icon + Math.floor(current) + stat.suffix;
                        }
                    }, 40);
                }
            });
        });
    </script>
</body>
</html>