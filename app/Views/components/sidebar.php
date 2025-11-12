<?php
// A variável $activePage é recebida da função view()
$activePage = $activePage ?? '';

// Para verificar permissões
$permissions = $_SESSION['permissions'] ?? [];

// Função auxiliar para verificar permissão
function hasPermission($permissionName, $permissionsArray)
{
    return in_array($permissionName, $permissionsArray);
}
?>

<body>
    <nav class="menu-lateral">
        <div class="area-logo">
            <a href="/nexus-erp/public/dashboard"><img src="<?= asset('images/nexus-logo.png') ?>"
                    alt="Logo do sistema"></a>
        </div>

        <ul class="lista-opcoes">
            <li class="item-opcao">
                <a href="/nexus-erp/public/dashboard"
                    class="link-opcao <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
            </li>

            <?php
            $isVendasActive = in_array($activePage, ['sales', 'nfe']);
            ?>
            <li class="item-opcao <?php echo $isVendasActive ? 'active' : ''; ?>">
                <span class="link-opcao">Vendas <span class="arrow"></span></span>
                <div class="submenu">
                    <?php if (hasPermission('sales_orders_view', $permissions)): ?>
                        <a href="/nexus-erp/public/sales"
                            class="link-subopcao <?php echo ($activePage === 'sales') ? 'active' : ''; ?>">Pedidos</a>
                    <?php endif; ?>
                    <?php if (hasPermission('nfe_view', $permissions)): ?>
                        <a href="/nexus-erp/public/nfe"
                            class="link-subopcao <?php echo ($activePage === 'nfe') ? 'active' : ''; ?>">Notas Fiscais</a>
                    <?php endif; ?>
                </div>
            </li>

            <?php
            $isCadastrosActive = in_array($activePage, ['products', 'customers']);
            ?>
            <li class="item-opcao <?php echo $isCadastrosActive ? 'active' : ''; ?>">
                <span class="link-opcao">Cadastros <span class="arrow"></span></span>
                <div class="submenu">
                    <?php if (hasPermission('products_view', $permissions)): ?>
                        <a href="/nexus-erp/public/products"
                            class="link-subopcao <?php echo ($activePage === 'products') ? 'active' : ''; ?>">Produtos</a>
                    <?php endif; ?>
                    <?php if (hasPermission('products_view', $permissions)): ?>
                        <a href="/nexus-erp/public/categories"
                            class="link-subopcao <?php echo ($activePage === 'categories') ? 'active' : ''; ?>">Categorias</a>
                    <?php endif; ?>
                    <?php if (hasPermission('customers_view', $permissions)): ?>
                        <a href="/nexus-erp/public/customers"
                            class="link-subopcao <?php echo ($activePage === 'customers') ? 'active' : ''; ?>">Clientes</a>
                    <?php endif; ?>
                </div>
            </li>


            <?php
            // 1. Verifica se a página ativa é 'reports' (para o menu)
            $isRelatoriosActive = ($activePage === 'reports');
            ?>
            <?php if (hasPermission('reports_sales_view', $permissions)): ?>
                <li class="item-opcao <?php echo $isRelatoriosActive ? 'active' : ''; ?>">
                    <span class="link-opcao">Relatórios <span class="arrow"></span></span>
                    <div class="submenu">
                        <a href="/nexus-erp/public/reports/dashboard" class="link-subopcao">Dashboard Gráfico</a>
                        <a href="/nexus-erp/public/reports/sales" class="link-subopcao">Relatório de Vendas</a>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (hasPermission('inventory_view', $permissions)): ?>
                <li class="item-opcao">
                    <a href="/nexus-erp/public/supplies"
                        class="link-opcao <?php echo ($activePage === 'supplies') ? 'active' : ''; ?>">Suprimentos</a>
                </li>
            <?php endif; ?>

            <?php if (hasPermission('users_manage', $permissions)): ?>
                <li class="item-opcao">
                    <a href="/nexus-erp/public/users"
                        class="link-opcao <?php echo ($activePage === 'users') ? 'active' : ''; ?>">Gerenciar Usuários</a>
                </li>
            <?php endif; ?>

        </ul>

        <div class="minha-conta">
            <a href="/nexus-erp/public/account" class="<?php echo ($activePage === 'account') ? 'active' : ''; ?>">Minha
                conta</a>
        </div>
    </nav>

    <?php js("js/sidebar.js", ["defer" => true]); ?>
</body>
<style>
    /* ===== MENU LATERAL ===== */
    .menu-lateral {
        width: 240px;
        height: 100%;
        background-color: #0b1533;
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 30px;
        border-top-right-radius: 100px;
        border-bottom-right-radius: 100px;
        box-shadow: 20px 1px 10px rgba(0, 0, 0, 0.3);
    }

    .area-logo img {
        width: 80px;
        height: 80px;
        margin-bottom: 40px;
    }

    .lista-opcoes {
        list-style: none;
        padding: 0;
        margin: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-grow: 1;
    }

    .item-opcao {
        position: relative;
        width: 80%;
        text-align: center;
        margin-bottom: 10px;
    }

    /* LINK PRINCIPAL (EX: VENDAS) */
    .link-opcao {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(180deg, #1e48a3, #0e2a66);
        border-radius: 20px;
        padding: 10px 15px;
        color: white;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        cursor: pointer;
        position: relative;
        z-index: 2;
        transition: transform 0.3s ease, border-radius 0.1s ease 0.3s;
    }

    .link-opcao:hover {
        transform: translateY(-2px);
    }

    /* LINK PRINCIPAL QUANDO O MENU ESTÁ ATIVO */
    .item-opcao.active .link-opcao {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        box-shadow: none;
        transform: translateY(0);

        transition: transform 0.3s ease, border-radius 0.1s ease 0.08s;
    }

    /* ÍCONE DA SETA */
    .arrow {
        border: solid white;
        border-width: 0 2px 2px 0;
        display: inline-block;
        padding: 3px;
        transform: rotate(45deg);
        transition: transform 0.4s ease;
    }

    .item-opcao.active .arrow {
        transform: rotate(-135deg);
    }


    /* SUBMENU - LÓGICA CORRIGIDA */
    .submenu {
        /* ESTADO PADRÃO (FECHADO E INVISÍVEL) */
        background-color: #0e2a66;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;

        /* Garantem que ele está 100% recolhido e sem espaço vertical */
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
        margin-top: 0;
        box-shadow: none;
        /* Sem sombra quando fechado */

        overflow: hidden;
        position: relative;
        z-index: 1;

        /* Anima TODAS as propriedades que vamos mudar */
        transition: max-height 0.3s ease-in-out, margin-top 0.3s ease-in-out, padding 0.3s ease-in-out, box-shadow 0.4s ease;
    }

    /* ESTADO ATIVO (ABERTO E VISÍVEL) */
    .item-opcao.active .submenu {
        max-height: 200px;
        /* Expande a altura */

        /* Aplica os estilos para o efeito contínuo SÓ QUANDO for clicado */
        margin-top: -10px;
        /* Puxa para trás do botão */
        padding-top: 15px;
        /* Adiciona o espaço interno */
        padding-bottom: 5px;
        box-shadow: 0 5px 8px rgba(0, 0, 0, 0.4);
        /* Adiciona a sombra */
    }

    /* LINKS DO SUBMENU (EX: PEDIDOS) */
    .link-subopcao {
        display: block;
        text-decoration: none;
        color: white;
        padding: 12px 20px;
        /* Mais padding para facilitar o clique */
        transition: background-color 0.3s ease;
        text-align: left;
    }

    .link-subopcao:hover {
        background-color: #2458bf;
    }

    /* Garante que o hover no último item mantenha a borda arredondada */
    .submenu a:last-child:hover {
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    /* Define o fundo azul claro para o item de submenu ativo (ex: "Pedidos") */
    .submenu .link-subopcao.active {
        background-color: #2458BF;
        /* Cor azul claro da sua imagem */
        font-weight: 600;
        border-radius: 8px;
        /* Adiciona um leve arredondamento */
    }

    /* Define o fundo azul claro para links principais ativos (ex: "Suprimentos") */
    .item-opcao>a.link-opcao.active {
        background: linear-gradient(180deg, #2458BF, #1e48a3);
        /* Destaque azul claro */
        font-weight: 600;
    }

    /* Define o fundo azul claro para "Minha Conta" quando ativo */
    .minha-conta a.active {
        background: linear-gradient(180deg, #2458BF, #1e48a3);
        /* Destaque azul claro */
    }


    /* ===== MINHA CONTA ===== */
    .minha-conta {
        margin-bottom: 20px;
        width: 80%;
    }

    .minha-conta a {
        display: block;
        background: linear-gradient(180deg, #1e48a3, #0e2a66);
        border-radius: 20px;
        padding: 10px;
        color: white;
        text-align: center;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
        margin-top: -100px;
    }

    .minha-conta a:hover {
        background: linear-gradient(180deg, #2d62d6, #1b3b91);
        transform: translateY(-2px);
    }
</style>