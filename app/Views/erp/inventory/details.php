<?php
// O Controller deve definir:
// $activePage
// $product (array)
// $logs (array)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato de Estoque</title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
            --borda-campo: #B0B0B0; --texto-secundario: #4B5563;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h1 { font-size: 2rem; font-weight: 700; }
        .btn-voltar { display: inline-block; background-color: #6c757d; color: #FFFFFF; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07); margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; font-size: 1.1rem; font-weight: 600; background-color: var(--azul-header); color: #FFFFFF; }
        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 20px; }
        .info-item { display: flex; flex-direction: column; }
        .info-item label { font-size: 0.875rem; color: var(--texto-label); margin-bottom: 4px; font-weight: 500; }
        .info-item span { font-size: 1rem; color: var(--texto-principal); word-break: break-word; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 16px 20px; text-align: left; white-space: nowrap; border-bottom: 1px solid #F3F4F6; }
        .text-right { text-align: right; }
        .card-table thead th { background-color: #F9FAFB; color: var(--texto-secundario); font-size: 0.875rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid #E5E7EB; }
        .log-in { color: #065F46; font-weight: 600; } /* Verde */
        .log-out { color: #991B1B; font-weight: 600; } /* Vermelho */
        @media (max-width: 900px) { .container { margin-left: 0; padding: 16px; } .info-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
        <div class="page-header">
            <h1>Extrato de Estoque</h1>
            <a href="/nexus-erp/public/supplies" class="btn-voltar">Voltar</a>
        </div>
        
        <div class="card">
            <div class="card-header">Produto</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>SKU</label>
                    <span><?php echo htmlspecialchars($product['sku']); ?></span>
                </div>
                <div class="info-item">
                    <label>Descrição</label>
                    <span><?php echo htmlspecialchars($product['description']); ?></span>
                </div>
                <div class="info-item">
                    <label>Unidade de Medida</label>
                    <span><?php echo htmlspecialchars($product['unit_of_measure']); ?></span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">Histórico de Movimentações (Logs)</div>
            <div class="table-wrapper">
                <table class="card-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo de Transação</th>
                            <th class="text-right">Quantidade (Alteração)</th>
                            <th class="text-right">Custo (na Entrada)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="4" style="text-align: center; padding: 20px;">Nenhuma movimentação encontrada.</td></tr>
                        <?php else: ?>
                            <?php
                            $typeMap = [
                                'PURCHASE_RECEIPT' => 'Entrada (Compra)',
                                'SALE_SHIPMENT' => 'Saída (Venda)',
                                'ADJUSTMENT_IN' => 'Entrada (Ajuste)',
                                'ADJUSTMENT_OUT' => 'Saída (Ajuste)'
                            ];
                            ?>
                            <?php foreach ($logs as $log): ?>
                                <?php
                                $quantity = (float)$log['quantity_change'];
                                $class = $quantity > 0 ? 'log-in' : 'log-out';
                                $cost = (float)$log['cost_at_time'];
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['transaction_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($typeMap[$log['transaction_type']] ?? $log['transaction_type']); ?></td>
                                    <td class="<?php echo $class; ?> text-right">
                                        <?php echo ($quantity > 0 ? '+' : '') . number_format($quantity, 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo ($cost > 0) ? 'R$ ' . number_format($cost, 2, ',', '.') : '-'; ?>
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