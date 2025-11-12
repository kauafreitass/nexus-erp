<?php
// O Controller define:
// $activePage
// $user (null se for 'create', array se for 'edit')
// $roles (array com todos os perfis)
// $action (a URL do form: .../store ou .../update)

$isEditMode = !empty($user);
$title = $isEditMode ? "Editar Usuário" : "Novo Usuário";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
            --borda-campo: #B0B0B0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h1 { font-size: 2rem; font-weight: 700; }
        .btn-voltar { display: inline-block; background-color: #6c757d; color: #FFFFFF; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; }
        .btn-primary { padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 1rem; color: #FFFFFF; background-color: var(--azul-header); border: none; cursor: pointer; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07); margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; font-size: 1.1rem; font-weight: 600; background-color: var(--azul-header); color: #FFFFFF; }
        .form-container { padding: 24px; }
        .form-grid { display: grid; gap: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; width: 100%; }
        .form-group label { font-size: 0.875rem; font-weight: 600; color: var(--texto-label); margin-bottom: 8px; }
        .form-group input, .form-group select {
            font-size: 1rem; color: var(--texto-principal); padding: 12px 16px; 
            border: 1px solid var(--borda-campo); border-radius: 8px; 
            background-color: #FFFFFF; width: 100%;
        }
        @media (max-width: 900px) {
            .container { margin-left: 0; padding: 16px; }
            .form-row, .form-row-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
        <div class="page-header">
            <h1><?php echo $title; ?></h1>
            <a href="/nexus-erp/public/users" class="btn-voltar">Voltar</a>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="<?php echo $action; ?>">
            <?php if ($isEditMode): ?>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <?php endif; ?>

            <div class="card">
                <div class="card-header">Informações de Acesso</div>
                <div class="form-container form-grid">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nome*</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail*</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Senha*</label>
                            <input type="password" id="password" name="password" <?php echo $isEditMode ? '' : 'required'; ?>>
                            <?php if ($isEditMode): ?>
                                <small>Deixe em branco para não alterar a senha.</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Perfil (Role)*</label>
                            <select id="role_id" name="role_id" required>
                                <option value="">Selecione um perfil</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" <?php echo (($user['role_id'] ?? '') == $role['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status">Status*</label>
                        <select id="status" name="status" required>
                            <option value="ACTIVE" <?php echo (($user['status'] ?? 'ACTIVE') == 'ACTIVE') ? 'selected' : ''; ?>>Ativo</option>
                            <option value="DISABLED" <?php echo (($user['status'] ?? '') == 'DISABLED') ? 'selected' : ''; ?>>Desativado</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">Dados da Empresa/Usuário (Opcional)</div>
                <div class="form-container form-grid">
                    <div class="form-group">
                        <label for="business_name">Razão Social (Se aplicável)</label>
                        <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($user['business_name'] ?? ''); ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="document_number">Documento (CPF/CNPJ)</label>
                            <input type="text" id="document_number" name="document_number" value="<?php echo htmlspecialchars($user['document_number'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 16px 24px;"><?php echo $isEditMode ? 'Salvar Alterações' : 'Criar Usuário'; ?></button>
            </div>
        </form>
    </div>
</body>
</html>