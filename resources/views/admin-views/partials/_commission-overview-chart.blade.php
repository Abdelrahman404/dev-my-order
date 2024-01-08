@php($params = session('dash_params'))
<div id="grow-sale-chart"></div>

<script>
    var options = {
          series: [{
          name: 'Gross Sale',
          data: [{{ implode(",",$data['total_sell']) }}]
        },{
          name: 'Admin Comission',
          data: [{{ implode(",",$data['commission']) }}]
        },{
          name: 'Delivery Comission',
          data: [{{ implode(",",$data['delivery_commission']) }}]
        }],
          chart: {
          height: 350,
          type: 'area',
          toolbar: {
            show:false
        },
            colors: ['#76ffcd','#ff6d6d', '#214752'],
        },
            colors: ['#76ffcd','#ff6d6d', '#214752'],
        dataLabels: {
          enabled: false,
            colors: ['#76ffcd','#ff6d6d', '#214752'],
        },
        stroke: {
          curve: 'smooth',
          width: 2,
            colors: ['#76ffcd','#ff6d6d', '#214752'],
        },
        fill: {
            type: 'gradient',
            colors: ['#76ffcd','#ff6d6d', '#214752'],
        },
        xaxis: {
        //   type: 'datetime',
            categories: [{!! implode(",",$data['label']) !!}]
        },
        tooltip: {
          x: {
            format: 'dd/MM/yy HH:mm'
          },
        },
        };

        var chart = new ApexCharts(document.querySelector("#grow-sale-chart"), options);
        chart.render();
    </script>

    <script>
        // INITIALIZATION OF CHARTJS
        // =======================================================
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function () {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));
    </script>
