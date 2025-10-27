<?php
// includes/auth.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $stmt = $pdo->prepare("SELECT * FROM adm WHERE email_adm = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($senha, $admin['senha_adm'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id_adm'];
        $_SESSION['admin_nome'] = $admin['nome_adm'];
        $_SESSION['nivel_acesso'] = $admin['nivel_acesso'];
        
        // Atualizar último login
        $updateStmt = $pdo->prepare("UPDATE adm SET ultimo_login = NOW() WHERE id_adm = ?");
        $updateStmt->execute([$admin['id_adm']]);
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Email ou senha inválidos!";
    }
}
?>