function eBarGraph(elementId, title, vs_data, hs_data, vs_label, color = '#198754') {

    let myChart = echarts.init(document.getElementById(elementId));

    let option = {
        title: {
            text: title,
        },
        tooltip: {},
        legend: {
            data: [vs_label],
        },
        xAxis: {
            data: hs_data,
        },
        yAxis: {},
        series: [
            {
                name:  vs_label,
                type: "bar",
                data: vs_data,
                color: color,
            },
        ],
    };
    myChart.setOption(option);
}

function eLineGraph(elementId, title, vs_data, hs_data, vs_label, color = '#198754') {

  let myChart = echarts.init(document.getElementById(elementId));

   let option = {
    title: {
      text: title,
    },
    tooltip: {},
    xAxis: {
      type: 'category',
      data: hs_data
    },
    yAxis: {
      type: 'value'
    },
    series: [
      {
        name:  vs_label,
        data: vs_data,
        type: 'line',
        color: color
      }
    ]
  };
  myChart.setOption(option);
}


function ePieChart(elementId, title, data){

    let myChart = echarts.init(document.getElementById(elementId, title));

   let option = {
        title: {
          text: title,
          left: 'left'
        },
        tooltip: {
          trigger: 'item'
        },
        // legend: {
        //   orient: 'bottom',
        //   left: 'left'
        // },
        series: [
          {
            name: title,
            type: 'pie',
            radius: '70%',
            data: data,
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
              }
            }
          }
        ]
      };

      myChart.setOption(option);
}

function get2BarsAndLineGraphOptions(elementId, title, metrics, xAxisLabels, metricsData){

  // let type = records && (records <= maxLimit) ? 'bar' : 'line';
  console.log(`metrics 0`, metricsData[0]);
  console.log(`metrics 1`, metricsData[1]);
  console.log(`metrics 2`, metricsData[2]);

  
  const colors = ['#5470C6', '#91CC75', '#EE6666'];
  let myChart = echarts.init(document.getElementById(elementId, title));
  
  
  let options = {
    color: colors,
    title: titleConfig(title),
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'cross'
      }
    },
    grid: {
      right: '20%'
    },
    toolbox: {
      show: true,
      orient: 'vertical',
      left: 'right',
      top: 'center',
      feature: {
        magicType: {
          type: ['line', 'bar']
        },
        dataView: { show: true, readOnly: false },
        restore: { show: true },
        saveAsImage: { show: true }
      }
    },
    legend: {
      bottom: 10,
      right: 'right',
      data: metrics
    },
    xAxis: [
      {
        type: 'category',
        axisTick: {
          alignWithLabel: true
        },
        data: xAxisLabels
      }
    ],
    yAxis: [
      {
        type: 'value',
        name: metrics[0],
        position: 'right',
        axisLine: {
          show: true,
          lineStyle: {
            color: colors[0]
          }
        },
        axisLabel: {
          formatter: '{value}'
        }
      },
      {
        type: 'value',
        name: metrics[1],
        position: 'right',
        offset: 80,
        axisLine: {
          show: true,
          lineStyle: {
            color: colors[1]
          }
        },
        axisLabel: {
          formatter: '{value}'
        }
      },
      {
        type: 'value',
        name: metrics[2],
        position: 'left',
        // alignTicks: true,
        axisLine: {
          show: true,
          lineStyle: {
            color: colors[2]
          }
        },
        axisLabel: {
          formatter: '{value}'
        }
      }
    ],
    series: [
      {
        name: metrics[0],
        type: 'bar',
        data: metricsData[0],
      },
      {
        name: metrics[1],
        type: 'line',
        yAxisIndex: 1,
        data:  metricsData[1],
      },
      {
        name: metrics[2],
        type: 'line',
        yAxisIndex: 2,
        data: metricsData[2]
      }
    ]
  };
  
  myChart.setOption(options);

}

function titleConfig(title){
  const config = {
    text: title,
    textStyle: {
      fontWeight: 'bold',
      // fontSize: 14.5,
    }
  }
  return config;
}