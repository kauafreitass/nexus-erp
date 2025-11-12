<?php
// O Controller DEVE definir:
// $pedido (array com dados do pedido, ex: id, customer_id, data)
// $itens (array de itens do pedido, ex: product_id, product_description, quantity, unit_price, total_price)
// $customers (array de TODOS os clientes)
// $products (array de TODOS os produtos)
// $activePage

// Placeholders (caso o controller ainda não os envie)
if (!isset($customers)) $customers = [];
if (!isset($products)) $products = [];
if (!isset($itens)) $itens = [];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido #<?php echo htmlspecialchars($pedido['id']); ?></title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
            --borda-campo: #B0B0B0; --texto-secundario: #4B5563; --vermelho: #dc3545;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--fundo-pagina); color: var(--texto-principal);
        }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07); margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; font-size: 1.1rem; font-weight: 600; background-color: var(--azul-header); color: #FFFFFF; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h1 { font-size: 2rem; font-weight: 700; }
        .btn-voltar { display: inline-block; background-color: #6c757d; color: #FFFFFF; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; }
        .form-container { padding: 20px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 0.875rem; color: var(--texto-label); font-weight: 500; }
        .form-group input, .form-group select {
            width: 100%; padding: 12px; font-size: 1rem; border-radius: 8px;
            border: 1px solid var(--borda-campo); background-color: var(--card-bg); color: var(--texto-principal);
        }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--azul-active); box-shadow: 0 0 0 2px rgba(24, 63, 140, 0.2); }
        .btn-primary { padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 1rem; color: #FFFFFF; background-color: var(--azul-header); border: none; cursor: pointer; }
        .btn-primary:hover { background-color: var(--azul-active); }
        .btn-secondary { background-color: #6c757d; }
        .btn-danger { background-color: var(--vermelho); font-size: 0.8rem; padding: 6px 10px; border:none; cursor:pointer; color: white; border-radius: 4px;}
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 16px 20px; text-align: left; border-bottom: 1px solid #F3F4F6; }
        table thead th { background-color: #F9FAFB; color: var(--texto-secundario); font-size: 0.875rem; font-weight: 600; text-transform: uppercase; }
        .item-row input { width: 100%; max-width: 120px; padding: 8px; }
        .item-row td:first-child { width: 50%; }
        .item-total { font-weight: 700; }
        .total-container { text-align: right; padding: 20px; font-size: 1.2rem; font-weight: 700; }
        @media (max-width: 900px) {
            .container { margin-left: 0; padding: 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
        <div class="page-header">
            <h1>Editar Pedido #<?php echo htmlspecialchars($pedido['id']); ?></h1>
            <a href="/nexus-erp/public/sales/details?id=<?php echo $pedido['id']; ?>" class="btn-voltar">Voltar</a>
        </div>

        <form method="POST" action="/nexus-erp/public/sales/update">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($pedido['id']); ?>">
            
            <div class="card">
                <div class="card-header">Cliente e Data</div>
                <div class="form-container">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="customer_id">Cliente</label>
                            <select id="customer_id" name="customer_id" required>
                                <option value="">Selecione um cliente</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?php echo $customer['id']; ?>" <?php echo ($customer['id'] == $pedido['customer_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($customer['business_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="order_date">Data do Pedido</label>
                            <input type="datetime-local" id="order_date" name="order_date" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($pedido['data'])); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Itens do Pedido</div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Qtd.</th>
                                <th>Valor Unit.</th>
                                <th>Total</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <?php $itemIndex = 0; ?>
                            <?php foreach ($itens as $item): ?>
                                <tr class="item-row">
                                    <td>
                                        <?php echo htmlspecialchars($item['product_description']); ?>
                                        <input type="hidden" name="items[<?php echo $itemIndex; ?>][product_id]" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="item-qty" name="items[<?php echo $itemIndex; ?>][quantity]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" step="any" required>
                                    </td>
                                    <td>
                                        <input type="number" class="item-price" name="items[<?php echo $itemIndex; ?>][unit_price]" value="<?php echo number_format($item['unit_price'], 2, '.', ''); ?>" min="0" step="any" required>
                                    </td>
                                    <td class="item-total">R$ <?php echo number_format($item['total_price'], 2, ',', '.'); ?></td>
                                    <td>
                                        <button type="button" class="btn-danger remove-item-btn">Remover</button>
                                    </td>
                                </tr>
                                <?php $itemIndex++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-container" style="display: flex; gap: 10px; align-items: flex-end;">
                    <div class="form-group" style="flex: 1;">
                        <label for="product-select">Adicionar Produto</label>
                        <select id="product-select">
                            <option value="">Selecione um produto para adicionar</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"
                                    data-price="<?php echo $product['sale_price']; ?>">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" id="add-item-btn" class="btn-primary" style="height: 47px;">Adicionar</button>
                </div>
                <div class="total-container">
                    Total do Pedido: R$ <span id="order-total">0,00</span>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 16px 24px;">Salvar Alterações</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const productSelect = document.getElementById('product-select');
            const addItemBtn = document.getElementById('add-item-btn');
            const itemsContainer = document.getElementById('items-container');
            const orderTotalEl = document.getElementById('order-total');
            
            // 1. O índice começa DEPOIS dos itens carregados pelo PHP
            let itemIndex = <?php echo $itemIndex; ?>; 

            addItemBtn.addEventListener('click', () => {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    alert('Por favor, selecione um produto.');
                    return;
                }

                const productId = selectedOption.value;
                const productName = selectedOption.text;
                const productPrice = parseFloat(selectedOption.getAttribute('data-price') || 0);

                const newRow = document.createElement('tr');
                newRow.classList.add('item-row');
                // Lógica de adicionar linha (idêntica ao sales_create.php)
                newRow.innerHTML = `
                    <td>
                        ${productName}
                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${productId}">
                    </td>
                    <td>
                        <input type="number" class="item-qty" name="items[${itemIndex}][quantity]" value="1" min="1" step="any" required>
                    </td>
                    <td>
                        <input type="number" class="item-price" name="items[${itemIndex}][unit_price]" value="${productPrice.toFixed(2)}" min="0" step="any" required>
                    </td>
                    <td class="item-total">R$ ${productPrice.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn-danger remove-item-btn">Remover</button>
                    </td>
                `;
                itemsContainer.appendChild(newRow);
                itemIndex++;
                updateOrderTotal();
            });

            // Lógica de eventos (idêntica ao sales_create.php)
            itemsContainer.addEventListener('input', (e) => {
                if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price')) {
                    const row = e.target.closest('tr');
                    updateRowTotal(row);
                }
            });

            itemsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-item-btn')) {
                    e.target.closest('tr').remove();
                    updateOrderTotal();
                }
            });

            // Lógica de cálculo (idêntica ao sales_create.php)
            function updateRowTotal(row) {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const total = qty * price;
                row.querySelector('.item-total').textContent = `R$ ${total.toFixed(2)}`;
                updateOrderTotal();
            }

            function updateOrderTotal() {
                let total = 0;
                itemsContainer.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                    const price = parseFloat(row.querySelector('.item-price').value) || 0;
                    total += qty * price;
                });
                orderTotalEl.textContent = total.toFixed(2).replace('.', ',');
            }
            
            // 2. Chama a função de total no carregamento para somar os itens existentes
            updateOrderTotal();
        });
    </script>
</body>

</html>