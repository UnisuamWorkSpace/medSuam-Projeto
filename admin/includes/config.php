<?php
// includes/config.php
session_start();

$host = 'localhost';
$dbname = 'bd_medsuam';
$username = (PHP_OS_FAMILY === 'Windows') ? 'root' : 'phpuser';// $username = 'root';//'phpuser'
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Verificar se usuário está logado
function checkAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }

    if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
        header('Location: 2fa.php');
        exit;
    }
}

// Verificar se é super admin
function isSuperAdmin() {
    return isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] === 'super';
}

// Registrar atividade no sistema
function registrarAtividade($descricao, $tipo = 'info') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao, tipo_atualizacao) VALUES (?, ?, ?)");
        $stmt->execute([
            $_SESSION['admin_id'] ?? null,
            $descricao,
            $tipo
        ]);
        return true;
    } catch (PDOException $e) {
        // Não quebrar a aplicação se falhar o registro de atividade
        error_log("Erro ao registrar atividade: " . $e->getMessage());
        return false;
    }
}

// Validar CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Calcula e confere primeiro dígito verificador
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    
    return true;
}
?>