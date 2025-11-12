<?php
// O Controller define:
// $activePage
// $notas (array)
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas Fiscais</title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5;
            --card-bg: #FFFFFF;
            --texto-principal: #050C1B;
            --texto-label: #555;
            --azul-header: #183F8C;
            --azul-active: #2458BF;
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
        }

        .container {
            padding: 32px;
            margin-left: 240px;
            max-width: 1600px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--texto-principal);
            margin-bottom: 24px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

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
        }

        table tbody tr:last-child {
            border-bottom: none;
        }

        table tbody td {
            font-size: 0.95rem;
            color: var(--texto-label);
        }

        table tbody td:first-child {
            color: var(--texto-principal);
            font-weight: 500;
        }

        /* Esfera de Status */
        .status-sphere {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            vertical-align: middle;
        }

        /* Status de NFe */
        .status-authorized {
            background-color: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.7);
        }

        .status-canceled {
            background-color: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.7);
        }

        .status-rejected {
            background-color: #ffc107;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.7);
        }

        .status-sent {
            background-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.7);
        }

        .status-generated {
            background-color: #6c757d;
            box-shadow: 0 0 8px rgba(108, 117, 125, 0.7);
        }

        .action-button-style {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4a4a4a;
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
        }

        .action-button-style:hover {
            background-color: #666;
        }

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
            <h3>Notas Fiscais (NFe)</h3>
            <a href="/nexus-erp/public/nfe/create" class="btn-primary">
                Nova Nota
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
                            <th>Nota</th>
                            <th>Série</th>
                            <th>Data Emissão</th>
                            <th>Status</th>
                            <th>Pedido Origem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($notas)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Nenhuma nota fiscal encontrada.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $statusMap = [
                                'GENERATED' => 'Gerada',
                                'SENT' => 'Enviada',
                                'AUTHORIZED' => 'Autorizada',
                                'REJECTED' => 'Rejeitada',
                                'CANCELED' => 'Cancelada'
                            ];
                            ?>
                            <?php foreach ($notas as $nota): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($nota['nfe_number']); ?></td>
                                    <td><?php echo htmlspecialchars($nota['series']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($nota['issue_date'])); ?></td>
                                    <td>
                                        <?php
                                        $statusIngles = $nota['status'];
                                        $statusClass = 'status-' . strtolower($statusIngles);
                                        $statusTraduzido = $statusMap[$statusIngles] ?? $statusIngles;
                                        ?>
                                        <span class="status-sphere <?php echo $statusClass; ?>"></span>
                                        <strong><?php echo htmlspecialchars($statusTraduzido); ?></strong>
                                    </td>
                                    <td>#<?php echo htmlspecialchars($nota['sales_order_id']); ?></td>
                                    <td>
                                        <a href="/nexus-erp/public/nfe/details?id=<?php echo $nota['id']; ?>"
                                            class="action-button-style">
                                            Ver Detalhes
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