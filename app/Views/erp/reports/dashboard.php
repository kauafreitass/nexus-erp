<?php
// O Controller agora define:
// $activePage, $salesByStatusChartData, $totalSalesByMonthChartData, 
// $topCustomersChartData, $topSellingProductsChartData
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Relatórios</title>
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
        /* ALTERAÇÃO AQUI: Para duas colunas de tamanho igual */
        .card-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .card { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07); padding: 24px; }
        .card h2 { font-size: 1.2rem; color: var(--texto-principal); margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .chart-container { width: 100%; height: 350px; display: flex; justify-content: center; align-items: center; }
        .no-data { text-align: center; color: var(--texto-label); }
        .btn-relatorios { display: inline-block; background-color: var(--azul-header); color: #FFFFFF; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; }

        @media (max-width: 900px) { /* Em telas menores, volta para uma coluna para não quebrar o layout */
            .container { margin-left: 0; padding: 16px; } 
            .card-grid { grid-template-columns: 1fr; } 
        }
    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // Chart 1: Vendas por Status do Pedido (Pie Chart ou Bar)
            drawSalesByStatusChart();
            // Chart 2: Vendas Totais por Mês (Line Chart)
            drawTotalSalesByMonthChart();
            // Chart 3: Top 5 Clientes (Bar Chart Horizontal)
            drawTopCustomersChart();
            // Chart 4: Top 5 Produtos/Serviços (Bar Chart Horizontal)
            drawTopSellingProductsChart();
        }

        function drawSalesByStatusChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($salesByStatusChartData); ?>);
            var options = {
                title: 'Vendas por Status do Pedido',
                pieHole: 0.4,
                colors: ['#3366CC', '#DC3912', '#FF9900', '#109618', '#990099'], // Azul, Vermelho, Laranja, Amarelo, Verde, Roxo
                legend: { position: 'bottom' }
            };
            var chart = new google.visualization.PieChart(document.getElementById('salesByStatusChart'));
            chart.draw(data, options);
        }

        function drawTotalSalesByMonthChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($totalSalesByMonthChartData); ?>);
            var options = {
                title: 'Vendas Totais por Mês (Últimos 12 meses)',
                legend: { position: 'bottom' },
                hAxis: { title: 'Mês' },
                vAxis: { title: 'Valor Total (R$)' }
            };
            var chart = new google.visualization.LineChart(document.getElementById('totalSalesByMonthChart'));
            chart.draw(data, options);
        }

        function drawTopCustomersChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($topCustomersChartData); ?>);
            var options = {
                title: 'Top 5 Clientes por Valor de Venda',
                chartArea: { left: 100, top: 20, width: '70%', height: '80%' },
                hAxis: { title: 'Valor Total (R$)', minValue: 0 },
                vAxis: { title: 'Cliente' },
                bars: 'horizontal',
                legend: { position: 'none' }
            };
            var chart = new google.charts.Bar(document.getElementById('topCustomersChart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }

        function drawTopSellingProductsChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($topSellingProductsChartData); ?>);
            var options = {
                title: 'Top 5 Produtos/Serviços Mais Vendidos (em Valor)',
                chartArea: { left: 100, top: 20, width: '70%', height: '80%' },
                hAxis: { title: 'Receita Gerada (R$)', minValue: 0 },
                vAxis: { title: 'Produto/Serviço' },
                bars: 'horizontal',
                legend: { position: 'none' }
            };
            var chart = new google.charts.Bar(document.getElementById('topSellingProductsChart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>
</head>
<body>
    <?php view("components/sidebar", ['activePage' => $activePage]) ?>

    <div class="container">
        <div class="page-header">
            <h1>Dashboard de Relatórios</h1>
            <div>
                <a href="/nexus-erp/public/reports/sales" class="btn-relatorios">Ver Relatório de Vendas Detalhado</a>
            </div>
        </div>

        <div class="card-grid">
            <div class="card">
                <h2>Vendas por Status do Pedido</h2>
                <div id="salesByStatusChart" class="chart-container">
                    <?php if (count($salesByStatusChartData) <= 1): ?><div class="no-data">Nenhum dado de vendas por status.</div><?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h2>Vendas Totais por Mês</h2>
                <div id="totalSalesByMonthChart" class="chart-container">
                    <?php if (count($totalSalesByMonthChartData) <= 1): ?><div class="no-data">Nenhum dado de vendas mensais.</div><?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h2>Top 5 Clientes</h2>
                <div id="topCustomersChart" class="chart-container">
                    <?php if (count($topCustomersChartData) <= 1): ?><div class="no-data">Nenhum dado de clientes.</div><?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h2>Top 5 Produtos/Serviços Mais Vendidos</h2>
                <div id="topSellingProductsChart" class="chart-container">
                    <?php if (count($topSellingProductsChartData) <= 1): ?><div class="no-data">Nenhum dado de produtos/serviços.</div><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>