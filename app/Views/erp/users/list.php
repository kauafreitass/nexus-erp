<?php
// O Controller define:
// $activePage
// $users (array)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; font-size: 2rem; font-weight: 700; color: var(--texto-principal); margin-bottom: 24px; }
        .btn-primary { display: inline-block; padding: 10px 16px; background-color: var(--azul-header); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 1rem; border: none; cursor: pointer; transition: background-color 0.2s; }
        .btn-primary:hover { background-color: var(--azul-active); }
        .btn-danger { display: inline-block; padding: 5px 10px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 15px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; margin-left: 8px; }
        .btn-danger:hover { background-color: #c82333; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07); overflow: hidden; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        table th, table td { white-space: nowrap; padding: 16px 20px; }
        table thead th { background-color: var(--azul-header); color: #FFFFFF; text-align: left; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; }
        table tbody tr { border-bottom: 1px solid #EEE; }
        table tbody tr:last-child { border-bottom: none; }
        table tbody td { font-size: 0.95rem; color: var(--texto-label); }
        table tbody td:first-child { color: var(--texto-principal); font-weight: 500; }
        .action-button-style { display: inline-block; padding: 5px 10px; background-color: #4a4a4a; color: white; text-decoration: none; border-radius: 15px; font-size: 13px; font-weight: bold; }
        .action-button-style:hover { background-color: #666; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
        .status-ACTIVE { background-color: #D1FAE5; color: #065F46; }
        .status-DISABLED { background-color: #FEE2E2; color: #991B1B; }
        @media (max-width: 900px) { .container { margin-left: 0; padding: 16px; } }
    </style>
</head>
        
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
    
    <div class="page-header">
        <h1>Gerenciar Usuários</h1>
        <a href="/nexus-erp/public/users/create" class="btn-primary">
            Novo Usuário
        </a>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div style="color: green; padding: 10px; border: 1px solid green;"><?php echo $_SESSION['message']; ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div style="color: red; padding: 10px; border: 1px solid red;"><?php echo $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil (Role)</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $user['status']; ?>">
                                        <?php echo ($user['status'] == 'ACTIVE') ? 'Ativo' : 'Desativado'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/nexus-erp/public/users/edit?id=<?php echo $user['id']; ?>" class="action-button-style">
                                        Editar
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user']['id']): // Não pode excluir a si mesmo ?>
                                        <form action="/nexus-erp/public/users/delete" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza? Esta ação é IRREVERSÍVEL e excluirá todos os dados deste usuário.');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn-danger">Excluir</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>