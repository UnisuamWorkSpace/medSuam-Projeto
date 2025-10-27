<?php
// medicos.php
require_once 'includes/config.php';
checkAuth();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_medico'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $crm = $_POST['crm'];
        $cpf = $_POST['cpf'];
        $data_nasc = $_POST['data_nasc'];
        $sexo = $_POST['sexo'];
        $telefone = $_POST['telefone'];
        $nome_social = $_POST['nome_social'];
        
        // Dados do RG
        $numero_rg = $_POST['numero_rg'];
        $data_emissao_rg = $_POST['data_emissao_rg'];
        $orgao_emissor_rg = $_POST['orgao_emissor_rg'];
        $uf_rg = $_POST['uf_rg'];
        $data_validade_rg = $_POST['data_validade_rg'];

        // Verificar se email já existe
        $checkStmt = $pdo->prepare("SELECT id_medico FROM medico WHERE email_medico = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = "Este email já está cadastrado!";
        } else {
            // Verificar se CRM já existe
            $checkStmt = $pdo->prepare("SELECT id_medico FROM medico WHERE crm = ?");
            $checkStmt->execute([$crm]);
            if ($checkStmt->fetch()) {
                $error = "Este CRM já está cadastrado!";
            } else {
                // Verificar se CPF já existe
                $checkStmt = $pdo->prepare("SELECT id_medico FROM medico WHERE cpf_medico = ?");
                $checkStmt->execute([$cpf]);
                if ($checkStmt->fetch()) {
                    $error = "Este CPF já está cadastrado!";
                } else {
                    try {
                        $pdo->beginTransaction();

                        $stmt = $pdo->prepare("INSERT INTO medico (nome_medico, email_medico, senha_medico, crm, cpf_medico, data_nasc_medico, sexo_medico, nome_social_medico, status_medico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ativo')");
                        if ($stmt->execute([$nome, $email, $senha, $crm, $cpf, $data_nasc, $sexo, $nome_social])) {
                            $medico_id = $pdo->lastInsertId();
                            
                            // Inserir telefone se fornecido
                            if (!empty($telefone)) {
                                $telStmt = $pdo->prepare("INSERT INTO telefone (medico_id_medico, dd, telefone) VALUES (?, '55', ?)");
                                $telStmt->execute([$medico_id, $telefone]);
                            }

                            // Inserir RG se fornecido
                            if (!empty($numero_rg)) {
                                $rgStmt = $pdo->prepare("INSERT INTO rg (medico_id_medico, numero_rg, data_emissao, orgao_emissor, uf_rg, data_validade) VALUES (?, ?, ?, ?, ?, ?)");
                                $rgStmt->execute([$medico_id, $numero_rg, $data_emissao_rg, $orgao_emissor_rg, $uf_rg, $data_validade_rg]);
                            }
                            
                            $pdo->commit();
                            $success = "Médico adicionado com sucesso!";
                            
                            // Registrar atividade
                            $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, medico_id_medico, descricao_atualizacao) VALUES (?, ?, ?)");
                            $stmt->execute([$_SESSION['admin_id'], $medico_id, "Adicionou médico: $nome"]);
                        } else {
                            $pdo->rollBack();
                            $error = "Erro ao adicionar médico!";
                        }
                    } catch (PDOException $e) {
                        $pdo->rollBack();
                        $error = "Erro ao adicionar médico: " . $e->getMessage();
                    }
                }
            }
        }
    }
    
    if (isset($_POST['editar_medico'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $crm = $_POST['crm'];
        $cpf = $_POST['cpf'];
        $data_nasc = $_POST['data_nasc'];
        $sexo = $_POST['sexo'];
        $telefone = $_POST['telefone'];
        $nome_social = $_POST['nome_social'];
        
        // Dados do RG
        $numero_rg = $_POST['numero_rg'];
        $data_emissao_rg = $_POST['data_emissao_rg'];
        $orgao_emissor_rg = $_POST['orgao_emissor_rg'];
        $uf_rg = $_POST['uf_rg'];
        $data_validade_rg = $_POST['data_validade_rg'];

        // Verificar se email já existe (excluindo o próprio médico)
        $checkStmt = $pdo->prepare("SELECT id_medico FROM medico WHERE email_medico = ? AND id_medico != ?");
        $checkStmt->execute([$email, $id]);
        if ($checkStmt->fetch()) {
            $error = "Este email já está cadastrado!";
        } else {
            // Verificar se CRM já existe (excluindo o próprio médico)
            $checkStmt = $pdo->prepare("SELECT id_medico FROM medico WHERE crm = ? AND id_medico != ?");
            $checkStmt->execute([$crm, $id]);
            if ($checkStmt->fetch()) {
                $error = "Este CRM já está cadastrado!";
            } else {
                // Verificar se CPF já existe (excluindo o próprio médico)
                $checkStmt = $pdo->prepare("SELECT id_medico FROM medico WHERE cpf_medico = ? AND id_medico != ?");
                $checkStmt->execute([$cpf, $id]);
                if ($checkStmt->fetch()) {
                    $error = "Este CPF já está cadastrado!";
                } else {
                    try {
                        $pdo->beginTransaction();

                        $stmt = $pdo->prepare("UPDATE medico SET nome_medico = ?, email_medico = ?, crm = ?, cpf_medico = ?, data_nasc_medico = ?, sexo_medico = ?, nome_social_medico = ? WHERE id_medico = ?");
                        if ($stmt->execute([$nome, $email, $crm, $cpf, $data_nasc, $sexo, $nome_social, $id])) {
                            
                            // Atualizar telefone
                            if (!empty($telefone)) {
                                $checkTel = $pdo->prepare("SELECT id_telefone FROM telefone WHERE medico_id_medico = ?");
                                $checkTel->execute([$id]);
                                if ($checkTel->fetch()) {
                                    $telStmt = $pdo->prepare("UPDATE telefone SET telefone = ? WHERE medico_id_medico = ?");
                                    $telStmt->execute([$telefone, $id]);
                                } else {
                                    $telStmt = $pdo->prepare("INSERT INTO telefone (medico_id_medico, dd, telefone) VALUES (?, '55', ?)");
                                    $telStmt->execute([$id, $telefone]);
                                }
                            }

                            // Atualizar RG
                            if (!empty($numero_rg)) {
                                $checkRg = $pdo->prepare("SELECT id_rg FROM rg WHERE medico_id_medico = ?");
                                $checkRg->execute([$id]);
                                if ($checkRg->fetch()) {
                                    $rgStmt = $pdo->prepare("UPDATE rg SET numero_rg = ?, data_emissao = ?, orgao_emissor = ?, uf_rg = ?, data_validade = ? WHERE medico_id_medico = ?");
                                    $rgStmt->execute([$numero_rg, $data_emissao_rg, $orgao_emissor_rg, $uf_rg, $data_validade_rg, $id]);
                                } else {
                                    $rgStmt = $pdo->prepare("INSERT INTO rg (medico_id_medico, numero_rg, data_emissao, orgao_emissor, uf_rg, data_validade) VALUES (?, ?, ?, ?, ?, ?)");
                                    $rgStmt->execute([$id, $numero_rg, $data_emissao_rg, $orgao_emissor_rg, $uf_rg, $data_validade_rg]);
                                }
                            }
                            
                            $pdo->commit();
                            $success = "Médico atualizado com sucesso!";
                            
                            // Registrar atividade
                            $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, medico_id_medico, descricao_atualizacao) VALUES (?, ?, ?)");
                            $stmt->execute([$_SESSION['admin_id'], $id, "Editou médico: $nome"]);
                        } else {
                            $pdo->rollBack();
                            $error = "Erro ao atualizar médico!";
                        }
                    } catch (PDOException $e) {
                        $pdo->rollBack();
                        $error = "Erro ao atualizar médico: " . $e->getMessage();
                    }
                }
            }
        }
    }
    
    if (isset($_POST['alterar_senha_medico'])) {
        $id = $_POST['id'];
        $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE medico SET senha_medico = ? WHERE id_medico = ?");
        if ($stmt->execute([$nova_senha, $id])) {
            $success = "Senha do médico alterada com sucesso!";
            
            // Registrar atividade
            $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, medico_id_medico, descricao_atualizacao) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['admin_id'], $id, "Alterou senha do médico ID: $id"]);
        } else {
            $error = "Erro ao alterar senha do médico!";
        }
    }
    
    if (isset($_POST['alterar_status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE medico SET status_medico = ? WHERE id_medico = ?");
        if ($stmt->execute([$status, $id])) {
            $success = "Status do médico atualizado com sucesso!";
            
            // Registrar atividade
            $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, medico_id_medico, descricao_atualizacao) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['admin_id'], $id, "Alterou status do médico para: $status"]);
        } else {
            $error = "Erro ao atualizar status!";
        }
    }
    
    if (isset($_POST['excluir_medico'])) {
        $id = $_POST['id'];
        
        try {
            $pdo->beginTransaction();
            
            // Remover referências nas tabelas relacionadas
            $updateStmt = $pdo->prepare("UPDATE atualizacao_adm SET medico_id_medico = NULL WHERE medico_id_medico = ?");
            $updateStmt->execute([$id]);
            
            // Remover referências em consulta
            $consultaStmt = $pdo->prepare("UPDATE consulta SET id_medico = NULL WHERE id_medico = ?");
            $consultaStmt->execute([$id]);
            
            // Excluir telefones
            $telStmt = $pdo->prepare("DELETE FROM telefone WHERE medico_id_medico = ?");
            $telStmt->execute([$id]);
            
            // Excluir endereços
            $endStmt = $pdo->prepare("DELETE FROM endereco WHERE medico_id_medico = ?");
            $endStmt->execute([$id]);
            
            // Excluir RGs
            $rgStmt = $pdo->prepare("DELETE FROM rg WHERE medico_id_medico = ?");
            $rgStmt->execute([$id]);
            
            // Excluir especialidades
            $espStmt = $pdo->prepare("DELETE FROM especialidade WHERE id_medico = ?");
            $espStmt->execute([$id]);
            
            // Agora excluir o médico
            $stmt = $pdo->prepare("DELETE FROM medico WHERE id_medico = ?");
            if ($stmt->execute([$id])) {
                $pdo->commit();
                $success = "Médico excluído com sucesso!";
                
                // Registrar atividade
                $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                $stmt->execute([$_SESSION['admin_id'], "Excluiu médico ID: $id"]);
            } else {
                $pdo->rollBack();
                $error = "Erro ao excluir médico!";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erro ao excluir médico: " . $e->getMessage();
        }
    }
}

// Buscar médicos com telefones e RG
$stmt = $pdo->query("
    SELECT m.*, t.telefone, r.numero_rg, r.data_emissao, r.orgao_emissor, r.uf_rg, r.data_validade
    FROM medico m 
    LEFT JOIN telefone t ON m.id_medico = t.medico_id_medico 
    LEFT JOIN rg r ON m.id_medico = r.medico_id_medico
    ORDER BY m.nome_medico
");
$medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="header">
    <h2>Gerenciar Médicos</h2>
    <button class="btn btn-primary" onclick="adminSystem.openModal('modalAdicionarMedico')">
        <i class="fas fa-plus"></i> Adicionar Médico
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
        <h3>Médicos Cadastrados</h3>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Pesquisar médicos...">
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CRM</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Data Nasc.</th>
                <th>Sexo</th>
                <th>Telefone</th>
                <th>RG</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicos as $medico): ?>
            <tr>
                <td><?php echo $medico['id_medico']; ?></td>
                <td><?php echo htmlspecialchars($medico['nome_medico']); ?></td>
                <td><?php echo htmlspecialchars($medico['crm']); ?></td>
                <td><?php echo htmlspecialchars($medico['email_medico']); ?></td>
                <td><?php echo htmlspecialchars($medico['cpf_medico']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($medico['data_nasc_medico'])); ?></td>
                <td><?php echo htmlspecialchars($medico['sexo_medico']); ?></td>
                <td><?php echo htmlspecialchars($medico['telefone'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($medico['numero_rg'] ?? '-'); ?></td>
                <td>
                    <span class="status <?php echo $medico['status_medico'] === 'ativo' ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo $medico['status_medico']; ?>
                    </span>
                </td>
                <td class="actions">
                    <button class="btn btn-primary btn-sm" onclick="editarMedico(<?php echo htmlspecialchars(json_encode($medico)); ?>)">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    
                    <button class="btn btn-warning btn-sm" onclick="alterarSenhaMedico(<?php echo $medico['id_medico']; ?>)">
                        <i class="fas fa-key"></i> Senha
                    </button>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $medico['id_medico']; ?>">
                        <input type="hidden" name="status" value="<?php echo $medico['status_medico'] === 'ativo' ? 'inativo' : 'ativo'; ?>">
                        <button type="submit" name="alterar_status" class="btn <?php echo $medico['status_medico'] === 'ativo' ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                            <i class="fas <?php echo $medico['status_medico'] === 'ativo' ? 'fa-pause' : 'fa-play'; ?>"></i>
                            <?php echo $medico['status_medico'] === 'ativo' ? 'Inativar' : 'Ativar'; ?>
                        </button>
                    </form>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $medico['id_medico']; ?>">
                        <button type="submit" name="excluir_medico" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Tem certeza que deseja excluir este médico? Esta ação não pode ser desfeita.')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Adicionar Médico -->
<div id="modalAdicionarMedico" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adicionar Médico</h3>
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
                        <label for="crm">CRM:</label>
                        <input type="text" id="crm" name="crm" class="form-control" required>
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
                        <label for="nome_social">Nome Social:</label>
                        <input type="text" id="nome_social" name="nome_social" class="form-control">
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
                <button type="submit" name="adicionar_medico" class="btn btn-primary">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Médico -->
<div id="modalEditarMedico" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Médico</h3>
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
                        <label for="edit_crm">CRM:</label>
                        <input type="text" id="edit_crm" name="crm" class="form-control" required>
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
                        <label for="edit_nome_social">Nome Social:</label>
                        <input type="text" id="edit_nome_social" name="nome_social" class="form-control">
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
                <button type="submit" name="editar_medico" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Alterar Senha Médico -->
<div id="modalAlterarSenhaMedico" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Alterar Senha do Médico</h3>
            <span class="close">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" id="senha_medico_id" name="id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="nova_senha_medico">Nova Senha:</label>
                    <input type="password" id="nova_senha_medico" name="nova_senha" class="form-control" required minlength="6">
                    <small style="color: #666;">A senha deve ter pelo menos 6 caracteres</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="alterar_senha_medico" class="btn btn-primary">Alterar Senha</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarMedico(medico) {
    document.getElementById('edit_id').value = medico.id_medico;
    document.getElementById('edit_nome').value = medico.nome_medico;
    document.getElementById('edit_email').value = medico.email_medico;
    document.getElementById('edit_crm').value = medico.crm;
    document.getElementById('edit_cpf').value = medico.cpf_medico;
    document.getElementById('edit_data_nasc').value = medico.data_nasc_medico;
    document.getElementById('edit_sexo').value = medico.sexo_medico;
    document.getElementById('edit_telefone').value = medico.telefone || '';
    document.getElementById('edit_nome_social').value = medico.nome_social_medico || '';
    document.getElementById('edit_numero_rg').value = medico.numero_rg || '';
    document.getElementById('edit_data_emissao_rg').value = medico.data_emissao || '';
    document.getElementById('edit_orgao_emissor_rg').value = medico.orgao_emissor || '';
    document.getElementById('edit_uf_rg').value = medico.uf_rg || '';
    document.getElementById('edit_data_validade_rg').value = medico.data_validade || '';
    
    adminSystem.openModal('modalEditarMedico');
}

function alterarSenhaMedico(id) {
    document.getElementById('senha_medico_id').value = id;
    adminSystem.openModal('modalAlterarSenhaMedico');
}
</script>

<?php include 'includes/footer.php'; ?>