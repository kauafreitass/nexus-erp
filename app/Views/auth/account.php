<?php
// Garante que a sessão seja iniciada para ler as variáveis
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// O Controller (showAccount) agora envia $company
$company = $company ?? []; 

// Pega e limpa as mensagens da sessão para exibição
$message = $_SESSION['message'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['message'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha conta</title>
<style>
    /* --- (Seu CSS original completo) --- */
    :root {
        --fundo-pagina: #F0F2F5; --card-bg: #FFFFFF; --texto-principal: #050C1B;
        --texto-label: #555; --azul-header: #183F8C; --azul-botao: #183F8C;
        --azul-destaque: #2458BF; --borda-campo: #B0B0B0; --cor-erro: #D93025;
        --cor-sucesso: #1E8E3E; --vermelho: #dc3545; 
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--fundo-pagina); color: var(--texto-principal); }
    .container { display: flex; flex-direction: column; gap: 24px; width: 100%; flex-grow: 1; max-width: 1400px; padding: 32px; align-items: flex-start; margin-left: 260px; }
    .row { display: flex; gap: 24px; width: 100%; margin-bottom: 24px; }
    .profile-column { flex-basis: 25%; flex-shrink: 0; flex-grow: 0; }
    .security-column { flex-basis: 600px; flex-shrink: 0; flex-grow: 1; }
    .account-data-column { flex-basis: 500px; flex-grow: 1; min-width: 500px; }
    .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07); padding: 24px 32px; }
    .profile-card { display: flex; flex-direction: column; align-items: center; padding: 40px 24px; }
    .avatar { width: 120px; height: 120px; border-radius: 50%; border: 2px solid var(--texto-principal); background-color: #e0e0e0; margin-bottom: 16px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .avatar svg { width: 60px; height: 60px; color: var(--texto-principal); }
    .profile-name-button { background-color: var(--azul-botao); color: #FFFFFF; border: none; border-radius: 20px; padding: 10px 24px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
    .profile-name-button:hover { background-color: var(--azul-destaque); }
    .btn-logout { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background-color: var(--vermelho); color: #FFFFFF; border: none; border-radius: 20px; padding: 10px 24px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s; text-decoration: none; margin-top: 16px; }
    .btn-logout:hover { background-color: #c82333; }
    .btn-logout svg { width: 20px; height: 20px; stroke-width: 2.5; }
    .form-section-wrapper { display: flex; flex-direction: column; gap: 8px; width: 100%; }
    .section-header { background-color: var(--azul-header); border-radius: 12px; padding: 16px 32px; }
    .section-header h3 { font-size: 1.25rem; color: #FFFFFF; font-weight: 600; }
    .form-grid { display: grid; gap: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; width: 100%; }
    .form-group-wrapper { position: relative; }
    .form-group label { font-size: 0.875rem; font-weight: 600; color: var(--texto-label); margin-bottom: 8px; }
    .save-group { display: flex; justify-content: flex-end; }
    .save-btn { background-color: var(--azul-botao); color: #FFFFFF; border: none; border-radius: 20px; padding: 10px 24px; font-size: 1.2rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
    .save-btn:hover { background-color: var(--azul-destaque); }
    #security-section input { font-size: 1rem; color: var(--texto-principal); padding: 8px 0px; border: none; border-bottom: 1px solid var(--borda-campo); border-radius: 0; background-color: transparent; width: 100%; padding-right: 30px; }
    #security-section input:focus { outline: none; border-bottom-color: var(--azul-destaque); box-shadow: none; }
    #security-section .edit-icon { position: absolute; right: 5px; bottom: 8px; color: var(--azul-destaque); cursor: pointer; }
    #account-data-section input, #account-data-section select { 
        font-size: 1rem; color: var(--texto-principal); padding: 12px 16px; 
        border: 1px solid var(--borda-campo); border-radius: 8px; 
        background-color: #FFFFFF; width: 100%; transition: all 0.2s ease;
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
    }
    #account-data-section select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23555' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 1rem center;
        background-size: 1em 1em; padding-right: 2.5rem; 
    }
    #account-data-section input:focus, #account-data-section select:focus { outline: none; border-color: var(--azul-destaque); box-shadow: 0 0 0 3px rgba(36, 88, 191, 0.15); }
    .alert { padding: 16px; border-radius: 8px; font-weight: 600; margin-bottom: 16px; text-align: center; }
    .alert-success { background-color: #d1e7dd; color: var(--cor-sucesso); }
    .alert-error { background-color: #f8d7da; color: var(--cor-erro); }
    @media (max-width: 1100px) {
        .container { margin-left: 0; flex-direction: column; align-items: center; }
        .row { flex-direction: column; }
        .profile-column, .security-column, .account-data-column { flex-basis: 100%; width: 100%; max-width: 500px; min-width: initial; }
    }
    @media (max-width: 600px) {
        .form-row, .form-row-3 { grid-template-columns: 1fr; }
        .container { padding: 16px; }
    }
    .modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; pointer-events: none; transition: opacity 0.3s ease-in-out; }
    .modal-overlay.active { opacity: 1; pointer-events: auto; }
    .modal-content { background: var(--card-bg); border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); transform: scale(0.95); transition: transform 0.3s ease-in-out; }
    .modal-overlay.active .modal-content { transform: scale(1); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #eee; }
    .modal-header h3 { font-size: 1.5rem; color: var(--texto-principal); }
    .modal-close-btn { cursor: pointer; color: var(--texto-label); width: 24px; height: 24px; }
    .modal-body { padding: 24px; display: grid; gap: 16px; }
    .modal-body .form-group input { font-size: 1rem; color: var(--texto-principal); padding: 12px 16px; border: 1px solid var(--borda-campo); border-radius: 8px; background-color: #FFFFFF; width: 100%; transition: all 0.2s ease; }
    .modal-body .form-group input:focus { outline: none; border-color: var(--azul-destaque); box-shadow: 0 0 0 3px rgba(36, 88, 191, 0.15); }
    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 20px 24px; background-color: #f9f9f9; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
    .btn { border: none; border-radius: 8px; padding: 10px 20px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-primary { background-color: var(--azul-botao); color: white; }
    .btn-primary:hover { background-color: var(--azul-destaque); }
    .btn-secondary { background-color: #eee; color: var(--texto-label); }
    .btn-secondary:hover { background-color: #ddd; }
</style>
</head>

<body>

    <svg width="0" height="0" style="position:absolute">
        <defs>
            <symbol id="icon-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </symbol>
            <symbol id="icon-pencil" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </symbol>
            <symbol id="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </symbol>
            <symbol id="icon-log-out" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </symbol>
        </defs>
    </svg>

    <?php 
    $activePage = $activePage ?? 'account';
    view("components/sidebar", ['activePage' => $activePage]);
    ?>

    <div class="container">

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="profile-column">
                <div class="card profile-card">
                    <div class="avatar">
                        <svg><use href="#icon-user"></use></svg>
                    </div>
                    <button class="profile-name-button"><?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Usuário'); ?></button>
                    
                    <a href="/nexus-erp/public/logout" class="btn-logout">
                        <svg><use href="#icon-log-out"></use></svg>
                        <span>Sair</span>
                    </a>
                </div>
            </div>
            <div class="security-column">
                <div class="form-section-wrapper" id="security-section">
                    <div class="section-header">
                        <h3>Segurança (Dados do Usuário)</h3>
                    </div>
                    <div class="card">
                        <div class="form-grid">
                            <div class="form-group-wrapper form-group">
                                <label for="email">E-mail de Acesso</label>
                                <input type="email" id="email" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>" readonly>
                                <svg class="edit-icon" id="open-email-modal" width="20" height="20">
                                    <use href="#icon-pencil"></use>
                                </svg>
                            </div>
                            <div class="form-group-wrapper form-group">
                                <label for="senha">Senha</label>
                                <input type="password" id="senha" value="************" readonly>
                                <svg class="edit-icon" id="open-password-modal" width="20" height="20">
                                    <use href="#icon-pencil"></use>
                                </svg>
                            </div>
                            <div class="form-group-wrapper form-group">
                                <label for="usuario">Nome de Usuário</label>
                                <input type="text" id="name" value="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? ''); ?>" readonly>
                                <svg class="edit-icon" id="open-name-modal" width="20" height="20">
                                    <use href="#icon-pencil"></use>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="account-data-column">
                <div class="form-section-wrapper" id="account-data-section">
                    <div class="section-header">
                        <h3>Dados da Empresa</h3>
                    </div>
                    <div class="card">
                        <form class="form-grid" id="account-details-form" method="POST" action="/nexus-erp/public/account/update">
                            
                            <input type="hidden" name="action" value="update_company_data">
                            
                            <div class="form-group">
                                <label for="company-name">Razão Social*</label>
                                <input type="text" id="company-name" name="name" value="<?php echo htmlspecialchars($company['name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="company-business-name">Nome Fantasia</label>
                                <input type="text" id="company-business-name" name="business_name" value="<?php echo htmlspecialchars($company['business_name'] ?? ''); ?>">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="company-doc">Documento (CNPJ/CPF)*</label>
                                    <input type="text" id="company-doc" name="document_number" value="<?php echo htmlspecialchars($company['document_number'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="company-phone">Telefone</label>
                                    <input type="text" id="company-phone" name="phone" value="<?php echo htmlspecialchars($company['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="company-email">E-mail (da Empresa)</label>
                                <input type="email" id="company-email" name="email" value="<?php echo htmlspecialchars($company['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="company-ie">Inscrição Estadual</label>
                                    <input type="text" id="company-ie" name="state_registration" value="<?php echo htmlspecialchars($company['state_registration'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="company-im">Inscrição Municipal</label>
                                    <input type="text" id="company-im" name="municipal_registration" value="<?php echo htmlspecialchars($company['municipal_registration'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="company-legal-nature">Natureza Jurídica</label>
                                <select id="company-legal-nature" name="legal_nature">
                                    <?php $ln = $company['legal_nature'] ?? ''; ?>
                                    <option value="MEI" <?php echo ($ln == 'MEI') ? 'selected' : ''; ?>>MEI</option>
                                    <option value="EI" <?php echo ($ln == 'EI') ? 'selected' : ''; ?>>EI</option>
                                    <option value="SLU" <?php echo ($ln == 'SLU') ? 'selected' : ''; ?>>SLU</option>
                                    <option value="LTDA" <?php echo ($ln == 'LTDA') ? 'selected' : ''; ?>>LTDA</option>
                                    <option value="SA" <?php echo ($ln == 'SA') ? 'selected' : ''; ?>>SA</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="company-street">Endereço (Rua, Av.)</label>
                                <input type="text" id="company-street" name="address_street" value="<?php echo htmlspecialchars($company['address_street'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="company-number">Número</label>
                                    <input type="text" id="company-number" name="address_number" value="<?php echo htmlspecialchars($company['address_number'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="company-complement">Complemento</label>
                                    <input type="text" id="company-complement" name="address_complement" value="<?php echo htmlspecialchars($company['address_complement'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="company-neighborhood">Bairro</label>
                                    <input type="text" id="company-neighborhood" name="address_neighborhood" value="<?php echo htmlspecialchars($company['address_neighborhood'] ?? ''); ?>">
                                </div>
                                 <div class="form-group">
                                    <label for="company-zipcode">Código Postal (CEP)</label>
                                    <input type="text" id="company-zipcode" name="address_zipcode" value="<?php echo htmlspecialchars($company['address_zipcode'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label for="company-city">Cidade</label>
                                    <input type="text" id="company-city" name="address_city" value="<?php echo htmlspecialchars($company['address_city'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="company-state">Estado (UF)</label>
                                    <input type="text" id="company-state" name="address_state" maxlength="2" value="<?php echo htmlspecialchars($company['address_state'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="company-country">Cód. País</label>
                                    <input type="text" id="company-country" name="address_country_code" maxlength="4" value="<?php echo htmlspecialchars($company['address_country_code'] ?? '1058'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group" style="border-top: 1px solid #eee; padding-top: 20px;">
                                <label for="current_password_company">Sua Senha Atual (para confirmar)</label>
                                <input type="password" id="current_password_company" name="current_password" required>
                            </div>
                            
                            <div class="save-group">
                                <button type="submit" id="save-company-btn" class="save-btn">Salvar Alterações</button>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    
    <div class="modal-overlay" id="password-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Mudar senha de acesso</h3>
                <svg class="modal-close-btn close-modal"><use href="#icon-close"></use></svg>
            </div>
            <form class="modal-body" method="POST" action="/nexus-erp/public/account/update">
                <input type="hidden" name="action" value="update_password">
                <div class="form-group">
                    <label for="current-password">Senha atual (para confirmar)</label>
                    <input type="password" id="current-password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new-password">Nova senha</label>
                    <input type="password" id="new-password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirmar nova senha</label>
                    <input type="password" id="confirm-password" name="confirm_password" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="email-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Mudar e-mail de acesso</h3>
                <svg class="modal-close-btn close-modal"><use href="#icon-close"></use></svg>
            </div>
            <form class="modal-body" method="POST" action="/nexus-erp/public/account/update">
                <input type="hidden" name="action" value="update_email">
                <div class="form-group">
                    <label for="new-email">Novo e-mail</label>
                    <input type="email" id="new-email" name="new_email" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="current-password-email">Senha atual (para confirmar)</label>
                    <input type="password" id="current-password-email" name="current_password" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="name-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Mudar nome de usuário</h3>
                <svg class="modal-close-btn close-modal"><use href="#icon-close"></use></svg>
            </div>
            <form class="modal-body" method="POST" action="/nexus-erp/public/account/update">
                <input type="hidden" name="action" value="update_name">
                <div class="form-group">
                    <label for="new-name">Novo nome</label>
                    <input type="text" id="new-name" name="new_name" value="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="current-password-name">Senha atual (para confirmar)</label>
                    <input type="password" id="current-password-name" name="current_password" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // Mapeia botões de ABRIR para seus modais
            const modalMap = {
                'open-password-modal': 'password-modal',
                'open-email-modal': 'email-modal',
                'open-name-modal': 'name-modal'
                // Botão 'open-confirm-modal-btn' foi removido
            };

            const openModal = (modalId) => {
                const modal = document.getElementById(modalId);
                if (modal) modal.classList.add('active');
            };

            const closeModal = (modal) => {
                if (modal) modal.classList.remove('active');
            };

            // Adiciona evento de "abrir" para cada botão
            Object.keys(modalMap).forEach(buttonId => {
                const openBtn = document.getElementById(buttonId);
                const modalId = modalMap[buttonId];
                
                if (openBtn) {
                    openBtn.addEventListener('click', () => {
                        openModal(modalId);
                    });
                }
            });

            // Adiciona evento de "fechar" para todos os botões de fechar
            const closeButtons = document.querySelectorAll('.close-modal');
            closeButtons.forEach(button => {
                const modal = button.closest('.modal-overlay');
                button.addEventListener('click', () => closeModal(modal));
            });

            // Adiciona evento de "fechar" ao clicar no fundo (overlay)
            const modalOverlays = document.querySelectorAll('.modal-overlay');
            modalOverlays.forEach(overlay => {
                overlay.addEventListener('click', (event) => {
                    if (event.target === overlay) {
                        closeModal(overlay);
                    }
                });
            });
        });
    </script>
</body>
</html>