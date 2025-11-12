<?php
// O Controller deve definir:
// $activePage
// $customer (array com dados do cliente)
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
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
            <h1>Editar Cliente</h1>
            <a href="/nexus-erp/public/customers" class="btn-voltar">Voltar</a>
        </div>

        <form method="POST" action="/nexus-erp/public/customers/update">
            <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer['id']); ?>">

            <div class="card">
                <div class="card-header">Informações Principais</div>
                <div class="form-container form-grid">
                    <div class="form-group">
                        <label for="name">Razão Social*</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="business_name">Nome Fantasia</label>
                        <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($customer['business_name'] ?? ''); ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="document_type">Tipo de Documento*</label>
                            <select id="document_type" name="document_type" required>
                                <option value="CNPJ" <?php echo (($customer['document_type'] ?? '') == 'CNPJ') ? 'selected' : ''; ?>>CNPJ</option>
                                <option value="CPF" <?php echo (($customer['document_type'] ?? '') == 'CPF') ? 'selected' : ''; ?>>CPF</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="document_number">Número do Documento*</label>
                            <input type="text" id="document_number" name="document_number" value="<?php echo htmlspecialchars($customer['document_number'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="state_registration">Inscrição Estadual</label>
                            <input type="text" id="state_registration" name="state_registration" value="<?php echo htmlspecialchars($customer['state_registration'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="municipal_registration">Inscrição Municipal</label>
                            <input type="text" id="municipal_registration" name="municipal_registration" value="<?php echo htmlspecialchars($customer['municipal_registration'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">Endereço</div>
                <div class="form-container form-grid">
                    <div class="form-row">
                         <div class="form-group" style="flex-grow: 3;">
                            <label for="address_street">Rua*</label>
                            <input type="text" id="address_street" name="address_street" value="<?php echo htmlspecialchars($customer['address_street'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group" style="flex-grow: 1;">
                            <label for="address_number">Número*</label>
                            <input type="text" id="address_number" name="address_number" value="<?php echo htmlspecialchars($customer['address_number'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address_complement">Complemento</label>
                            <input type="text" id="address_complement" name="address_complement" value="<?php echo htmlspecialchars($customer['address_complement'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="address_neighborhood">Bairro*</label>
                            <input type="text" id="address_neighborhood" name="address_neighborhood" value="<?php echo htmlspecialchars($customer['address_neighborhood'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label for="address_city">Cidade*</label>
                            <input type="text" id="address_city" name="address_city" value="<?php echo htmlspecialchars($customer['address_city'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address_state">Estado (UF)*</label>
                            <input type="text" id="address_state" name="address_state" maxlength="2" value="<?php echo htmlspecialchars($customer['address_state'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address_zipcode">CEP*</label>
                            <input type="text" id="address_zipcode" name="address_zipcode" maxlength="8" value="<?php echo htmlspecialchars($customer['address_zipcode'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 16px 24px;">Salvar Alterações</button>
            </div>
        </form>
    </div>
</body>
</html>