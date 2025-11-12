<?php
// O Controller define:
// $activePage, $salesChartData, $productsChartData, $stockChartData, $customerChartData

// Placeholders para segurança
$salesChartData = $salesChartData ?? [['Mês', 'Vendas'], ['Jan', 0]];
$productsChartData = $productsChartData ?? [['Status', 'Quantidade'], ['Nenhum', 1]];
$stockChartData = $stockChartData ?? [['Produto', 'Quantidade', ['role' => 'style']], ['Nenhum', 0, '#3b74e6']];
$customerChartData = $customerChartData ?? [['Mês', 'Novos Clientes'], ['Jan', 0]];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relatórios</title>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  
  <script id="charts-data" type="application/json">
    {
      "sales": <?php echo json_encode($salesChartData); ?>,
      "products": <?php echo json_encode($productsChartData); ?>,
      "stock": <?php echo json_encode($stockChartData); ?>,
      "customers": <?php echo json_encode($customerChartData); ?>
    }
  </script>
  
  <?php js('js/charts.js', ['defer' => true]); ?>
  
  <style>
    body {
      margin: 0;
      display: flex;
      font-family: Arial, sans-serif;
      background-color: #e0e0e0;
      color: #333;
    }
    .conteudo-principal {
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
      display: flex;
      flex-direction: column;
      height: 320px;
    }
    .cartao h2 {
      margin: 0 0 15px 0;
      font-size: 18px;
      color: #333;
      text-align: left;
    }
    .chart-container {
      width: 100%;
      flex-grow: 1;
    }
  </style>
</head>

<body>

  <?php view("components/sidebar", ['activePage' => $activePage]) ?>

  <main class="conteudo-principal">
    <h1>Relatórios</h1>

    <div class="cartoes">
      <div class="cartao">
        <h2>Vendas (Últimos 6 Meses)</h2>
        <div id="vendas_chart_div" class="chart-container"></div>
      </div>
      <div class="cartao">
        <h2>Estoque (Top 5 Produtos)</h2>
        <div id="estoque_chart_div" class="chart-container"></div>
      </div>
      <div class="cartao">
        <h2>Status dos Produtos</h2>
        <div id="donutchart" class="chart-container"></div>
      </div>
      <div class="cartao">
        <h2>Novos Clientes (Últimos 6 Meses)</h2>
        <div id="clientes_chart_div" class="chart-container"></div>
      </div>
    </div>
  </main>
</body>
</html>