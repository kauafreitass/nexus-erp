<?php
// O Controller define:
// $activePage
// $nota (array com dados da nfe)
// $itens (array de nfe_items)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da NFe #<?php echo htmlspecialchars($nota['nfe_number']); ?></title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
            --borda-campo: #B0B0B0; --texto-secundario: #4B5563; --vermelho: #dc3545;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.07); margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; font-size: 1.1rem; font-weight: 600; background-color: var(--azul-header); color: #FFFFFF; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 16px 20px; text-align: left; white-space: nowrap; border-bottom: 1px solid #F3F4F6; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h1 { font-size: 2rem; font-weight: 700; }
        .btn-voltar { display: inline-block; background-color: var(--azul-header); color: #FFFFFF; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; }
        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 20px; }
        .info-item { display: flex; flex-direction: column; }
        .info-item label { font-size: 0.875rem; color: var(--texto-label); margin-bottom: 4px; font-weight: 500; }
        .info-item span { font-size: 1rem; color: var(--texto-principal); word-break: break-word; }
        .badge { display: inline-block; padding: 4px 12px; font-size: 0.875rem; font-weight: 600; border-radius: 99px; text-transform: uppercase; width: fit-content; }
        
        .badge-AUTHORIZED { background-color: #D1FAE5; color: #065F46; }
        .badge-CANCELED { background-color: #FEE2E2; color: #991B1B; }
        .badge-REJECTED { background-color: #FEF3C7; color: #92400E; }
        .badge-SENT { background-color: #DBEAFE; color: #1E40AF; }
        .badge-GENERATED { background-color: #F3F4F6; color: #4B5563; }
        
        .card-table thead th { background-color: #F9FAFB; color: var(--texto-secundario); font-size: 0.875rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid #E5E7EB; }
        .btn-primary { padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; color: #FFFFFF; background-color: var(--azul-header); border: none; cursor: pointer; }
        .btn-danger { background-color: var(--vermelho); }
        
        @media (max-width: 900px) { .container { margin-left: 0; padding: 16px; } .info-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
        <div class="page-header">
            <h1>Nota Fiscal N° <?php echo htmlspecialchars($nota['nfe_number']); ?></h1>
            <a href="/nexus-erp/public/nfe" class="btn-voltar">Voltar para a lista</a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 20px;"><?php echo $_SESSION['message']; ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">Informações da NFe</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Status</label>
                    <?php
                    $status = htmlspecialchars($nota['status']);
                    $classe = 'badge-' . $status;
                    $statusMap = [
                        'GENERATED' => 'Gerada', 'SENT' => 'Enviada',
                        'AUTHORIZED' => 'Autorizada', 'REJECTED' => 'Rejeitada',
                        'CANCELED' => 'Cancelada'
                    ];
                    $statusTraduzido = $statusMap[$status] ?? $status;
                    ?>
                    <span class="badge <?php echo $classe; ?>"><?php echo $statusTraduzido; ?></span>
                </div>
                <div class="info-item">
                    <label>Número</label>
                    <span><?php echo htmlspecialchars($nota['nfe_number']); ?></span>
                </div>
                <div class="info-item">
                    <label>Série</label>
                    <span><?php echo htmlspecialchars($nota['series']); ?></span>
                </div>
                <div class="info-item">
                    <label>Data de Emissão</label>
                    <span><?php echo date('d/m/Y H:i', strtotime($nota['issue_date'])); ?></span>
                </div>
                <div class="info-item">
                    <label>Pedido de Venda (Origem)</label>
                    <span><a href="/nexus-erp/public/sales/details?id=<?php echo $nota['sales_order_id']; ?>">Pedido #<?php echo $nota['sales_order_id']; ?></a></span>
                </div>
                <div class="info-item">
                    <label>Chave de Acesso (MVP)</label>
                    <span style="word-break: break-all; font-family: monospace; font-size: 0.9rem;">
                        <?php echo htmlspecialchars($nota['access_key'] ?? '[N/A]'); ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($nota['status'] == 'AUTHORIZED'): ?>
        <div class="card">
            <div class="card-header">Ações</div>
            <div class="form-container">
                <p style="margin-bottom: 15px;">Ações disponíveis para esta nota fiscal.</p>
                <form action="/nexus-erp/public/nfe/cancel" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta NFe? Esta ação também reabrirá o pedido de venda.');">
                    <input type="hidden" name="nfe_id" value="<?php echo htmlspecialchars($nota['id']); ?>">
                    <button type="submit" class="btn-primary btn-danger">Cancelar NFe</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">Itens da Nota Fiscal</div>
            <div class="table-wrapper">
                <table class="card-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>SKU</th>
                            <th class="text-center">CFOP</th>
                            <th class="text-center">Qtd.</th>
                            <th class="text-right">Valor Unit.</th>
                            <th class="text-right">Total do Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($itens)): ?>
                            <tr><td colspan="6" class="text-center">Nenhum item encontrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                                    <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($item['cfop']); ?></td>
                                    <td class="text-center"><?php echo number_format($item['quantity'], 2, ',', '.'); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($item['unit_price'], 2, ',', '.'); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($item['total_price'], 2, ',', '.'); ?></td>
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