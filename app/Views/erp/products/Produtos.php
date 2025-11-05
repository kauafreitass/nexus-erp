<?php

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - NEXUS ERP</title>
    <style>
        body {
            background-color: #e5e5e5;
        }

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

        .link-opcao {
            display: block;
            background: linear-gradient(180deg, #1e48a3, #0e2a66);
            border-radius: 20px;
            padding: 10px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }

        .link-opcao:hover {
            background: linear-gradient(180deg, #2d62d6, #1b3b91);
            transform: translateY(-2px);
        }

        .submenu {
            display: none;
            flex-direction: column;
            background-color: #0e2a66;
            border-radius: 20px;
            margin-top: 8px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
            animation: aparecer 0.3s ease-in-out;
        }

        .item-opcao:hover .submenu {
            display: flex;
        }

        .link-subopcao {
            text-decoration: none;
            color: white;
            padding: 10px;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }

        .link-subopcao:hover {
            background-color: #2458bf;
        }

        @keyframes aparecer {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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


        /* ===== CONTEÚDO ===== */
.content {
  margin-left: 260px; /* espaço lateral igual à barra */
  padding: 40px;
  min-height: 100vh;
  background-color: #e5e5e5;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

/* Caixa da tabela */
.table-container {
  width: 90%;
  background-color: white;
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  overflow: hidden;
}

/* Cabeçalho */
.table-header {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  background-color: #082a78;
  color: white;
  font-weight: bold;
  padding: 15px 25px;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}

/* Linha de produto */
.product-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  align-items: center;
  padding: 15px 25px;
  border-bottom: 1px solid #ddd;
  transition: background 0.2s ease;
}

.product-row:hover {
  background-color: #f1f4ff;
}

.product-row:last-child {
  border-bottom: none;
}

/* Quando não há produtos */
.empty-message {
  padding: 30px;
  text-align: center;
  color: #666;
  font-style: italic;
}

    </style>
</head>

<body>

    <nav class="menu-lateral">
        <div class="area-logo">
            <img src="images/nexus-logo.png" alt="Logo do sistema">
        </div>

        <ul class="lista-opcoes">
            <li class="item-opcao">
                <a href="#" class="link-opcao">Vendas ▾</a>
                <div class="submenu">
                    <a href="#" class="link-subopcao">Pedidos</a>
                    <a href="#" class="link-subopcao">Notas Fiscais</a>
                </div>
            </li>

            <li class="item-opcao">
                <a href="#" class="link-opcao">Cadastros ▾</a>
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
            <a href="#">Minha conta</a>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
   
    <div class="content">
        <div class="table-container">
            <div class="table-header">
                <div>Descrição</div>
                <div>Código</div>
                <div>Unidade</div>
                <div>Preço</div>
            </div>

            <!-- Aqui futuramente vai o loop dos produtos -->
            <div class="empty-message">Nenhum produto cadastrado.</div>
        </div>
    </div>


</body>

</html>