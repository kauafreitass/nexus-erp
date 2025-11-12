<?php
// O Controller define:
// $activePage, $orders (array), $customers (array), $filters (array), $totalSales (float)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas</title>
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
        .btn-primary { padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem; color: #FFFFFF; background-color: var(--azul-header); border: none; cursor: pointer; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07); margin-bottom: 24px; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 16px 20px; text-align: left; white-space: nowrap; border-bottom: 1px solid #F3F4F6; }
        table thead th { background-color: var(--azul-header); color: #FFFFFF; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; }
        table tbody td { font-size: 0.95rem; color: var(--texto-label); }
        table tbody td:first-child { color: var(--texto-principal); font-weight: 500; }
        table tfoot tr { border-top: 2px solid var(--azul-header); }
        table tfoot td { font-size: 1.1rem; font-weight: 700; color: var(--texto-principal); }
        .text-right { text-align: right; }
        
        /* Filtros */
        .filter-card { padding: 20px; }
        .filter-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; width: 100%; }
        .form-group label { font-size: 0.875rem; font-weight: 600; color: var(--texto-label); margin-bottom: 8px; }
        .form-group input, .form-group select {
            font-size: 1rem; color: var(--texto-principal); padding: 10px; 
            border: 1px solid var(--borda-campo); border-radius: 8px; 
            background-color: #FFFFFF; width: 100%;
        }
        @media (max-width: 900px) { .container { margin-left: 0; padding: 16px; } }
    </style>
</head>
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
        <div class="page-header">
            <h1>Relatório de Vendas</h1>
            <a href="/nexus-erp/public/reports" class="btn-voltar">Voltar para o Dashboard</a>
        </div>
        
        <div class="card filter-card">
            <form method="GET" action="/nexus-erp/public/reports/sales" class="filter-form">
                <div class="form-group">
                    <label for="date_start">Data Inicial</label>
                    <input type="date" id="date_start" name="date_start" value="<?php echo htmlspecialchars($filters['date_start']); ?>">
                </div>
                <div class="form-group">
                    <label for="date_end">Data Final</label>
                    <input type="date" id="date_end" name="date_end" value="<?php echo htmlspecialchars($filters['date_end']); ?>">
                </div>
                <div class="form-group">
                    <label for="customer_id">Cliente</label>
                    <select id="customer_id" name="customer_id">
                        <option value="">Todos os Clientes</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>" <?php echo ($filters['customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status do Pedido</label>
                    <select id="status" name="status">
                        <option value="">Todos os Status</option>
                        <option value="DRAFT" <?php echo ($filters['status'] == 'DRAFT') ? 'selected' : ''; ?>>Rascunho</option>
                        <option value="CONFIRMED" <?php echo ($filters['status'] == 'CONFIRMED') ? 'selected' : ''; ?>>Confirmado</option>
                        <option value="INVOICED" <?php echo ($filters['status'] == 'INVOICED') ? 'selected' : ''; ?>>Faturado</option>
                        <option value="CANCELED" <?php echo ($filters['status'] == 'CANCELED') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-primary" style="width: 100%;">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Pedido ID</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Criado Por</th>
                            <th class="text-right">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Nenhum pedido encontrado para os filtros selecionados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right">Total Geral:</td>
                            <td class="text-right">R$ <?php echo number_format($totalSales, 2, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</body>
</html>