@if( isset($data['dadosInforme']['agrupamento']) && count($data['dadosInforme']['agrupamento']) > 0 )

    <script type="text/javascript">
        
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Data', 'Valor'],
                
                @for( $i=count($data['dadosInforme']['agrupamento']) - 1; $i>=0; $i-- )
                    [
                        "{{date('d-m-Y',strtotime($data['dadosInforme']['agrupamento'][$i]->dtInforme))}}",
                        {{ number_format($data['dadosInforme']['agrupamento'][$i]->valor,2,'','.')}}
                    ],
                @endfor

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