<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Produto</title>
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
        .btn-primary { padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 1rem; color: #FFFFFF; background-color: var(--azul-header); border: none; cursor: pointer; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07); margin-bottom: 24px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; font-size: 1.1rem; font-weight: 600; background-color: var(--azul-header); color: #FFFFFF; }
        .form-container { padding: 24px; }
        .form-grid { display: grid; gap: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; width: 100%; }
        .form-group label { font-size: 0.875rem; font-weight: 600; color: var(--texto-label); margin-bottom: 8px; }
        .form-group input, .form-group select {
            font-size: 1rem; color: var(--texto-principal); padding: 12px 16px; 
            border: 1px solid var(--borda-campo); border-radius: 8px; 
            background-color: #FFFFFF; width: 100%;
        }
        @media (max-width: 900px) {
            .container { margin-left: 0; padding: 16px; }
            .form-row, .form-row-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
        <div class="page-header">
            <h1>Novo Produto</h1>
            <a href="/nexus-erp/public/products" class="btn-voltar">Voltar</a>
        </div>

        <form method="POST" action="/nexus-erp/public/products/store">
            <div class="card">
                <div class="card-header">Informações Principais</div>
                <div class="form-container form-grid">
                    <div class="form-group">
                        <label for="description">Descrição*</label>
                        <input type="text" id="description" name="description" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sku">SKU (Código)*</label>
                            <input type="text" id="sku" name="sku" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Tipo*</label>
                            <select id="type" name="type" required>
                                <option value="PRODUCT">Produto</option>
                                <option value="SERVICE">Serviço</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">Informações Fiscais e de Venda</div>
                <div class="form-container form-grid">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label for="ncm_code">NCM*</label>
                            <input type="text" id="ncm_code" name="ncm_code" maxlength="8" required>
                        </div>
                        <div class="form-group">
                            <label for="cest_code">CEST (se aplicável)</label>
                            <input type="text" id="cest_code" name="cest_code" maxlength="7">
                        </div>
                        <div class="form-group">
                            <label for="unit_of_measure">Unidade (UN, KG, CX)*</label>
                            <input type="text" id="unit_of_measure" name="unit_of_measure" maxlength="6" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cost_price">Preço de Custo*</label>
                            <input type="number" id="cost_price" name="cost_price" step="0.01" min="0" value="0.00" required>
                        </div>
                        <div class="form-group">
                            <label for="sale_price">Preço de Venda*</label>
                            <input type="number" id="sale_price" name="sale_price" step="0.01" min="0" value="0.00" required>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 16px 24px;">Salvar Produto</button>
            </div>
        </form>
    </div>
</body>
</html>