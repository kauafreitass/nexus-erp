<?php
$pedidos;
$activePage;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos de Venda</title>

    <style>
        :root {
            --fundo-pagina: #F0F2F5;
            --card-bg: #FFFFFF;
            --texto-principal: #050C1B;
            --texto-label: #555;
            --azul-header: #183F8C;
            --azul-active: #2458BF;
            --borda-campo: #B0B0B0;
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

        /* ----- NOVO/ALTERADO ----- */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--texto-principal);
            margin-bottom: 24px;
        }

        /* Botão Novo Pedido */
        .btn-primary {
            display: inline-block;
            padding: 10px 16px;
            background-color: var(--azul-header);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: var(--azul-active);
        }

        /* Botão Excluir */
        .btn-danger {
            display: inline-block;
            padding: 5px 10px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            margin-left: 8px;
            vertical-align: middle;
        }
        .btn-danger:hover { background-color: #c82333; }
        /* ----- FIM NOVO/ALTERADO ----- */


        .card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        table th,
        table td {
            white-space: nowrap;
            padding: 16px 20px;
        }

        table thead th {
            background-color: var(--azul-header);
            color: #FFFFFF;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        table thead th:first-child {
            border-top-left-radius: 12px;
        }

        table thead th:last-child {
            border-top-right-radius: 12px;
        }

        table tbody tr {
            border-bottom: 1px solid #EEE;
            transition: background-color 0.2s ease;
        }

        table tbody tr:last-child {
            border-bottom: none;
        }

        table tbody tr.clickable-row {
            cursor: pointer;
        }

        table tbody tr.clickable-row:hover {
            background-color: #F0F5FF;
        }

        table tbody tr.clickable-row:focus {
            outline: 2px solid var(--azul-active);
            outline-offset: -2px;
            background-color: #F0F5FF;
        }

        table tbody td {
            font-size: 0.95rem;
            color: var(--texto-label);
        }

        table tbody td:first-child {
            color: var(--texto-principal);
            font-weight: 500;
        }

        .submenu .link-subopcao.active,
        .minha-conta a.active,
        .item-opcao>a.link-opcao.active {
            background: var(--azul-active) !important;
            font-weight: 600;
        }

        .submenu .link-subopcao.active {
            border-radius: 8px;
        }

        .status-sphere {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            vertical-align: middle;
        }
        .status-confirmed {
            background-color: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.7);
        }
        .status-draft {
            background-color: #6c757d;
            box-shadow: 0 0 8px rgba(108, 117, 125, 0.7);
        }
        .status-canceled {
            background-color: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.7);
        }
        .status-invoiced {
            background-color: #ffc107;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.7);
        }
        .sales-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }
        .sales-table th,
        .sales-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .sales-table th {
            background-color: #f4f4f4;
        }
        .action-button {
            display: inline-block;
            padding: 6px 12px;
            background-color: #4a4a4a;
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-size: 14px;
        }
        .action-button:hover {
            background-color: #666;
        }
        
        /* ----- NOVO/ALTERADO ----- */
        .action-button-style {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4a4a4a;
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
            vertical-align: middle;
        }
        .action-button-style:hover { background-color: #666; }
        /* ----- FIM NOVO/ALTERADO ----- */

        @media (max-width: 900px) {
            .container {
                margin-left: 0;
                padding: 16px;
            }
        }
    </style>
</head>
        
<body>

    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
    
    <div class="page-header">
        <h3>Pedidos de Venda</h3>
        <a href="/nexus-erp/public/sales/create" class="btn-primary">
            Novo Pedido
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
                    <th>ID do Pedido</th>
                    <th>Data</th>
                    <th>Cliente</th> <th>CNPJ/CPF</th> <th>Status</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Nenhum pedido encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $statusMap = [
                            'DRAFT' => 'Rascunho',
                            'CONFIRMED' => 'Confirmado',
                            'INVOICED' => 'Faturado',
                            'CANCELED' => 'Cancelado'
                        ];
                        ?>

                        <?php foreach ($orders as $order): ?>
                            
                            <?php
                            $url_destino = "/nexus-erp/public/sales/details?id=" . urlencode($order['id']);
                            ?>
                            
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                
                                <td><?php echo date('d/m/Y H:i', strtotime($order['data'])); ?></td>
                                
                                <td><?php echo htmlspecialchars($order['cliente']); ?></td>
                                <td><?php echo htmlspecialchars($order['cnpj_cpf']); ?></td>
                                
                                <td>
                                    <?php
                                    $statusIngles = $order['status'];
                                    $statusClass = 'status-' . strtolower($statusIngles);
                                    $statusTraduzido = $statusMap[$statusIngles] ?? $statusIngles;
                                    ?>
                                    <span class="status-sphere <?php echo $statusClass; ?>"></span>
                                    <strong><?php echo htmlspecialchars($statusTraduzido); ?></strong>
                                </td>
                                
                                <td>R$ <?php echo number_format($order['total'], 2, ',', '.'); ?></td>
                                
                                <td>
                                    <a href="<?php echo $url_destino; ?>" class="action-button-style">
                                        Ver Detalhes
                                    </a>

                                    <?php if ($statusIngles === 'DRAFT'): ?>
                                        <form action="/nexus-erp/public/sales/delete" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este pedido?');">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
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