//         BAR CHART
var colors = [
  "#F44336",
  "#E91E63",
  "#9C27B0",
  "#673AB7",
  "#3F51B5",
  "#2196F3",
  "#03A9F4",
  "#00BCD4",
];

var barChartOptions = {
  series: [
    {
      data: cantidadBarChart,
    },
  ],
  chart: {
    height: 250,
    type: "bar",
    events: {
      click: function (chart, w, e) {
        // console.log(chart, w, e)
      },
    },
  },
  colors: colors,
  plotOptions: {
    bar: {
      columnWidth: "45%",
      distributed: true,
    },
  },
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: false,
  },
  xaxis: {
    categories: titulosBarChart,
    labels: {
      style: {
        colors: colors,
        fontSize: "12px",
      },
    },
  },
};

var barChart = new ApexCharts(
  document.querySelector("#bar-chart"),
  barChartOptions
);
barChart.render();

//      PIE CHART
console.log(cantidadPrestadosPorDia);
var pieChartOptions = {
  series: cantidadPrestadosPorDia,
  chart: {
    width: 380,
    type: "pie",
  },
  labels: [
    "Lunes",
    "Martes",
    "Miércoles",
    "Jueves",
    "Viernes",
    "Sábado",
    "Domingo",
  ],
  responsive: [
    {
      breakpoint: 480,
      options: {
        chart: {
          width: 200,
        },
        legend: {
          position: "bottom",
        },
      },
    },
  ],
};

var pieChart = new ApexCharts(
  document.querySelector("#pie-chart"),
  pieChartOptions
);
pieChart.render();
