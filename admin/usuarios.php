<?php
// usuarios.php
require_once 'includes/config.php';
checkAuth();

// Verificar se é super admin
if (!isSuperAdmin()) {
    header('Location: dashboard.php');
    exit;
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_admin'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $cpf = $_POST['cpf'];
        $data_nasc = $_POST['data_nasc'];
        $nivel_acesso = $_POST['nivel_acesso'];
        
        // Verificar se email já existe
        $checkStmt = $pdo->prepare("SELECT id_adm FROM adm WHERE email_adm = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = "Este email já está cadastrado!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO adm (nome_adm, email_adm, senha_adm, cpf_adm, data_nasc_adm, nivel_acesso, data_criacao) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            if ($stmt->execute([$nome, $email, $senha, $cpf, $data_nasc, $nivel_acesso])) {
                $success = "Administrador adicionado com sucesso!";
                
                // Registrar atividade
                $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                $stmt->execute([$_SESSION['admin_id'], "Adicionou administrador: $nome"]);
            } else {
                $error = "Erro ao adicionar administrador!";
            }
        }
    }
    
    if (isset($_POST['editar_admin'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $data_nasc = $_POST['data_nasc'];
        $nivel_acesso = $_POST['nivel_acesso'];
        
        // Verificar se email já existe (excluindo o próprio usuário)
        $checkStmt = $pdo->prepare("SELECT id_adm FROM adm WHERE email_adm = ? AND id_adm != ?");
        $checkStmt->execute([$email, $id]);
        if ($checkStmt->fetch()) {
            $error = "Este email já está cadastrado!";
        } else {
            $stmt = $pdo->prepare("UPDATE adm SET nome_adm = ?, email_adm = ?, cpf_adm = ?, data_nasc_adm = ?, nivel_acesso = ? WHERE id_adm = ?");
            if ($stmt->execute([$nome, $email, $cpf, $data_nasc, $nivel_acesso, $id])) {
                $success = "Administrador atualizado com sucesso!";
                
                // Registrar atividade
                $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                $stmt->execute([$_SESSION['admin_id'], "Editou administrador: $nome"]);
            } else {
                $error = "Erro ao atualizar administrador!";
            }
        }
    }
    
    if (isset($_POST['alterar_senha'])) {
        $id = $_POST['id'];
        $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE adm SET senha_adm = ? WHERE id_adm = ?");
        if ($stmt->execute([$nova_senha, $id])) {
            $success = "Senha alterada com sucesso!";
            
            // Registrar atividade
            $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
            $stmt->execute([$_SESSION['admin_id'], "Alterou senha do administrador ID: $id"]);
        } else {
            $error = "Erro ao alterar senha!";
        }
    }
    
    if (isset($_POST['excluir_admin'])) {
        $id = $_POST['id'];
        
        // Não permitir excluir a si mesmo
        if ($id == $_SESSION['admin_id']) {
            $error = "Você não pode excluir sua própria conta!";
        } else {
            try {
                // Iniciar transação para garantir consistência
                $pdo->beginTransaction();
                
                // Primeiro, atualizar os registros em atualizacao_adm que referenciam este admin
                // Definir como NULL os registros onde este admin é referenciado
                $updateStmt = $pdo->prepare("UPDATE atualizacao_adm SET adm_id_adm = NULL WHERE adm_id_adm = ?");
                $updateStmt->execute([$id]);
                
                // Agora excluir o administrador
                $stmt = $pdo->prepare("DELETE FROM adm WHERE id_adm = ?");
                if ($stmt->execute([$id])) {
                    $pdo->commit();
                    $success = "Administrador excluído com sucesso!";
                    
                    // Registrar atividade
                    $stmt = $pdo->prepare("INSERT INTO atualizacao_adm (adm_id_adm, descricao_atualizacao) VALUES (?, ?)");
                    $stmt->execute([$_SESSION['admin_id'], "Excluiu administrador ID: $id"]);
                } else {
                    $pdo->rollBack();
                    $error = "Erro ao excluir administrador!";
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Erro ao excluir administrador: " . $e->getMessage();
            }
        }
    }
}

// Buscar administradores
$stmt = $pdo->query("SELECT * FROM adm ORDER BY data_criacao DESC");
$administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="header">
    <h2>Gerenciar Administradores</h2>
    <button class="btn btn-primary" onclick="adminSystem.openModal('modalAdicionarAdmin')">
        <i class="fas fa-plus"></i> Adicionar Admin
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
        <h3>Administradores do Sistema</h3>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Pesquisar administradores...">
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Nível Acesso</th>
                <th>Data Criação</th>
                <th>Último Login</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($administradores as $admin): ?>
            <tr>
                <td><?php echo $admin['id_adm']; ?></td>
                <td><?php echo htmlspecialchars($admin['nome_adm']); ?></td>
                <td><?php echo htmlspecialchars($admin['email_adm']); ?></td>
                <td><?php echo htmlspecialchars($admin['cpf_adm']); ?></td>
                <td>
                    <span class="status <?php echo $admin['nivel_acesso'] === 'super' ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo $admin['nivel_acesso']; ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($admin['data_criacao'])); ?></td>
                <td><?php echo $admin['ultimo_login'] ? date('d/m/Y H:i', strtotime($admin['ultimo_login'])) : 'Nunca'; ?></td>
                <td class="actions">
                    <button class="btn btn-primary btn-sm" onclick="editarAdmin(<?php echo htmlspecialchars(json_encode($admin)); ?>)">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    
                    <button class="btn btn-warning btn-sm" onclick="alterarSenha(<?php echo $admin['id_adm']; ?>)">
                        <i class="fas fa-key"></i> Senha
                    </button>
                    
                    <?php if ($admin['id_adm'] != $_SESSION['admin_id']): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $admin['id_adm']; ?>">
                        <button type="submit" name="excluir_admin" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Tem certeza que deseja excluir este administrador? Esta ação não pode ser desfeita.')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                    <?php else: ?>
                    <span class="btn btn-sm" style="background: #ccc; color: #666;">Você</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Adicionar Admin -->
<div id="modalAdicionarAdmin" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adicionar Administrador</h3>
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
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_nasc">Data de Nascimento:</label>
                        <input type="date" id="data_nasc" name="data_nasc" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="nivel_acesso">Nível de Acesso:</label>
                        <select id="nivel_acesso" name="nivel_acesso" class="form-control" required>
                            <option value="adm">Administrador</option>
                            <option value="super">Super Administrador</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="adicionar_admin" class="btn btn-primary">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Admin -->
<div id="modalEditarAdmin" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Administrador</h3>
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
                        <label for="edit_nivel_acesso">Nível de Acesso:</label>
                        <select id="edit_nivel_acesso" name="nivel_acesso" class="form-control" required>
                            <option value="adm">Administrador</option>
                            <option value="super">Super Administrador</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="editar_admin" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Alterar Senha -->
<div id="modalAlterarSenha" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Alterar Senha</h3>
            <span class="close">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" id="senha_id" name="id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="nova_senha">Nova Senha:</label>
                    <input type="password" id="nova_senha" name="nova_senha" class="form-control" required minlength="6">
                    <small style="color: #666;">A senha deve ter pelo menos 6 caracteres</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancel-btn">Cancelar</button>
                <button type="submit" name="alterar_senha" class="btn btn-primary">Alterar Senha</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarAdmin(admin) {
    document.getElementById('edit_id').value = admin.id_adm;
    document.getElementById('edit_nome').value = admin.nome_adm;
    document.getElementById('edit_email').value = admin.email_adm;
    document.getElementById('edit_cpf').value = admin.cpf_adm;
    document.getElementById('edit_data_nasc').value = admin.data_nasc_adm;
    document.getElementById('edit_nivel_acesso').value = admin.nivel_acesso;
    
    adminSystem.openModal('modalEditarAdmin');
}

function alterarSenha(id) {
    document.getElementById('senha_id').value = id;
    adminSystem.openModal('modalAlterarSenha');
}
</script>

<?php include 'includes/footer.php'; ?>