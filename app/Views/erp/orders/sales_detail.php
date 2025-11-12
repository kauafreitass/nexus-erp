<?php
// O Controller já definiu as variáveis:
// $pedido (array com dados do pedido E do cliente)
// $itens (array de itens)
// $activePage (string)
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido #<?php echo htmlspecialchars($pedido['id']); ?></title>

    <style>
        :root {
            --fundo-pagina: #F0F2F5;
            --card-bg: #FFFFFF;
            --texto-principal: #050C1B;
            --texto-label: #555;
            --azul-header: #183F8C;
            --azul-active: #2458BF;
            --borda-campo: #B0B0B0;
            --texto-secundario: #4B5563;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--fundo-pagina);
            color: var(--texto-principal);
            overflow-x: hidden;
        }

        .container {
            padding: 32px;
            margin-left: 240px;
            max-width: 1600px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 16px 20px;
            text-align: left;
            white-space: nowrap;
        }

        table tbody tr {
            border-bottom: 1px solid #F3F4F6;
        }

        table tbody tr:last-child {
            border-bottom: none;
        }

        table tbody td {
            font-size: 0.95rem;
            color: var(--texto-principal);
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        @media (max-width: 900px) {
            .container {
                margin-left: 0;
                padding: 16px;
            }
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--texto-principal);
        }

        .btn-voltar {
            display: inline-block;
            background-color: var(--azul-header);
            color: #FFFFFF;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: background-color 0.2s;
        }

        .btn-voltar:hover {
            background-color: var(--azul-active);
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #F3F4F6;
            font-size: 1.1rem;
            font-weight: 600;
            background-color: var(--azul-header);
            color: #FFFFFF;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-item label {
            font-size: 0.875rem;
            color: var(--texto-label);
            margin-bottom: 4px;
            font-weight: 500;
        }

        .info-item span {
            font-size: 1rem;
            color: var(--texto-principal);
            word-break: break-word;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 99px;
            text-transform: uppercase;
            width: fit-content;
        }

        .badge-CONFIRMED {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-INVOICED {
            background-color: #ffc107;
            color: #1E40AF;
        }

        .badge-CANCELED {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .badge-DRAFT {
            background-color: #F3F4F6;
            color: #4B5563;
        }

        .card-table thead th {
            background-color: #F9FAFB;
            color: var(--texto-secundario);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 1px solid #E5E7EB;
        }

        .status-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: fit-content;
        }

        .status-form-wrapper {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .select-wrapper {
            position: relative;
            width: 100%;
        }

        .select-wrapper::after {
            content: '▼';
            font-size: 0.8rem;
            color: var(--texto-secundario);
            position: absolute;
            margin-left: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .status-select {
            appearance: none;
            -webkit-appearance: none;
            width: fit-content;
            padding: 10px 32px 10px 12px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid var(--borda-campo);
            background-color: var(--card-bg);
            color: var(--texto-principal);
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s, border-color 0.2s;
        }

        .status-select:focus {
            outline: none;
            border-color: var(--azul-active);
            box-shadow: 0 0 0 2px rgba(24, 63, 140, 0.2);
        }

        .status-select.status-DRAFT {
            background-color: #F3F4F6;
            color: #4B5563;
            border-color: #E5E7EB;
        }

        .status-select.status-CONFIRMED {
            background-color: #D1FAE5;
            color: #065F46;
            border-color: #A7F3D0;
        }

        .status-select.status-INVOICED {
            background-color: #ffc107;
            color: #3d2f02ff;
            border-color: #ffc107;
        }

        .status-select.status-CANCELED {
            background-color: #FEE2E2;
            color: #991B1B;
            border-color: #FECACA;
        }

        .btn-primary {
            padding: 10px 16px;
            border-radius: 8px;
            width: fit-content;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            color: #FFFFFF;
            background-color: var(--azul-header);
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--azul-active);
        }
        
        /* ----- NOVOS ESTILOS ----- */
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .action-form {
            padding: 20px;
        }
        .action-form p {
            margin-bottom: 15px;
            color: var(--texto-secundario);
            font-size: 0.95rem;
        }
        .action-form form, .action-form a { /* Afeta o <a> e o <form> */
            display: inline-block;
            margin-right: 10px;
        }
        /* ----- FIM NOVOS ESTILOS ----- */


        @media (max-width: 900px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }
    </style>
</head>

<body>

    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">

        <div class="page-header">
            <h1>Pedido #<?php echo htmlspecialchars($pedido['id']); ?></h1>
            <a href="/nexus-erp/public/sales" class="btn-voltar">Voltar para a lista</a> </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 20px;"><?php echo $_SESSION['message']; ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                Informações do Pedido
            </div>
            <div class="info-grid">

                <div class="info-item">
                    <form action="/nexus-erp/public/sales/update_status" method="POST" class="status-form-wrapper">
                        <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($pedido['id']); ?>">

                        <div class="status-container">
                            <label for="status-select">Alterar Status</label>
                            <?php
                            $statusMap = [
                                'DRAFT' => 'Rascunho',
                                'CONFIRMED' => 'Confirmado',
                                'INVOICED' => 'Faturado',
                                'CANCELED' => 'Cancelado'
                            ];
                            $statusIngles = $pedido['status'];
                            ?>

                            <div class="select-wrapper">
                                <select name="novo_status" id="status-select" class="status-select status-<?php echo $statusIngles; ?>">
                                    <option value="DRAFT" <?php echo ($statusIngles == 'DRAFT') ? 'selected' : ''; ?>>
                                        Rascunho
                                    </option>
                                    <option value="CONFIRMED" <?php echo ($statusIngles == 'CONFIRMED') ? 'selected' : ''; ?>>
                                        Confirmado
                                    </option>
                                    <option value="INVOICED" <?php echo ($statusIngles == 'INVOICED') ? 'selected' : ''; ?>>
                                        Faturado
                                    </option>
                                    <option value="CANCELED" <?php echo ($statusIngles == 'CANCELED') ? 'selected' : ''; ?>>
                                        Cancelado
                                    </option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">Salvar Alteração</button>
                    </form>
                </div>

                <div class="info-item">
                    <label>Status Atual</label>
                    <span>
                        <?php
                        $status = htmlspecialchars($pedido['status']);
                        $classe = 'badge-' . $status;
                        $statusTraduzido = $statusMap[$status] ?? $status;
                        ?>
                        <span class="badge <?php echo $classe; ?>"><?php echo $statusTraduzido; ?></span>
                    </span>
                </div>
                <div class="info-item">
                    <label>Valor Total</label>
                    <span>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                Ações do Pedido
            </div>
            
            <div class="action-form">
                <?php if ($pedido['status'] === 'CONFIRMED'): ?>
                    <p>O pedido está confirmado e pronto para ser faturado.</p>
                    <form action="/nexus-erp/public/nfe/generate" method="POST"> <input type="hidden" name="sales_order_id" value="<?php echo htmlspecialchars($pedido['id']); ?>">
                        <button type="submit" class="btn-primary btn-success">Gerar Nota Fiscal (NFe)</button>
                    </form>

                <?php elseif ($pedido['status'] === 'DRAFT'): ?>
                    <p>Este pedido é um rascunho. Você pode editá-lo ou excluí-lo.</p>
                    
                    <a href="/nexus-erp/public/sales/edit?id=<?php echo $pedido['id']; ?>" class="btn-primary btn-secondary">
                        Editar Pedido
                    </a>
                    
                    <form action="/nexus-erp/public/sales/delete" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este pedido? Esta ação não pode ser desfeita.');">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($pedido['id']); ?>">
                        <button type="submit" class="btn-primary btn-danger">Excluir Pedido</button>
                    </form>
                
                <?php elseif ($pedido['status'] === 'INVOICED'): ?>
                    <p>Este pedido já foi faturado. Você pode ver a NFe correspondente.</p>
                    
                    <?php if (!empty($pedido['nfe_id'])): // Verifica se o Model encontrou o ID da NFe ?>
                        <a href="/nexus-erp/public/nfe/details?id=<?php echo htmlspecialchars($pedido['nfe_id']); ?>" class="btn-primary btn-secondary">
                            Ver Nota Fiscal (NFe)
                        </a>
                    <?php else: ?>
                        <p style="color: var(--vermelho); font-weight: bold;">Erro: O pedido está faturado, mas não foi possível encontrar a NFe associada.</p>
                    <?php endif; ?>
                <?php elseif ($pedido['status'] === 'CANCELED'): ?>
                    <p>Este pedido está cancelado e não permite novas ações.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Informações do Cliente
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Cliente</label>
                    <span><?php echo htmlspecialchars($pedido['cliente']); ?></span>
                </div>
                <div class="info-item">
                    <label>CNPJ/CPF</label>
                    <span><?php echo htmlspecialchars($pedido['cnpj_cpf']); ?></span>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($pedido['cliente_email']); ?></span>
                </div>
                <div class="info-item">
                    <label>Telefone</label>
                    <span><?php echo htmlspecialchars($pedido['cliente_telefone']); ?></span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <label>Endereço</label>
                    <span>
                        <?php
                        echo htmlspecialchars($pedido['cliente_rua']) . ', ' . htmlspecialchars($pedido['cliente_numero']);
                        if (!empty($pedido['cliente_complemento'])) {
                            echo ' - ' . htmlspecialchars($pedido['cliente_complemento']);
                        }
                        echo '<br>' . htmlspecialchars($pedido['cliente_bairro']) . ' - ' . htmlspecialchars($pedido['cliente_cidade']);
                        echo ' / ' . htmlspecialchars($pedido['cliente_estado']) . ' - CEP: ' . htmlspecialchars($pedido['cliente_cep']);
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Itens do Pedido
            </div>
            <div class="table-wrapper">
                <table class="card-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="text-center">Qtd.</th>
                            <th class="text-right">Valor Unit.</th>
                            <th class="text-right">Total do Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($itens)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 20px;">
                                    Nenhum item encontrado para este pedido.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_description']); ?></td>
                                    <td class="text-center"><?php echo number_format($item['quantity'], 2, ',', '.'); ?></td>
                                    <td class="text-right">R$ <?php echo number_format($item['unit_price'], 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php
                                        // Cálculo feito na view
                                        $total_item = $item['quantity'] * $item['unit_price'];
                                        echo 'R$ ' . number_format($total_item, 2, ',', '.');
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('status-select');
            function updateSelectColor(selectElement) {
                if (!selectElement) return;
                const selectedValue = selectElement.value;
                selectElement.className = 'status-select status-' + selectedValue;
            }
            if (select) {
                updateSelectColor(select);
                select.addEventListener('change', () => updateSelectColor(select));
            }
        });
    </script>

</body>
</html>