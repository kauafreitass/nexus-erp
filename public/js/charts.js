// Carrega o pacote 'corechart' que é necessário para os gráficos
google.charts.load('current', { 'packages': ['corechart'] });

// Define uma única função de callback que irá chamar todas as outras funções de desenho
google.charts.setOnLoadCallback(drawAllCharts);

// Função principal que coordena o desenho de todos os gráficos
function drawAllCharts() {
  
  // 1. Tenta ler os dados dinâmicos do PHP
  let dynamicData = {};
  try {
    const dataElement = document.getElementById('charts-data');
    if (dataElement) {
      dynamicData = JSON.parse(dataElement.textContent);
    }
  } catch (e) {
    console.error("Erro ao ler dados JSON do dashboard:", e);
  }

  // 2. Chama cada função de gráfico, passando os dados dinâmicos
  drawSalesChart(dynamicData.sales);
  drawProductsChart(dynamicData.products);
  drawStockChart(dynamicData.stock); // ATUALIZADO
  drawClientsChart(dynamicData.customers); // ATUALIZADO
}

// ===== Gráfico de Vendas (Dinâmico) =====
function drawSalesChart(chartData) {
  const defaultData = [['Mês', 'Vendas'], ['Nenhum', 0]];
  var data = google.visualization.arrayToDataTable(chartData || defaultData);
  var options = {
    legend: { position: 'none' },
    colors: ['#3b74e6'],
    backgroundColor: 'transparent',
    vAxis: { minValue: 0 },
    chartArea: { left: 50, top: 20, width: '90%', height: '80%' }
  };
  var chart = new google.visualization.LineChart(document.getElementById('vendas_chart_div'));
  chart.draw(data, options);
}

// ===== Gráfico de Produtos (Dinâmico) =====
function drawProductsChart(chartData) {
  const defaultData = [['Status', 'Quantidade'], ['Nenhum produto', 1]];
  var data = google.visualization.arrayToDataTable(chartData || defaultData);
  var options = {
    pieHole: 0.5,
    colors: ['#233876', '#3b74e6', '#669df6'],
    slices: { 0: { offset: 0.05 }, 1: { offset: 0.05 }, 2: { offset: 0.05 } },
    legend: { position: 'right', alignment: 'center', textStyle: { color: '#333', fontSize: 12 } },
    backgroundColor: 'transparent',
    pieSliceBorderColor: 'transparent',
    chartArea: { left: 10, top: 15, width: '90%', height: '85%' }
  };
  var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
  chart.draw(data, options);
}

// ===== Gráfico de Estoque (AGORA DINÂMICO) =====
function drawStockChart(chartData) {
  // Define dados padrão (fallback)
  const defaultData = [
    ['Produto', 'Quantidade', { role: 'style' }],
    ['Nenhum', 0, '#3b74e6']
  ];
  
  var data = google.visualization.arrayToDataTable(chartData || defaultData);

  var options = {
    legend: { position: 'none' },
    backgroundColor: 'transparent',
    bar: { groupWidth: '50%' }, // Largura das barras
    vAxis: { minValue: 0 },
    chartArea: { left: 40, top: 20, width: '85%', height: '80%' }
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('estoque_chart_div'));
  chart.draw(data, options);
}

// ===== Gráfico de Clientes (AGORA DINÂMICO) =====
function drawClientsChart(chartData) {
    // Define dados padrão (fallback)
    const defaultData = [
        ['Mês', 'Novos Clientes'],
        ['Nenhum', 0]
      ];
      
    var data = google.visualization.arrayToDataTable(chartData || defaultData);
    
      var options = {
        legend: { position: 'none' },
        backgroundColor: 'transparent',
        bar: { groupWidth: '50%' },
        vAxis: { minValue: 0 },
        chartArea: { left: 40, top: 20, width: '85%', height: '80%' }
      };
    
      var chart = new google.visualization.ColumnChart(document.getElementById('clientes_chart_div'));
      chart.draw(data, options);
}