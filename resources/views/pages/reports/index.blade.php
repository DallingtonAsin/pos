
@extends('layouts.master')

@section('content')

<div class="panel panel-success">
  <div class="panel-heading">
   <div class="panel-title nunito-font">
    <div class="row">
      <div class="col-lg-6 text-left graph-view nunito-font">
        <h5><i class="fa fa-chart-line text-success"></i>
          <strong class="reportTitle ml-1"> Reports generated from the sales data made over time</strong>
        </h5>
      </div>

      <div class="pull-right">
        <div class="graph-view">
          <h5><i class="text-success nunito-font">Select a graph to view</i></h5>
        </div>
        <div class="graph-view">
          <select class="form-control nunito-font" id="chart">
           <option value="barchart">Bar graph</option>
           <option  value="linechart">Line graph</option>
           <option  value="piechart">Pie graph</option>
         </select>
       </div>

     </div>
   </div>
 </div>
</div>


<div class="panel-body">
  <div class="panel-canvas">
   <canvas id="canvas" height="250" width="600" class="nunito-font"></canvas>
 </div>
</div>
</div>


<script type="text/javascript">

  $(document).ready(function(){

    var chart_data = {!! json_encode($chartdata) !!};
    if (chart_data != undefined || chart_data.length > 0) {
      chartdata = chart_data;
    }

    var barchartTitle = "Barchart showing the growth of sales generated over months";
    var piechartTitle = "Piechart showing the growth of sales generated over months";
    var linechartTitle = "Areachart showing the growth of sales generated over months";
    $('.reportTitle').html(barchartTitle);

         getSalesData('bar');
   

    detectGraphChange();

    function detectGraphChange() {
      $("#chart").change(function(){
        var option = $('#chart').val();

        switch(true)
        {
          case option == "linechart":
          $('.reportTitle').html(linechartTitle);
          getSalesData('line');
          break;
          case option == "barchart":
          $('.reportTitle').html(barchartTitle);
          getSalesData('bar');
          break;
          case option == "piechart":
          $('.reportTitle').html(piechartTitle);
          getSalesData('pie');
          break;
          default:
          $('.reportTitle').html(barchartTitle);
          getSalesData('bar');

        }

      });
    }

   
    function getSalesData(chart){

      if(chart == 'bar'){
        drawBarChart(chartdata);
      }
      else if(chart == 'pie'){
        drawPieChart(chartdata);
      }
      else if(chart == 'line'){
        drawLineChart(chartdata);
      }


    }


    function drawLineChart(response){


      var ctx = document.getElementById("canvas").getContext('2d');
      if(window.bar != undefined)
       window.bar.destroy();
     window.bar = new Chart(ctx, {
      type: 'line',
      data: {
        labels: response.months,
        datasets:[{
          label:"Sales made",
          lineTension:0.3,
          backgroundColor:'rgba(2,117,216,0.2)',
          borderColor:'rgba(2,117,216,1)',
          pointRadius:5,
          pointHoverRadius:5,
          pointHoverBackgroundColor:'rgba(2,117,216,1)',
          pointHitRadius:5,
          pointBorderWidth:2,
          data:response.sales,

        }],
      },


      options: {
       tooltips: {
        callbacks: {
          label: function(tooltipItem, data) {
            return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
        }
      },
      title: {
        display: true,
        text: 'Areachart showing the growth of sales generated over months',
        fontColor:'#006400',
        fontFamily:'Nunito',
      },

      scales: {
        xAxes: [{
          time: {
            unit: 'date'
          },
          scaleLabel: {
            display: true,
            labelString: 'Months of the year'
          },
          gridLines:{
            display:true
          },
          ticks: {
            maxTicksLimit:24,
          },
          gridLines:{
            color: "rgba(0,0,0,0.125)",
          }
        }],
        yAxes: [{
            //    ticks: {
            //   min:0,
            //   max:response.max,
            //   maxTicksLimit:5,
            //   beginAtZero:true
            // },
            scaleLabel: {
              display: true,
              labelString: "Volume of sales"
            },
            gridLines:{
              color: "rgba(0,0,0,0.125)",
            }
          }]
        }
      },
      legend:{
        display:true
      }
    });
   }

   function drawBarChart(response){
    var ctx = document.getElementById("canvas").getContext('2d');
    var date = response.months+" "+response.years;
    if(window.bar != undefined)
     window.bar.destroy();
   window.bar= new Chart(ctx, {
    type: 'bar',
    data: {
      labels: response.months,
      datasets:[{
        label:'sales',
        data: response.sales,
        backgroundColor: ['#b87333','orange','grey','#B1FB17',
        '#000','#82caff',
        '#7D0552',"#3e95cd", "#8e5ea2","#3cba9f","#800000","#c45850",'#808000',
        '#008000', '#000080', '#ff00ff', '#008080','#ff0000',
        ],
        borderColor: '#82caff',
          // hoverBackgroundColor: '#CCCCCC',
          hoverBorderColor: '#666666'
        }],
      },
      options: {
       tooltips: {
        callbacks: {
          label: function(tooltipItem, data) {
            return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
        }
      },
      scales: {
        xAxes: [{
          scaleLabel: {
            display: true,
            labelString: 'Months of the year'
          }
        }],
        yAxes: [{
         scaleLabel: {
           display: true,
           labelString: 'Volume of sales generated'
         }
       }]
     } ,
     title: {
      display: true,
      text: 'Barchart showing the growth of sales generated over months',
      fontColor:'#006400',
      fontFamily:'Nunito',
    },
  },
  legend:{
    display:true
  }
});
 }

 function drawPieChart(response){

  var ctx = document.getElementById("canvas").getContext('2d');
   //console.log(response);


  //  for(let i=0; i<k.length; i++){
  //   sum += k[i];
  //   percent = k[i]/sum;
  // }
  // console.log(k);
  // console.log(sum);
  // console.log(percent);

  if(window.bar != undefined)
    window.bar.destroy();
  window.bar = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: response.months,
      datasets:[{
        label:'sales',
        data: response.sales,
        backgroundColor: ['#b87333','orange','grey','#B1FB17',
        'white','#82caff',
        '#7D0552','#006400','#4B0082',
        'pink','black','red'
        ],
        borderColor: '#b87333',
            // hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
          }],
        },
        options: {
         tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
          }
        },
        plugins: {
         datalabels: {
           formatter: (value, ctx) => {
             var k = response.sales;
             var sum = 0;
             for(let i=0; i<k.length; i++){
              sum += k[i];
              percent = Math.round(k[i]/sum);
            }
            return ""+percent+"%";
          },
          color: '#000',
        }
      },

      title: {
        display: true,
        position:'top',
        text: 'Piechart showing the growth of sales generated over months',
        fontColor:'#006400',
        fontFamily:'Nunito',

      },

      legend: {
        display: true,
        position: 'right',
      },

      tooltips: {
        enabled: true
      },

    },



  });
}

});

</script>

@endsection
