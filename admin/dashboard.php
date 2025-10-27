<?php
// dashboard.php
require_once 'includes/config.php';
checkAuth();

// Estatísticas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM paciente WHERE status_paciente = 'ativo'");
$totalPacientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM medico WHERE status_medico = 'ativo'");
$totalMedicos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM consulta");
$totalConsultas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM adm");
$totalAdmins = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<?php include 'includes/header.php'; ?>

<div class="header">
    <h2>Dashboard</h2>
    <div class="user-info">
        <span>Olá, <?php echo $_SESSION['admin_nome']; ?></span>
        <span class="user-badge"><?php echo $_SESSION['nivel_acesso']; ?></span>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-user-injured"></i>
        <h3><?php echo $totalPacientes; ?></h3>
        <p>Pacientes Ativos</p>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-user-md"></i>
        <h3><?php echo $totalMedicos; ?></h3>
        <p>Médicos Ativos</p>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-calendar-check"></i>
        <h3><?php echo $totalConsultas; ?></h3>
        <p>Total de Consultas</p>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-users-cog"></i>
        <h3><?php echo $totalAdmins; ?></h3>
        <p>Administradores</p>
    </div>
</div>

<div class="card">
    <div class="table-header">
        <h3>Atividade Recente</h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Ação</th>
                <th>Administrador</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("
                SELECT a.*, ad.nome_adm 
                FROM atualizacao_adm a 
                LEFT JOIN adm ad ON a.adm_id_adm = ad.id_adm 
                ORDER BY a.data_atualizacao DESC 
                LIMIT 10
            ");
            $atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($atividades as $atividade): 
            ?>
            <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($atividade['data_atualizacao'])); ?></td>
                <td><?php echo $atividade['descricao_atualizacao']; ?></td>
                <td><?php echo $atividade['nome_adm'] ?? 'Sistema'; ?></td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($atividades)): ?>
            <tr>
                <td colspan="3" style="text-align: center;">Nenhuma atividade registrada</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>