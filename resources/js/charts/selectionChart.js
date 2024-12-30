import Chart from 'chart.js/auto';

const BILION = 1000000000;

export function calculateChartData(selectedOrders, props) {
    const totalWeight = selectedOrders.value.reduce((sum, orderItem) => {
      if (orderItem?.order_items) {
        const orderItemsWeight = orderItem.order_items.reduce((itemsSum, item) => {
          return itemsSum + (item.weight || 0);
        }, 0);
        return sum + orderItemsWeight;
      }
      return sum;
    }, 0);
  
    const totalVolume = selectedOrders.value.reduce((sum, orderItem) => {
      if (orderItem?.order_items) {
        const orderItemsVolume = orderItem.order_items.reduce((itemsSum, item) => {
          const height = item.height || 0;
          const width = item.width || 0;
          const length = item.length || 0;
          return (itemsSum + height * width * length) / BILION;
        }, 0);
        return sum + orderItemsVolume;
      }
      return sum;
    }, 0);
  
    const remainingTrailerWeight = props.trailer.max_weight - totalWeight;
    const remainingTrailerVolume =
      props.trailer.height * props.trailer.width * props.trailer.length - totalVolume;
  
    return {
      remainingTrailerWeight,
      remainingTrailerVolume,
      totalWeight,
      totalVolume,
      labels: ['Pozostała waga', 'Waga zamówień'],
      colors: ['#90EE90', '#ff6384'],
    };
  }
  
  export function initializeWeightChart(chartCanvas, chartData) {
    return new Chart(chartCanvas, {
      type: 'pie',
      data: {
        labels: ['Pozostała waga', 'Waga zamówień'],
        datasets: [
          {
            data: [chartData.remainingTrailerWeight, chartData.totalWeight],
            backgroundColor: chartData.colors,
            borderColor: 'white',
            borderWidth: 5,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: { enabled: true },
        },
      },
    });
  }
  
  export function initializeVolumeChart(volumeChart, chartData) {
    return new Chart(volumeChart, {
      type: 'pie',
      data: {
        labels: ['Pozostała objętość', 'Objętość zamówień'],
        datasets: [
          {
            data: [chartData.remainingTrailerVolume, chartData.totalVolume],
            backgroundColor: chartData.colors,
            borderColor: 'white',
            borderWidth: 5,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: { enabled: true },
        },
      },
    });
  }
  
  export function updateCharts(weightChart, volumeChartInstance, chartData) {
    if (weightChart) {
      weightChart.data.datasets[0].data = [
        chartData.remainingTrailerWeight,
        chartData.totalWeight,
      ];
      weightChart.update();
    }
  
    if (volumeChartInstance) {
      volumeChartInstance.data.datasets[0].data = [
        chartData.remainingTrailerVolume,
        chartData.totalVolume,
      ];
      volumeChartInstance.update();
    }
  }
  