<?php
// index.php - Página inicial do Painel Administrativo
require_once 'includes/config.php';

// Se já está logado, redireciona para dashboard
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
                <div class="feature">
                    <i class="fas fa-users-cog"></i>
                    <div class="feature-content">
                        <h3>Gerenciamento Completo</h3>
                        <p>Controle total sobre médicos, pacientes, consultas e administradores do sistema.</p>
                    </div>
                </div>
                
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <div class="feature-content">
                        <h3>Dashboard Interativo</h3>
                        <p>Visualize estatísticas e métricas importantes em tempo real.</p>
                    </div>
                </div>
                
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <div class="feature-content">
                        <h3>Segurança Total</h3>
                        <p>Sistema protegido com autenticação e níveis de acesso hierárquicos.</p>
                    </div>
                </div>
                
                <div class="feature">
                    <i class="fas fa-mobile-alt"></i>
                    <div class="feature-content">
                        <h3>Responsivo</h3>
                        <p>Interface adaptável para desktop, tablet e smartphones.</p>
                    </div>
                </div>

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
                
                <!-- Vou ter que testar esse caminho no XAMPP, pois no php -S foi e no Apache também -->
                <a href="login.php" class="btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Fazer Login
                </a>
                
                <div class="stats">
                    <div class="stat">
                        <span class="stat-number">
                            <i class="fas fa-user-md"></i>
                        </span>
                        <span class="stat-label">Médicos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">
                            <i class="fas fa-user-injured"></i>
                        </span>
                        <span class="stat-label">Pacientes</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <span class="stat-label">Consultas</span>
                    </div>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 6px; border-left: 4px solid #ffc107;">
                    <h4 style="color: #856404; margin-bottom: 8px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Acesso Restrito
                    </h4>
                    <p style="color: #856404; font-size: 0.9rem;">
                        Esta área é exclusiva para administradores autorizados. 
                        Contate o super administrador para obter credenciais de acesso.
                    </p>
                </div>
            </div>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef;">
            <p style="color: #666; margin: 0;">
                <img src="images/logo_medsuam_sem_fundo.png" alt="MedSuam" style="height: 40px; vertical-align: middle; margin-right: 10px;">
                <!-- <i class="fas fa-heart"></i> -->
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