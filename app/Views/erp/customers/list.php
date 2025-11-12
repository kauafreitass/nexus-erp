<?php
// O Controller deve definir:
// $activePage
// $customers (array)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
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
        <h3>Meus Clientes</h3>
        <a href="/nexus-erp/public/customers/create" class="btn-primary">
            Novo Cliente
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
                        <th>Razão Social</th>
                        <th>Nome Fantasia</th>
                        <th>Documento</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Nenhum cliente encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['business_name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['document_number']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td>
                                    <a href="/nexus-erp/public/customers/edit?id=<?php echo $customer['id']; ?>" class="action-button-style">
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