<?php
// O Controller define:
// $activePage
// $categories (array)
// $editCategory (array|null)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias de Produtos</title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
            --borda-campo: #B0B0B0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; font-size: 2rem; font-weight: 700; color: var(--texto-principal); margin-bottom: 24px; }
        .btn-primary { display: inline-block; padding: 10px 16px; background-color: var(--azul-header); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 1rem; border: none; cursor: pointer; transition: background-color 0.2s; }
        .btn-secondary { display: inline-block; padding: 10px 16px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 1rem; border: none; cursor: pointer; }
        .btn-danger-sm { padding: 5px 10px; background-color: #dc3545; color: white; text-decoration: none; border: none; border-radius: 5px; font-size: 13px; cursor: pointer; }
        .btn-warning-sm { display: inline-block; padding: 5px 10px; background-color: #ffc107; color: #212529; text-decoration: none; border-radius: 5px; font-size: 13px; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #EEE; font-size: 1.1rem; font-weight: 600; background-color: var(--azul-header); color: #FFFFFF; }
        .card-body { padding: 24px; }
        .form-row { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; width: 100%; }
        .form-group label { font-size: 0.875rem; font-weight: 600; color: var(--texto-label); margin-bottom: 8px; }
        .form-group input, .form-group select { font-size: 1rem; color: var(--texto-principal); padding: 12px 16px; border: 1px solid var(--borda-campo); border-radius: 8px; background-color: #FFFFFF; width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 16px 20px; white-space: nowrap; }
        table thead th { background-color: #f8f9fa; color: var(--texto-label); text-align: left; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; border-bottom: 2px solid #EEE; }
        table tbody tr { border-bottom: 1px solid #EEE; }
        table tbody tr:last-child { border-bottom: none; }
        table tbody td { font-size: 0.95rem; color: var(--texto-label); }
        .badge { padding: 4px 8px; border-radius: 10px; font-size: 0.8rem; font-weight: 600; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-secondary { background-color: #e2e3e5; color: #383d41; }
        @media (max-width: 900px) { .container { margin-left: 0; padding: 16px; } .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]); ?>

    <div class="container">
    
        <div class="page-header">
            <h3>Categorias de Produtos</h3>
            <a href="/nexus-erp/public/products" class="btn-secondary">
                Voltar para Produtos
            </a>
        </div>
        

        <div class="card">
            <div class="card-header">
                <?php echo $editCategory ? 'Editando Categoria' : 'Nova Categoria'; ?>
            </div>
            <div class="card-body">
                <form 
                    action="<?php echo $editCategory ? '/nexus-erp/public/categories/update' : '/nexus-erp/public/categories/store'; ?>" 
                    method="POST"
                >
                    <?php if ($editCategory): ?>
                        <input type="hidden" name="category_id" value="<?php echo $editCategory['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nome da Categoria</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($editCategory['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="ACTIVE" <?php echo (($editCategory['status'] ?? 'ACTIVE') == 'ACTIVE') ? 'selected' : ''; ?>>Ativo</option>
                                <option value="INACTIVE" <?php echo (($editCategory['status'] ?? '') == 'INACTIVE') ? 'selected' : ''; ?>>Inativo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-primary" style="padding: 12px;"><?php echo $editCategory ? 'Salvar Alterações' : 'Criar Categoria'; ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">Nenhuma categoria cadastrada.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td>
                                        <?php if ($category['status'] === 'ACTIVE'): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/nexus-erp/public/categories?edit_id=<?php echo $category['id']; ?>" class="btn-warning-sm">
                                            Editar
                                        </a>
                                        <form action="/nexus-erp/public/categories/delete" method="POST" style="display: inline-block; margin-left: 5px;">
                                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                            <button type="submit" class="btn-danger-sm" onclick="return confirm('Tem certeza que deseja deletar?');">
                                                Excluir
                                            </button>
                                        </form>
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