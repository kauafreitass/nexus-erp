<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <?php js('js/charts.js', ['defer' => true]); ?>
  <style>
    body {
      margin: 0;
      display: flex;
      font-family: Arial, sans-serif;
      background-color: #e0e0e0;
      color: #333;
    }

    /* ===== CONTEÚDO PRINCIPAL ===== */
    .conteudo-principal {
      /* Simulação do seu sidebar, remova se estiver usando o PHP */
      margin-left: 260px;
      padding: 40px;
      flex: 1;
    }

    .conteudo-principal h1 {
      margin-top: 0;
      color: #333;
    }

    .cartoes {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .cartao {
      background-color: white;
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      /* Adicionado para alinhar o conteúdo e definir uma altura fixa */
      display: flex;
      flex-direction: column;
      height: 320px;
      /* Garante que todos os cards tenham a mesma altura */
    }

    .cartao h2 {
      /* CSS do título ajustado */
      margin: 0 0 15px 0;
      /* Remove a altura fixa e adiciona margem inferior */
      font-size: 18px;
      color: #333;
      text-align: left;
      /* Títulos alinhados à esquerda são mais comuns em dashboards */
    }

    .chart-container {
      width: 100%;
      flex-grow: 1;
      /* Faz o gráfico ocupar o espaço restante no card */
    }
  </style>
</head>

<body>

  <?php view("components/sidebar") ?>

  <main class="conteudo-principal">
    <h1>Bom dia, Kauã!</h1>

    <div class="cartoes">
      <div class="cartao">
        <h2>Vendas</h2>
        <div id="vendas_chart_div" class="chart-container"></div>
      </div>
      <div class="cartao">
        <h2>Estoque</h2>
        <div id="estoque_chart_div" class="chart-container"></div>
      </div>
      <div class="cartao">
        <h2>Produtos</h2>
        <div id="donutchart" class="chart-container"></div>
      </div>
      <div class="cartao">
        <h2>Clientes</h2>
        <div id="clientes_chart_div" class="chart-container"></div>
      </div>
    </div>
  </main>
</body>

</html>