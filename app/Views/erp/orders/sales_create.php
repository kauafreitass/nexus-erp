<?php
// O Controller NÃO precisa mais enviar $customers ou $products
// if (!isset($customers)) $customers = []; // Removido
// if (!isset($products)) $products = []; // Removido
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pedido de Venda</title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
            --borda-campo: #B0B0B0; --texto-secundario: #4B5563; --vermelho: #dc3545;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07); margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; font-size: 1.1rem; font-weight: 600; background-color: var(--azul-header); color: #FFFFFF; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h1 { font-size: 2rem; font-weight: 700; }
        .btn-voltar { display: inline-block; background-color: #6c757d; color: #FFFFFF; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; }
        .form-container { padding: 20px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; position: relative; /* Adicionado para os resultados */ }
        .form-group label { font-size: 0.875rem; color: var(--texto-label); font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 12px; font-size: 1rem; border-radius: 8px; border: 1px solid var(--borda-campo); background-color: var(--card-bg); color: var(--texto-principal); }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--azul-active); box-shadow: 0 0 0 2px rgba(24, 63, 140, 0.2); }
        .btn-primary { padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 1rem; color: #FFFFFF; background-color: var(--azul-header); border: none; cursor: pointer; }
        .btn-primary:hover { background-color: var(--azul-active); }
        .btn-secondary { background-color: #6c757d; }
        .btn-danger { background-color: var(--vermelho); font-size: 0.8rem; padding: 6px 10px; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 16px 20px; text-align: left; border-bottom: 1px solid #F3F4F6; }
        table thead th { background-color: #F9FAFB; color: var(--texto-secundario); font-size: 0.875rem; font-weight: 600; text-transform: uppercase; }
        .item-row input { width: 100%; max-width: 120px; padding: 8px; }
        .item-row td:first-child { width: 50%; }
        .item-total { font-weight: 700; }
        .total-container { text-align: right; padding: 20px; font-size: 1.2rem; font-weight: 700; }
        
        /* --- NOVOS ESTILOS PARA A BARRA DE PESQUISA --- */
        .search-results {
            position: absolute;
            top: 100%; /* Posiciona abaixo do input */
            left: 0;
            right: 0;
            background: var(--card-bg);
            border: 1px solid var(--borda-campo);
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            display: none; /* Começa escondido */
        }
        .search-results.active {
            display: block; /* Mostra quando ativo */
        }
        .result-item {
            padding: 12px 16px;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .result-item:hover {
            background-color: var(--fundo-pagina);
        }
        .result-item strong {
            color: var(--texto-principal);
        }
        .result-item span {
            font-size: 0.85rem;
            color: var(--texto-label);
            margin-left: 8px;
        }
        /* --- FIM DOS NOVOS ESTILOS --- */

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
            <h1>Novo Pedido de Venda</h1>
            <a href="/nexus-erp/public/sales" class="btn-voltar">Voltar</a>
        </div>

        <form method="POST" action="/nexus-erp/public/sales/store">
            <div class="card">
                <div class="card-header">Cliente e Data</div>
                <div class="form-container">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label for="customer-search">Cliente</label>
                            <input type="text" id="customer-search" placeholder="Digite para buscar o cliente..." autocomplete="off">
                            <input type="hidden" id="customer_id" name="customer_id" required>
                            <div class="search-results" id="customer-results"></div>
                        </div>
                        <div class="form-group">
                            <label for="order_date">Data do Pedido</label>
                            <input type="datetime-local" id="order_date" name="order_date" required>
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
                            </tbody>
                    </table>
                </div>
                <div class="form-container" style="display: flex; gap: 10px; align-items: flex-end;">
                    
                    <div class="form-group" style="flex: 1;">
                        <label for="product-search">Adicionar Produto</label>
                        <input type="text" id="product-search" placeholder="Digite para buscar o produto..." autocomplete="off" data-id="" data-price="">
                        <div class="search-results" id="product-results"></div>
                    </div>
                    <button type="button" id="add-item-btn" class="btn-primary" style="height: 47px;">Adicionar</button>
                </div>
                <div class="total-container">
                    Total do Pedido: R$ <span id="order-total">0,00</span>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 16px 24px;">Salvar Pedido</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Elementos de UI
            const customerSearch = document.getElementById('customer-search');
            const customerIdInput = document.getElementById('customer_id');
            const customerResults = document.getElementById('customer-results');
            
            const productSearch = document.getElementById('product-search');
            const productResults = document.getElementById('product-results');
            
            const addItemBtn = document.getElementById('add-item-btn');
            const itemsContainer = document.getElementById('items-container');
            const orderTotalEl = document.getElementById('order-total');
            let itemIndex = 0;
            
            // URL base (ajuste se for diferente)
            const BASE_URL = "/nexus-erp/public";

            /**
             * Função de busca genérica
             * @param {string} query - Termo da pesquisa
             * @param {string} type - 'customers' or 'products'
             * @param {HTMLElement} resultsContainer - Onde exibir os resultados
             */
            async function fetchSearch(query, type, resultsContainer) {
                if (query.length < 2) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.classList.remove('active');
                    return;
                }
                
                try {
                    const response = await fetch(`${BASE_URL}/api/${type}/search?q=${encodeURIComponent(query)}`);
                    if (!response.ok) throw new Error('Falha na busca');
                    
                    const results = await response.json();
                    
                    resultsContainer.innerHTML = ''; // Limpa resultados anteriores
                    if (results.length > 0) {
                        results.forEach(item => {
                            const div = document.createElement('div');
                            div.classList.add('result-item');
                            
                            if (type === 'customers') {
                                //
                                div.innerHTML = `<strong>${item.name}</strong> <span>${item.document_number}</span>`;
                                div.dataset.id = item.id;
                                div.dataset.name = item.name;
                            } else {
                                //
                                div.innerHTML = `<strong>${item.description}</strong> <span>(SKU: ${item.sku})</span>`;
                                div.dataset.id = item.id;
                                div.dataset.name = item.description;
                                div.dataset.price = item.sale_price;
                            }
                            resultsContainer.appendChild(div);
                        });
                        resultsContainer.classList.add('active');
                    } else {
                        resultsContainer.innerHTML = '<div class="result-item"><span>Nenhum resultado encontrado.</span></div>';
                        resultsContainer.classList.add('active');
                    }
                } catch (error) {
                    console.error('Erro na busca:', error);
                    resultsContainer.innerHTML = '<div class="result-item"><span>Erro ao buscar.</span></div>';
                    resultsContainer.classList.add('active');
                }
            }

            // Pesquisa de Clientes
            customerSearch.addEventListener('input', () => {
                fetchSearch(customerSearch.value, 'customers', customerResults);
            });
            
            // Seleção de Cliente
            customerResults.addEventListener('click', (e) => {
                const item = e.target.closest('.result-item');
                if (item && item.dataset.id) {
                    customerSearch.value = item.dataset.name; // Põe o nome no input
                    customerIdInput.value = item.dataset.id; // Põe o ID no input oculto
                    customerResults.classList.remove('active');
                }
            });

            // Pesquisa de Produtos
            productSearch.addEventListener('input', () => {
                fetchSearch(productSearch.value, 'products', productResults);
            });

            // Seleção de Produto
            productResults.addEventListener('click', (e) => {
                const item = e.target.closest('.result-item');
                if (item && item.dataset.id) {
                    productSearch.value = item.dataset.name; // Põe o nome no input
                    // Guarda os dados no próprio input de pesquisa
                    productSearch.dataset.id = item.dataset.id; 
                    productSearch.dataset.price = item.dataset.price;
                    productResults.classList.remove('active');
                }
            });
            
            // Esconde resultados ao clicar fora
            document.addEventListener('click', (e) => {
                if (!customerSearch.contains(e.target)) customerResults.classList.remove('active');
                if (!productSearch.contains(e.target)) productResults.classList.remove('active');
            });

            // Adicionar Item (Lógica atualizada)
            addItemBtn.addEventListener('click', () => {
                // Pega os dados guardados no input de pesquisa do produto
                const productId = productSearch.dataset.id;
                const productName = productSearch.value;
                const productPrice = parseFloat(productSearch.dataset.price || 0);

                if (!productId) {
                    alert('Por favor, selecione um produto da lista.');
                    return;
                }

                const newRow = document.createElement('tr');
                newRow.classList.add('item-row');
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
                
                // Limpa o input do produto
                productSearch.value = '';
                productSearch.dataset.id = '';
                productSearch.dataset.price = '';
            });

            // Funções de Tabela (idênticas ao seu arquivo original)
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
        });
    </script>
</body>
</html>