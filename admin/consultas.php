<?php
// consultas.php
require_once 'includes/config.php';
checkAuth();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_consulta'])) {
        $id_paciente = $_POST['id_paciente'];
        $id_medico = $_POST['id_medico'];
        $data_consulta = $_POST['data_consulta'];
        $hora_consulta = $_POST['hora_consulta'];
        $status = $_POST['status'];
        $gravacao_link = $_POST['gravacao_link'];
        $link_videochamada = $_POST['link_videochamada'];
        
        // Combinar data e hora para o campo hora_consulta (datetime)
        $data_hora_consulta = $data_consulta . ' ' . $hora_consulta . ':00';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO consulta (id_paciente, id_medico, data_consulta, hora_consulta, status, gravacao_link, link_videochamada) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$id_paciente, $id_medico, $data_consulta, $data_hora_consulta, $status, $gravacao_link, $link_videochamada])) {
                $consulta_id = $pdo->lastInsertId();
                
                $pdo->commit();
                $success = "Consulta agendada com sucesso!";
                
                // Registrar atividade
                $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                $stmt->execute([$_SESSION['admin_id'], "Agendou consulta ID: $consulta_id"]);
            } else {
                $pdo->rollBack();
                $error = "Erro ao agendar consulta!";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao agendar consulta: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['editar_consulta'])) {
        $id = $_POST['id'];
        $id_paciente = $_POST['id_paciente'];
        $id_medico = $_POST['id_medico'];
        $data_consulta = $_POST['data_consulta'];
        $hora_consulta = $_POST['hora_consulta'];
        $status = $_POST['status'];
        $gravacao_link = $_POST['gravacao_link'];
        $link_videochamada = $_POST['link_videochamada'];
        
        // Combinar data e hora para o campo hora_consulta (datetime)
        $data_hora_consulta = $data_consulta . ' ' . $hora_consulta . ':00';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE consulta SET id_paciente = ?, id_medico = ?, data_consulta = ?, hora_consulta = ?, status = ?, gravacao_link = ?, link_videochamada = ? WHERE id_consulta = ?");
            if ($stmt->execute([$id_paciente, $id_medico, $data_consulta, $data_hora_consulta, $status, $gravacao_link, $link_videochamada, $id])) {
                
                $pdo->commit();
                $success = "Consulta atualizada com sucesso!";
                
                // Registrar atividade
                $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                $stmt->execute([$_SESSION['admin_id'], "Editou consulta ID: $id"]);
            } else {
                $pdo->rollBack();
                $error = "Erro ao atualizar consulta!";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao atualizar consulta: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['alterar_status_consulta'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE consulta SET status = ? WHERE id_consulta = ?");
        if ($stmt->execute([$status, $id])) {
            $success = "Status da consulta atualizado com sucesso!";
            
            // Registrar atividade
            $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
            $stmt->execute([$_SESSION['admin_id'], "Alterou status da consulta ID: $id para: $status"]);
        } else {
            $error = "Erro ao atualizar status da consulta!";
        }
    }
    
    if (isset($_POST['excluir_consulta'])) {
        $id = $_POST['id'];
        
        try {
            $pdo->beginTransaction();
            
            // Remover referências em laudo (se existir)
            $laudoStmt = $pdo->prepare("DELETE FROM laudo WHERE id_consulta = ?");
            $laudoStmt->execute([$id]);
            
            // Agora excluir a consulta
            $stmt = $pdo->prepare("DELETE FROM consulta WHERE id_consulta = ?");
            if ($stmt->execute([$id])) {
                $pdo->commit();
                $success = "Consulta excluída com sucesso!";
                
                // Registrar atividade
                $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                $stmt->execute([$_SESSION['admin_id'], "Excluiu consulta ID: $id"]);
            } else {
                $pdo->rollBack();
                $error = "Erro ao excluir consulta!";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao excluir consulta: " . $e->getMessage();
        }
    }
}

// Buscar consultas com joins
$stmt = $pdo->query("
    SELECT c.*, p.nome_paciente, m.nome_medico 
    FROM consulta c 
    LEFT JOIN paciente p ON c.id_paciente = p.id_paciente 
    LEFT JOIN medico m ON c.id_medico = m.id_medico 
    ORDER BY c.data_consulta DESC, c.hora_consulta DESC
");
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar pacientes e médicos para os selects
$pacientesStmt = $pdo->query("SELECT id_paciente, nome_paciente FROM paciente WHERE status_paciente = 'ativo' ORDER BY nome_paciente");
$pacientes = $pacientesStmt->fetchAll(PDO::FETCH_ASSOC);

$medicosStmt = $pdo->query("SELECT id_medico, nome_medico FROM medico WHERE status_medico = 'ativo' ORDER BY nome_medico");
$medicos = $medicosStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="header">
    <h2>Gerenciar Consultas</h2>
    <button class="btn btn-primary" onclick="adminSystem.openModal('modalAdicionarConsulta')">
        <i class="fas fa-plus"></i> Agendar Consulta
    </button>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-header">
        <h3>Consultas Agendadas</h3>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Pesquisar consultas...">
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Status</th>
                <th>Links</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($consultas as $consulta): 
                $hora_formatada = date('H:i', strtotime($consulta['hora_consulta']));
            ?>
            <tr>
                <td><?php echo $consulta['id_consulta']; ?></td>
                <td><?php echo htmlspecialchars($consulta['nome_paciente']); ?></td>
                <td><?php echo htmlspecialchars($consulta['nome_medico']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($consulta['data_consulta'])); ?></td>
                <td><?php echo $hora_formatada; ?></td>
                <td>
                    <span class="status <?php echo $consulta['status'] === 'confirmada' ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo $consulta['status']; ?>
                    </span>
                </td>
                <td>
                    <?php if ($consulta['link_videochamada']): ?>
                        <a href="<?php echo htmlspecialchars($consulta['link_videochamada']); ?>" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-video"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($consulta['gravacao_link']): ?>
                        <a href="<?php echo htmlspecialchars($consulta['gravacao_link']); ?>" target="_blank" class="btn btn-sm btn-secondary">
                            <i class="fas fa-play-circle"></i>
                        </a>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <button class="btn btn-primary btn-sm" onclick="editarConsulta(<?php echo htmlspecialchars(json_encode($consulta)); ?>)">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $consulta['id_consulta']; ?>">
                        <select name="status" onchange="this.form.submit()" class="form-control-sm">
                            <option value="agendada" <?php echo $consulta['status'] === 'agendada' ? 'selected' : ''; ?>>Agendada</option>
                            <option value="confirmada" <?php echo $consulta['status'] === 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                            <option value="realizada" <?php echo $consulta['status'] === 'realizada' ? 'selected' : ''; ?>>Realizada</option>
                            <option value="cancelada" <?php echo $consulta['status'] === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                        </select>
                        <input type="hidden" name="alterar_status_consulta" value="1">
                    </form>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $consulta['id_consulta']; ?>">
                        <button type="submit" name="excluir_consulta" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Tem certeza que deseja excluir esta consulta? Esta ação não pode ser desfeita.')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Adicionar Consulta -->
<div id="modalAdicionarConsulta" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agendar Nova Consulta</h3>
            <span class="close">&times;</span>
        </div>
        <form method="POST">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="id_paciente">Paciente:</label>
                        <select id="id_paciente" name="id_paciente" class="form-control" required>
                            <option value="">Selecione um paciente</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?php echo $paciente['id_paciente']; ?>">
                                    <?php echo htmlspecialchars($paciente['nome_paciente']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_medico">Médico:</label>
                        <select id="id_medico" name="id_medico" class="form-control" required>
                            <option value="">Selecione um médico</option>
                            <?php foreach ($medicos as $medico): ?>
                                <option value="<?php echo $medico['id_medico']; ?>">
                                    <?php echo htmlspecialchars($medico['nome_medico']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_consulta">Data da Consulta:</label>
                        <input type="date" id="data_consulta" name="data_consulta" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="hora_consulta">Hora da Consulta:</label>
                        <input type="time" id="hora_consulta" name="hora_consulta" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="agendada">Agendada</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="realizada">Realizada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="gravacao_link">Link da Gravação:</label>
                        <input type="url" id="gravacao_link" name="gravacao_link" class="form-control" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label for="link_videochamada">Link da Videochamada:</label>
                        <input type="url" id="link_videochamada" name="link_videochamada" class="form-control" placeholder="https://...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="adicionar_consulta" class="btn btn-primary">Agendar Consulta</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Consulta -->
<div id="modalEditarConsulta" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Consulta</h3>
            <span class="close">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" id="edit_id" name="id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_id_paciente">Paciente:</label>
                        <select id="edit_id_paciente" name="id_paciente" class="form-control" required>
                            <option value="">Selecione um paciente</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?php echo $paciente['id_paciente']; ?>">
                                    <?php echo htmlspecialchars($paciente['nome_paciente']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_id_medico">Médico:</label>
                        <select id="edit_id_medico" name="id_medico" class="form-control" required>
                            <option value="">Selecione um médico</option>
                            <?php foreach ($medicos as $medico): ?>
                                <option value="<?php echo $medico['id_medico']; ?>">
                                    <?php echo htmlspecialchars($medico['nome_medico']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_data_consulta">Data da Consulta:</label>
                        <input type="date" id="edit_data_consulta" name="data_consulta" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_hora_consulta">Hora da Consulta:</label>
                        <input type="time" id="edit_hora_consulta" name="hora_consulta" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_status">Status:</label>
                        <select id="edit_status" name="status" class="form-control" required>
                            <option value="agendada">Agendada</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="realizada">Realizada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_gravacao_link">Link da Gravação:</label>
                        <input type="url" id="edit_gravacao_link" name="gravacao_link" class="form-control" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_link_videochamada">Link da Videochamada:</label>
                        <input type="url" id="edit_link_videochamada" name="link_videochamada" class="form-control" placeholder="https://...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="editar_consulta" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarConsulta(consulta) {
    document.getElementById('edit_id').value = consulta.id_consulta;
    document.getElementById('edit_id_paciente').value = consulta.id_paciente;
    document.getElementById('edit_id_medico').value = consulta.id_medico;
    document.getElementById('edit_data_consulta').value = consulta.data_consulta;
    
    // Extrair hora do datetime
    const horaConsulta = new Date(consulta.hora_consulta);
    const horas = horaConsulta.getHours().toString().padStart(2, '0');
    const minutos = horaConsulta.getMinutes().toString().padStart(2, '0');
    document.getElementById('edit_hora_consulta').value = `${horas}:${minutos}`;
    
    document.getElementById('edit_status').value = consulta.status;
    document.getElementById('edit_gravacao_link').value = consulta.gravacao_link || '';
    document.getElementById('edit_link_videochamada').value = consulta.link_videochamada || '';
    
    adminSystem.openModal('modalEditarConsulta');
}

// Configurar data mínima para hoje nos formulários de adição
document.addEventListener('DOMContentLoaded', function() {
    const dataInput = document.getElementById('data_consulta');
    if (dataInput) {
        const hoje = new Date().toISOString().split('T')[0];
        dataInput.min = hoje;
    }
});
</script>

<?php include 'includes/footer.php'; ?>