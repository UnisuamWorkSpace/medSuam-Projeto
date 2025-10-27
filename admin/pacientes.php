<?php
// pacientes.php
require_once 'includes/config.php';
checkAuth();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_paciente'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $cpf = $_POST['cpf'];
        $data_nasc = $_POST['data_nasc'];
        $sexo = $_POST['sexo'];
        $telefone = $_POST['telefone'];
        $peso = $_POST['peso'];
        $altura = $_POST['altura'];
        $tipo_sanguineo = $_POST['tipo_sanguineo'];
        $nome_social = $_POST['nome_social'];
        $estado_civil = $_POST['estado_civil'];
        
        // Dados do RG
        $numero_rg = $_POST['numero_rg'];
        $data_emissao_rg = $_POST['data_emissao_rg'];
        $orgao_emissor_rg = $_POST['orgao_emissor_rg'];
        $uf_rg = $_POST['uf_rg'];
        $data_validade_rg = $_POST['data_validade_rg'];

        // Verificar se email já existe
        $checkStmt = $pdo->prepare("SELECT id_paciente FROM paciente WHERE email_paciente = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = "Este email já está cadastrado!";
        } else {
            // Verificar se CPF já existe
            $checkStmt = $pdo->prepare("SELECT id_paciente FROM paciente WHERE cpf_paciente = ?");
            $checkStmt->execute([$cpf]);
            if ($checkStmt->fetch()) {
                $error = "Este CPF já está cadastrado!";
            } else {
                try {
                    $pdo->beginTransaction();

                    $stmt = $pdo->prepare("INSERT INTO paciente (nome_paciente, email_paciente, senha_paciente, cpf_paciente, data_nasc_paciente, sexo_paciente, peso, altura, tipo_sanguineo, nome_social_paciente, estado_civil) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$nome, $email, $senha, $cpf, $data_nasc, $sexo, $peso, $altura, $tipo_sanguineo, $nome_social, $estado_civil])) {
                        $paciente_id = $pdo->lastInsertId();
                        
                        // Inserir telefone se fornecido
                        if (!empty($telefone)) {
                            $telStmt = $pdo->prepare("INSERT INTO telefone (paciente_id_paciente, dd, telefone) VALUES (?, '55', ?)");
                            $telStmt->execute([$paciente_id, $telefone]);
                        }

                        // Inserir RG se fornecido
                        if (!empty($numero_rg)) {
                            $rgStmt = $pdo->prepare("INSERT INTO rg (paciente_id_paciente, numero_rg, data_emissao, orgao_emissor, uf_rg, data_validade) VALUES (?, ?, ?, ?, ?, ?)");
                            $rgStmt->execute([$paciente_id, $numero_rg, $data_emissao_rg, $orgao_emissor_rg, $uf_rg, $data_validade_rg]);
                        }
                        
                        $pdo->commit();
                        $success = "Paciente adicionado com sucesso!";
                        
                        // Registrar atividade
                        $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, paciente_id_paciente, descricao_atualizacao) VALUES (?, ?, ?)");
                        $stmt->execute([$_SESSION['admin_id'], $paciente_id, "Adicionou paciente: $nome"]);
                    } else {
                        $pdo->rollBack();
                        $error = "Erro ao adicionar paciente!";
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error = "Erro ao adicionar paciente: " . $e->getMessage();
                }
            }
        }
    }
    
    if (isset($_POST['editar_paciente'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $data_nasc = $_POST['data_nasc'];
        $sexo = $_POST['sexo'];
        $telefone = $_POST['telefone'];
        $peso = $_POST['peso'];
        $altura = $_POST['altura'];
        $tipo_sanguineo = $_POST['tipo_sanguineo'];
        $nome_social = $_POST['nome_social'];
        $estado_civil = $_POST['estado_civil'];
        
        // Dados do RG
        $numero_rg = $_POST['numero_rg'];
        $data_emissao_rg = $_POST['data_emissao_rg'];
        $orgao_emissor_rg = $_POST['orgao_emissor_rg'];
        $uf_rg = $_POST['uf_rg'];
        $data_validade_rg = $_POST['data_validade_rg'];

        // Verificar se email já existe (excluindo o próprio paciente)
        $checkStmt = $pdo->prepare("SELECT id_paciente FROM paciente WHERE email_paciente = ? AND id_paciente != ?");
        $checkStmt->execute([$email, $id]);
        if ($checkStmt->fetch()) {
            $error = "Este email já está cadastrado!";
        } else {
            // Verificar se CPF já existe (excluindo o próprio paciente)
            $checkStmt = $pdo->prepare("SELECT id_paciente FROM paciente WHERE cpf_paciente = ? AND id_paciente != ?");
            $checkStmt->execute([$cpf, $id]);
            if ($checkStmt->fetch()) {
                $error = "Este CPF já está cadastrado!";
            } else {
                try {
                    $pdo->beginTransaction();

                    $stmt = $pdo->prepare("UPDATE paciente SET nome_paciente = ?, email_paciente = ?, cpf_paciente = ?, data_nasc_paciente = ?, sexo_paciente = ?, peso = ?, altura = ?, tipo_sanguineo = ?, nome_social_paciente = ?, estado_civil = ? WHERE id_paciente = ?");
                    if ($stmt->execute([$nome, $email, $cpf, $data_nasc, $sexo, $peso, $altura, $tipo_sanguineo, $nome_social, $estado_civil, $id])) {
                        
                        // Atualizar telefone
                        if (!empty($telefone)) {
                            $checkTel = $pdo->prepare("SELECT id_telefone FROM telefone WHERE paciente_id_paciente = ?");
                            $checkTel->execute([$id]);
                            if ($checkTel->fetch()) {
                                $telStmt = $pdo->prepare("UPDATE telefone SET telefone = ? WHERE paciente_id_paciente = ?");
                                $telStmt->execute([$telefone, $id]);
                            } else {
                                $telStmt = $pdo->prepare("INSERT INTO telefone (paciente_id_paciente, dd, telefone) VALUES (?, '55', ?)");
                                $telStmt->execute([$id, $telefone]);
                            }
                        }

                        // Atualizar RG
                        if (!empty($numero_rg)) {
                            $checkRg = $pdo->prepare("SELECT id_rg FROM rg WHERE paciente_id_paciente = ?");
                            $checkRg->execute([$id]);
                            if ($checkRg->fetch()) {
                                $rgStmt = $pdo->prepare("UPDATE rg SET numero_rg = ?, data_emissao = ?, orgao_emissor = ?, uf_rg = ?, data_validade = ? WHERE paciente_id_paciente = ?");
                                $rgStmt->execute([$numero_rg, $data_emissao_rg, $orgao_emissor_rg, $uf_rg, $data_validade_rg, $id]);
                            } else {
                                $rgStmt = $pdo->prepare("INSERT INTO rg (paciente_id_paciente, numero_rg, data_emissao, orgao_emissor, uf_rg, data_validade) VALUES (?, ?, ?, ?, ?, ?)");
                                $rgStmt->execute([$id, $numero_rg, $data_emissao_rg, $orgao_emissor_rg, $uf_rg, $data_validade_rg]);
                            }
                        }
                        
                        $pdo->commit();
                        $success = "Paciente atualizado com sucesso!";
                        
                        // Registrar atividade
                        $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, paciente_id_paciente, descricao_atualizacao) VALUES (?, ?, ?)");
                        $stmt->execute([$_SESSION['admin_id'], $id, "Editou paciente: $nome"]);
                    } else {
                        $pdo->rollBack();
                        $error = "Erro ao atualizar paciente!";
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error = "Erro ao atualizar paciente: " . $e->getMessage();
                }
            }
        }
    }
    
    if (isset($_POST['alterar_status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE paciente SET status_paciente = ? WHERE id_paciente = ?");
        if ($stmt->execute([$status, $id])) {
            $success = "Status do paciente atualizado com sucesso!";
            
            // Registrar atividade
            $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, paciente_id_paciente, descricao_atualizacao) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['admin_id'], $id, "Alterou status do paciente para: $status"]);
        } else {
            $error = "Erro ao atualizar status!";
        }
    }
    
    if (isset($_POST['excluir_paciente'])) {
        $id = $_POST['id'];
        
        try {
            $pdo->beginTransaction();
            
            // Remover referências nas tabelas relacionadas
            $updateStmt = $pdo->prepare("UPDATE atualizacao_adm SET paciente_id_paciente = NULL WHERE paciente_id_paciente = ?");
            $updateStmt->execute([$id]);
            
            // Excluir telefones
            $telStmt = $pdo->prepare("DELETE FROM telefone WHERE paciente_id_paciente = ?");
            $telStmt->execute([$id]);
            
            // Excluir endereços
            $endStmt = $pdo->prepare("DELETE FROM endereco WHERE paciente_id_paciente = ?");
            $endStmt->execute([$id]);
            
            // Excluir RGs
            $rgStmt = $pdo->prepare("DELETE FROM rg WHERE paciente_id_paciente = ?");
            $rgStmt->execute([$id]);
            
            // Agora excluir o paciente
            $stmt = $pdo->prepare("DELETE FROM paciente WHERE id_paciente = ?");
            if ($stmt->execute([$id])) {
                $pdo->commit();
                $success = "Paciente excluído com sucesso!";
                
                // Registrar atividade
                $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                $stmt->execute([$_SESSION['admin_id'], "Excluiu paciente ID: $id"]);
            } else {
                $pdo->rollBack();
                $error = "Erro ao excluir paciente!";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao excluir paciente: " . $e->getMessage();
        }
    }
}

// Buscar pacientes com telefones e RG
$stmt = $pdo->query("
    SELECT p.*, t.telefone, r.numero_rg, r.data_emissao, r.orgao_emissor, r.uf_rg, r.data_validade
    FROM paciente p 
    LEFT JOIN telefone t ON p.id_paciente = t.paciente_id_paciente 
    LEFT JOIN rg r ON p.id_paciente = r.paciente_id_paciente
    ORDER BY p.nome_paciente
");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="header">
    <h2>Gerenciar Pacientes</h2>
    <button class="btn btn-primary" onclick="adminSystem.openModal('modalAdicionarPaciente')">
        <i class="fas fa-plus"></i> Adicionar Paciente
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
        <h3>Pacientes Cadastrados</h3>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Pesquisar pacientes...">
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Data Nasc.</th>
                <th>Sexo</th>
                <th>Telefone</th>
                <th>RG</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pacientes as $paciente): ?>
            <tr>
                <td><?php echo $paciente['id_paciente']; ?></td>
                <td><?php echo htmlspecialchars($paciente['nome_paciente']); ?></td>
                <td><?php echo htmlspecialchars($paciente['cpf_paciente']); ?></td>
                <td><?php echo htmlspecialchars($paciente['email_paciente']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($paciente['data_nasc_paciente'])); ?></td>
                <td><?php echo htmlspecialchars($paciente['sexo_paciente']); ?></td>
                <td><?php echo htmlspecialchars($paciente['telefone'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($paciente['numero_rg'] ?? '-'); ?></td>
                <td>
                    <span class="status <?php echo $paciente['status_paciente'] === 'ativo' ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo $paciente['status_paciente']; ?>
                    </span>
                </td>
                <td class="actions">
                    <button class="btn btn-primary btn-sm" onclick="editarPaciente(<?php echo htmlspecialchars(json_encode($paciente)); ?>)">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $paciente['id_paciente']; ?>">
                        <input type="hidden" name="status" value="<?php echo $paciente['status_paciente'] === 'ativo' ? 'inativo' : 'ativo'; ?>">
                        <button type="submit" name="alterar_status" class="btn <?php echo $paciente['status_paciente'] === 'ativo' ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                            <i class="fas <?php echo $paciente['status_paciente'] === 'ativo' ? 'fa-pause' : 'fa-play'; ?>"></i>
                            <?php echo $paciente['status_paciente'] === 'ativo' ? 'Inativar' : 'Ativar'; ?>
                        </button>
                    </form>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $paciente['id_paciente']; ?>">
                        <button type="submit" name="excluir_paciente" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita.')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Adicionar Paciente -->
<div id="modalAdicionarPaciente" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adicionar Paciente</h3>
            <span class="close">&times;</span>
        </div>
        <form method="POST">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_nasc">Data de Nascimento:</label>
                        <input type="date" id="data_nasc" name="data_nasc" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sexo">Sexo:</label>
                        <select id="sexo" name="sexo" class="form-control" required>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="peso">Peso (kg):</label>
                        <input type="number" id="peso" name="peso" class="form-control" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="altura">Altura (m):</label>
                        <input type="number" id="altura" name="altura" class="form-control" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_sanguineo">Tipo Sanguíneo:</label>
                        <input type="text" id="tipo_sanguineo" name="tipo_sanguineo" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="nome_social">Nome Social:</label>
                        <input type="text" id="nome_social" name="nome_social" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="estado_civil">Estado Civil:</label>
                        <input type="text" id="estado_civil" name="estado_civil" class="form-control">
                    </div>

                    <!-- Campos do RG -->
                    <div class="form-group">
                        <label for="numero_rg">Número do RG:</label>
                        <input type="text" id="numero_rg" name="numero_rg" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_emissao_rg">Data de Emissão do RG:</label>
                        <input type="date" id="data_emissao_rg" name="data_emissao_rg" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="orgao_emissor_rg">Órgão Emissor do RG:</label>
                        <input type="text" id="orgao_emissor_rg" name="orgao_emissor_rg" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="uf_rg">UF do RG:</label>
                        <input type="text" id="uf_rg" name="uf_rg" class="form-control" maxlength="2">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_validade_rg">Data de Validade do RG:</label>
                        <input type="date" id="data_validade_rg" name="data_validade_rg" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="adicionar_paciente" class="btn btn-primary">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Paciente -->
<div id="modalEditarPaciente" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Paciente</h3>
            <span class="close">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" id="edit_id" name="id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_nome">Nome Completo:</label>
                        <input type="text" id="edit_nome" name="nome" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_email">Email:</label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_cpf">CPF:</label>
                        <input type="text" id="edit_cpf" name="cpf" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_data_nasc">Data de Nascimento:</label>
                        <input type="date" id="edit_data_nasc" name="data_nasc" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_sexo">Sexo:</label>
                        <select id="edit_sexo" name="sexo" class="form-control" required>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_telefone">Telefone:</label>
                        <input type="text" id="edit_telefone" name="telefone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_peso">Peso (kg):</label>
                        <input type="number" id="edit_peso" name="peso" class="form-control" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_altura">Altura (m):</label>
                        <input type="number" id="edit_altura" name="altura" class="form-control" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_tipo_sanguineo">Tipo Sanguíneo:</label>
                        <input type="text" id="edit_tipo_sanguineo" name="tipo_sanguineo" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_nome_social">Nome Social:</label>
                        <input type="text" id="edit_nome_social" name="nome_social" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_estado_civil">Estado Civil:</label>
                        <input type="text" id="edit_estado_civil" name="estado_civil" class="form-control">
                    </div>

                    <!-- Campos do RG -->
                    <div class="form-group">
                        <label for="edit_numero_rg">Número do RG:</label>
                        <input type="text" id="edit_numero_rg" name="numero_rg" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_data_emissao_rg">Data de Emissão do RG:</label>
                        <input type="date" id="edit_data_emissao_rg" name="data_emissao_rg" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_orgao_emissor_rg">Órgão Emissor do RG:</label>
                        <input type="text" id="edit_orgao_emissor_rg" name="orgao_emissor_rg" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_uf_rg">UF do RG:</label>
                        <input type="text" id="edit_uf_rg" name="uf_rg" class="form-control" maxlength="2">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_data_validade_rg">Data de Validade do RG:</label>
                        <input type="date" id="edit_data_validade_rg" name="data_validade_rg" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="editar_paciente" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarPaciente(paciente) {
    document.getElementById('edit_id').value = paciente.id_paciente;
    document.getElementById('edit_nome').value = paciente.nome_paciente;
    document.getElementById('edit_email').value = paciente.email_paciente;
    document.getElementById('edit_cpf').value = paciente.cpf_paciente;
    document.getElementById('edit_data_nasc').value = paciente.data_nasc_paciente;
    document.getElementById('edit_sexo').value = paciente.sexo_paciente;
    document.getElementById('edit_telefone').value = paciente.telefone || '';
    document.getElementById('edit_peso').value = paciente.peso || '';
    document.getElementById('edit_altura').value = paciente.altura || '';
    document.getElementById('edit_tipo_sanguineo').value = paciente.tipo_sanguineo || '';
    document.getElementById('edit_nome_social').value = paciente.nome_social_paciente || '';
    document.getElementById('edit_estado_civil').value = paciente.estado_civil || '';
    document.getElementById('edit_numero_rg').value = paciente.numero_rg || '';
    document.getElementById('edit_data_emissao_rg').value = paciente.data_emissao || '';
    document.getElementById('edit_orgao_emissor_rg').value = paciente.orgao_emissor || '';
    document.getElementById('edit_uf_rg').value = paciente.uf_rg || '';
    document.getElementById('edit_data_validade_rg').value = paciente.data_validade || '';
    
    adminSystem.openModal('modalEditarPaciente');
}
</script>

<?php include 'includes/footer.php'; ?>