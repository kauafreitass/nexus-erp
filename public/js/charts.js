// Carrega o pacote 'corechart' que é necessário para os gráficos
google.charts.load('current', { 'packages': ['corechart'] });

// Define uma única função de callback que irá chamar todas as outras funções de desenho
google.charts.setOnLoadCallback(drawAllCharts);

// Função principal que coordena o desenho de todos os gráficos
function drawAllCharts() {
  drawSalesChart();
  drawStockChart();
  drawProductsChart();
  drawClientsChart();
}

// Função para desenhar o Gráfico de Vendas (Linha)
function drawSalesChart() {
  var data = google.visualization.arrayToDataTable([
    ['Mês', 'Vendas'],
    ['Jan', 750],
    ['Fev', 900],
    ['Mar', 1250],
    ['Abr', 1550]
  ]);

  var options = {
    legend: { position: 'none' },
    colors: ['#3b74e6'],
    backgroundColor: 'transparent',
    vAxis: {
      ticks: [0, 750, 1500],
      minValue: 0
    },
    chartArea: { left: 40, top: 20, width: '90%', height: '80%' }
  };

  var chart = new google.visualization.LineChart(document.getElementById('vendas_chart_div'));
  chart.draw(data, options);
}

// Função para desenhar o Gráfico de Estoque (Colunas)
function drawStockChart() {
  var data = google.visualization.arrayToDataTable([
    ['Categoria', 'Quantidade', { role: 'style' }],
    ['A', 280, '#3b74e6'],
    ['B', 700, '#3b74e6'],
    ['C', 1450, '#3b74e6']
  ]);

  var options = {
    legend: { position: 'none' },
    backgroundColor: 'transparent',
    bar: { groupWidth: '50%' }, // Largura das barras
    vAxis: {
      ticks: [0, 750, 1500],
      minValue: 0
    },
    chartArea: { left: 40, top: 20, width: '85%', height: '80%' }
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('estoque_chart_div'));
  chart.draw(data, options);
}

// Função para desenhar o Gráfico de Produtos (Rosca) - o que você já tinha
function drawProductsChart() {
  var data = google.visualization.arrayToDataTable([
    ['Status', 'Quantidade'],
    ['Em estoque', 60],
    ['Baixo estoque', 25],
    ['Esgotado', 15]
  ]);

  var options = {
    pieHole: 0.5,
    colors: ['#233876', '#3b74e6', '#669df6'],
    slices: {
      0: { offset: 0.05 },
      1: { offset: 0.05 },
      2: { offset: 0.05 }
    },
    legend: {
      position: 'right',
      alignment: 'center',
      textStyle: { color: '#333', fontSize: 12 }
    },
    backgroundColor: 'transparent',
    pieSliceBorderColor: 'transparent',
    chartArea: { left: 10, top: 15, width: '90%', height: '85%' }
  };

  var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
  chart.draw(data, options);
}

// Função para desenhar o Gráfico de Clientes (Colunas)
function drawClientsChart() {
    var data = google.visualization.arrayToDataTable([
        ['Mês', 'Novos Clientes', { role: 'style' }],
        ['Jan', 210, '#3b74e6'],
        ['Fev', 600, '#3b74e6'],
        ['Mar', 1300, '#3b74e6']
      ]);
    
      var options = {
        legend: { position: 'none' },
        backgroundColor: 'transparent',
        bar: { groupWidth: '50%' },
        vAxis: {
          ticks: [0, 750, 1500],
          minValue: 0
        },
        chartArea: { left: 40, top: 20, width: '85%', height: '80%' }
      };
    
      var chart = new google.visualization.ColumnChart(document.getElementById('clientes_chart_div'));
      chart.draw(data, options);
}