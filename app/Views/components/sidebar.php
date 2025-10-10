<?php

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: #d9d9d9;
      display: flex;
      justify-content: flex-start;
    }

    .sidebar {
      width: 220px;
      height: 100vh;
      background: linear-gradient(180deg, #0a1931 0%, #001233 100%);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 15px;
      border-radius: 0 100px 100px 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.3);
    }

    .logo {
      text-align: center;
      margin-bottom: 20px;
    }

    .logo img {
      width: 60px;
    }

    .menu {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .menu-group {
      display: flex;
      flex-direction: column;
    }

    .menu-btn,
    .menu-item {
      background: #13294b;
      color: white;
      border: none;
      border-radius: 15px;
      padding: 10px;
      text-align: left;
      font-weight: 600;
    }

    .menu-btn.active {
      background: #2d5be3;
    }


    .account-btn {
      width: 80%;
      background: #1d3c7c;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 10px;
      font-weight: 1000;
      position: relative;
      bottom: 60px;
    }
  </style>
</head>
<body>

  <aside class="sidebar">
    <div class="logo">
     
      <img src="../public/images/nexus-logo.png" alt="Logo">
    </div>

    <nav class="menu">
      <div class="menu-group">
        <select class="menu-btn active">Vendas
        <ul class="submenu">
          <option><a href="#">Pedidos</a></option>
          <option><a href="#">Notas Fiscais</a></option>
        </ul>
        </select>
      </div>

      <div class="menu-group">
        <select class="menu-btn">Cadastros
        <ul class="submenu">
          <option><a href="#">Produtos</a></option>
          <option><a href="#">Clientes</a></option>
        </ul>
        </select>
      </div>

      <button class="menu-item">Suprimentos</button>
      <button class="menu-item">Relatórios</button>
    </nav>

    <div class="bottom">
      <button class="account-btn">Minha conta</button>
    </div>
  </aside>

</body>
</html>
