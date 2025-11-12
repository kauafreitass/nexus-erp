<?php
// O Controller deve definir:
// $activePage
// $products (array)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
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
        .btn-secondary { display: inline-block; padding: 10px 16px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 1rem; border: none; cursor: pointer; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07); overflow: hidden; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        table th, table td { white-space: nowrap; padding: 16px 20px; }
        table thead th { background-color: var(--azul-header); color: #FFFFFF; text-align: left; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; }
        table thead th:first-child { border-top-left-radius: 12px; }
        table thead th:last-child { border-top-right-radius: 12px; }
        table tbody tr { border-bottom: 1px solid #EEE; }
        table tbody tr:last-child { border-bottom: none; }
        table tbody td { font-size: 0.95rem; color: var(--texto-label); }
        table tbody td:first-child { color: var(--texto-principal); font-weight: 500; }
        .action-button-style { display: inline-block; padding: 5px 10px; background-color: #4a4a4a; color: white; text-decoration: none; border-radius: 15px; font-size: 13px; font-weight: bold; }
        .action-button-style:hover { background-color: #666; }
        @media (max-width: 900px) { .container { margin-left: 0; padding: 16px; } }
    </style>
</head>
        
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
    
    <div class="page-header">
        <h3>Meus Produtos</h3>
        <div>
            <a href="/nexus-erp/public/categories" class="btn-secondary" style="margin-right: 10px;">
                Gerenciar Categorias
            </a>
            <a href="/nexus-erp/public/products/create" class="btn-primary">
                Novo Produto
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>SKU</th>
                        <th>Tipo</th>
                        <th>Categoria</th> <th>UN</th>
                        <th>Preço de Custo</th>
                        <th>Preço de Venda</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Nenhum produto encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['description']); ?></td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo htmlspecialchars($product['type']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'Sem Categoria'); ?></td>
                                <td><?php echo htmlspecialchars($product['unit_of_measure']); ?></td>
                                <td>R$ <?php echo number_format($product['cost_price'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($product['sale_price'], 2, ',', '.'); ?></td>
                                <td>
                                    <a href="/nexus-erp/public/products/edit?id=<?php echo $product['id']; ?>" class="action-button-style">
                                        Editar
                                    </a>
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