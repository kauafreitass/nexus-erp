<style>
    /* ===== MENU LATERAL (sem alterações) ===== */
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
        box-shadow: none; /* Sem sombra quando fechado */

        overflow: hidden;
        position: relative;
        z-index: 1;

        /* Anima TODAS as propriedades que vamos mudar */
        transition: max-height 0.3s ease-in-out, margin-top 0.3s ease-in-out, padding 0.3s ease-in-out, box-shadow 0.4s ease;
    }

    /* ESTADO ATIVO (ABERTO E VISÍVEL) */
    .item-opcao.active .submenu {
        max-height: 200px; /* Expande a altura */

        /* Aplica os estilos para o efeito contínuo SÓ QUANDO for clicado */
        margin-top: -10px; /* Puxa para trás do botão */
        padding-top: 15px; /* Adiciona o espaço interno */
        padding-bottom: 5px;
        box-shadow: 0 5px 8px rgba(0, 0, 0, 0.4); /* Adiciona a sombra */
    }

    /* LINKS DO SUBMENU (EX: PEDIDOS) */
    .link-subopcao {
        display: block;
        text-decoration: none;
        color: white;
        padding: 12px 20px; /* Mais padding para facilitar o clique */
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


    /* ===== MINHA CONTA (sem alterações) ===== */
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
<body>
<nav class="menu-lateral">
    <div class="area-logo">
        <img src="images/nexus-logo.png" alt="Logo do sistema">
    </div>

    <ul class="lista-opcoes">
        <li class="item-opcao">
            <span class="link-opcao">Vendas <span class="arrow"></span></span>
            <div class="submenu">
                <a href="#" class="link-subopcao">Pedidos</a>
                <a href="#" class="link-subopcao">Notas Fiscais</a>
            </div>
        </li>

        <li class="item-opcao">
            <span class="link-opcao">Cadastros <span class="arrow"></span></span>
            <div class="submenu">
                <a href="#" class="link-subopcao">Produtos</a>
                <a href="#" class="link-subopcao">Clientes</a>
            </div>
        </li>

        <li class="item-opcao">
            <a href="#" class="link-opcao">Suprimentos</a>
        </li>

        <li class="item-opcao">
            <a href="#" class="link-opcao">Relatórios</a>
        </li>
    </ul>

    <div class="minha-conta">
        <a href="acccount">Minha conta</a>
    </div>
</nav>

<?php js("js/sidebar.js", ["defer" => true]); ?>
</body>