window.onload = function () {
  var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    title: {
      text: "Twoje wydatki z wybranego okresu",
    },
    data: [
      {
        type: "pie",
        startAngle: 240,
        yValueFormatString: '##0.00"%"',
        indexLabel: "{label} {y}",
        dataPoints: [
          { y: 21, label: "Jedzenie (300,00 PLN)" },
          { y: 15, label: "Rozrywka (200,00 PLN)" },
          { y: 29, label: "Wycieczka (400,00 PLN)" },
          { y: 35, label: "Ubranie (500,00 PLN)" },
        ],
      },
    ],
  });
  chart.render();
};
