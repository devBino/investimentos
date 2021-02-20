@if( isset($data['agrupamento']) && count($data['agrupamento']) > 0 )
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Data', 'Valor'],
                
                @foreach($data['agrupamento'] as $num => $val)
                    ["{{date('d-m-Y',strtotime($val->dtInforme))}}",{{ number_format($val->valor,2,'','.')}}],
                @endforeach
                
            ]);

            var options = {
                title: 'Evolução Patrimonial',
                curveType: 'function',
                legend: { position: 'none' },
                animation: false
            };

            var chart = new google.visualization.LineChart(document.getElementById('divInforme'));
            chart.draw(data, options);

        }
    </script>
@endif