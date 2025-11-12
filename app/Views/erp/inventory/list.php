<?php
// O Controller deve definir:
// $activePage
// $stockLevels (array)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Estoque</title>
    <style>
        :root {
            --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
            --texto-label: #555; --azul-header: #183F8C; --azul-active: #2458BF;
            --borda-campo: #B0B0B0; --texto-secundario: #4B5563;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
        .container { padding: 32px; margin-left: 240px; max-width: 1600px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; font-size: 2rem; font-weight: 700; color: var(--texto-principal); margin-bottom: 24px; }
        .btn-primary { display: inline-block; padding: 10px 16px; background-color: var(--azul-header); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 1rem; border: none; cursor: pointer; transition: background-color 0.2s; }
        .btn-primary:hover { background-color: var(--azul-active); }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07); overflow: hidden; margin-bottom: 24px; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        table th, table td { white-space: nowrap; padding: 16px 20px; }
        table thead th { background-color: var(--azul-header); color: #FFFFFF; text-align: left; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; }
        table tbody tr { border-bottom: 1px solid #EEE; }
        table tbody td { font-size: 0.95rem; color: var(--texto-label); }
        table tbody td:first-child, table tbody td:nth-child(2) { color: var(--texto-principal); font-weight: 500; }
        
        .stock-level { font-weight: 700; font-size: 1rem; }
        .stock-positive { color: #065F46; }
        .stock-zero { color: var(--texto-label); }
        .stock-negative { color: #991B1B; }
        
        .action-button-style { display: inline-block; padding: 5px 10px; background-color: #4a4a4a; color: white; text-decoration: none; border-radius: 15px; font-size: 13px; font-weight: bold; cursor: pointer; border: none;}
        .action-button-style:hover { background-color: #666; }
        
        /* === ESTILOS PARA LINHA CLICÁVEL === */
        .product-link { /* Este estilo não é mais usado, mas pode ser removido */
            color: var(--texto-principal); text-decoration: none; font-weight: 500;
        }
        .product-link:hover { text-decoration: underline; color: var(--azul-active); }

        table tbody tr.clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        table tbody tr.clickable-row:hover {
            background-color: #F0F5FF; /* Azul claro de hover */
        }
        table tbody tr.clickable-row:focus {
            outline: 2px solid var(--azul-active);
            outline-offset: -2px;
            background-color: #F0F5FF;
        }
        /* === FIM DOS ESTILOS === */


        /* Estilos do Modal (sem alterações) */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; pointer-events: none; transition: opacity 0.3s ease-in-out; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: var(--card-bg); border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #eee; }
        .modal-header h3 { font-size: 1.5rem; color: var(--texto-principal); }
        .modal-close-btn { cursor: pointer; color: var(--texto-label); width: 24px; height: 24px; }
        .modal-body { padding: 24px; display: grid; gap: 16px; }
        .form-group { display: flex; flex-direction: column; width: 100%; }
        .form-group label { font-size: 0.875rem; font-weight: 600; color: var(--texto-label); margin-bottom: 8px; }
        .form-group input, .form-group select { font-size: 1rem; color: var(--texto-principal); padding: 12px 16px; border: 1px solid var(--borda-campo); border-radius: 8px; background-color: #FFFFFF; width: 100%; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 20px 24px; background-color: #f9f9f9; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
        .btn { border: none; border-radius: 8px; padding: 10px 20px; font-size: 1rem; font-weight: 600; cursor: pointer; }
        .btn-secondary { background-color: #eee; color: var(--texto-label); }
        .btn-secondary:hover { background-color: #ddd; }

        @media (max-width: 900px) { .container { margin-left: 0; padding: 16px; } }
    </style>
</head>
        
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
    
    <div class="page-header">
        <h3>Controle de Estoque</h3>
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
                        <th>SKU</th>
                        <th>Produto / Serviço</th>
                        <th>Tipo</th>
                        <th>Estoque Atual</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stockLevels)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Nenhum produto encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stockLevels as $item): ?>
                            
                            <?php
                            // 1. Define a URL de destino
                            $url_destino = "/nexus-erp/public/supplies/details?id=" . $item['id'];
                            ?>
                            <tr class="clickable-row" data-href="<?php echo $url_destino; ?>" tabindex="0">
                                <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($item['description']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['type']); ?></td>
                                <td>
                                    <?php
                                    $stock = (float)$item['current_stock'];
                                    $class = 'stock-zero';
                                    if ($stock > 0) $class = 'stock-positive';
                                    if ($stock < 0) $class = 'stock-negative';
                                    $formattedStock = number_format($stock, 2, ',', '.');
                                    $unit = htmlspecialchars($item['unit_of_measure']);
                                    ?>
                                    <span class="<?php echo $class; ?> stock-level">
                                        <?php echo "$formattedStock ($unit)"; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['type'] == 'PRODUCT'): ?>
                                        <button type="button" class="action-button-style open-adjust-modal"
                                                data-product-id="<?php echo $item['id']; ?>"
                                                data-product-name="<?php echo htmlspecialchars($item['description']); ?>">
                                            Ajustar
                                        </button>
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

<div class="modal-overlay" id="adjust-stock-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="adjust-modal-title">Ajustar Estoque</h3>
            <svg class="modal-close-btn" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        <form class="modal-body" method="POST" action="/nexus-erp/public/supplies/adjust">
            
            <input type="hidden" id="adjust-product-id" name="product_id">
            
            <div class="form-group">
                <label>Produto</label>
                <input type="text" id="adjust-product-name" readonly style="background: #eee;">
            </div>
            
            <div class="form-group">
                <label for="adjust-type">Tipo de Ajuste</label>
                <select id="adjust-type" name="type" required>
                    <option value="ADJUSTMENT_IN">Entrada (Ajuste)</option>
                    <option value="ADJUSTMENT_OUT">Saída (Ajuste)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="adjust-quantity">Quantidade</label>
                <input type="number" id="adjust-quantity" name="quantity" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group" id="cost-group">
                <label for="adjust-cost">Custo Unitário (R$)</label>
                <input type="number" id="adjust-cost" name="cost_price" step="0.01" min="0">
                <small>Opcional. Usado apenas para entradas.</small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Ajuste</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // --- Lógica do Modal (existente) ---
    const modal = document.getElementById('adjust-stock-modal');
    if (modal) { // Adiciona verificação de segurança
        const openButtons = document.querySelectorAll('.open-adjust-modal');
        const closeButtons = modal.querySelectorAll('.modal-close-btn');
        const modalTitle = document.getElementById('adjust-modal-title');
        const productIdInput = document.getElementById('adjust-product-id');
        const productNameInput = document.getElementById('adjust-product-name');
        const typeSelect = document.getElementById('adjust-type');
        const costGroup = document.getElementById('cost-group');

        const openModal = (e) => {
            const btn = e.currentTarget;
            const id = btn.dataset.productId;
            const name = btn.dataset.productName;
            
            modalTitle.textContent = `Ajustar Estoque: ${name}`;
            productIdInput.value = id;
            productNameInput.value = name;
            
            modal.classList.add('active');
        };
        
        const closeModal = () => {
            modal.classList.remove('active');
        };

        if (typeSelect) {
            typeSelect.addEventListener('change', () => {
                costGroup.style.display = (typeSelect.value === 'ADJUSTMENT_IN') ? 'flex' : 'none';
            });
        }

        openButtons.forEach(btn => btn.addEventListener('click', openModal));
        closeButtons.forEach(btn => btn.addEventListener('click', closeModal));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    }

    // --- LÓGICA DA LINHA CLICÁVEL (NOVA) ---
    const rows = document.querySelectorAll('tr.clickable-row');
    rows.forEach(row => {
        const url = row.dataset.href;
        if (!url) return;

        // Clique com o botão do meio (abrir em nova aba)
        row.addEventListener('auxclick', (e) => {
            if (e.button === 1) { // 1 = Botão do meio
                window.open(url, '_blank');
            }
        });

        // Clique normal (esquerdo)
        row.addEventListener('click', (e) => {
            // IMPEDE A NAVEGAÇÃO se o clique for no botão "Ajustar"
            if (e.target.closest('.action-button-style')) {
                return;
            }
            
            // Ctrl/Cmd + Clique (abrir em nova aba)
            if (e.ctrlKey || e.metaKey) {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        });

        // Tecla Enter (acessibilidade)
        row.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                // IMPEDE A NAVEGAÇÃO se o foco estiver no botão
                if (e.target.closest('.action-button-style')) {
                    return;
                }
                window.location.href = url;
            }
        });
    });
});
</script>
</body>
</html>