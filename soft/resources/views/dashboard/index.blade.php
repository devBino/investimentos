@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-11">
        <h6 class="text text-secondary">Dashoboard</h6>
    </div>
    <div class="col-sm-1 justify-content-end">
        <div class="btn-group">
            
            @php
                
                $stPermissaoValores = 0;
                
                if( !is_null( $data['permissaoValores'] ) ){
                    $stPermissaoValores = $data['permissaoValores'];
                }

            @endphp

            <span id="permissaoValores" class="btn btn-default btn-sm mt-1 ml-3 text text-secondary" data-status="{{$stPermissaoValores}}"><i class="fas fa-eye"></i></span>
            <span id="atualizaCotacao" class="btn btn-default btn-sm  mt-1 text text-secondary"><i id="icon-bt" class="fas fa-sync"></i></span>
        </div>
    </div>
</div>

<div class="row">

    <input type="hidden" id="tkn" value="{!! csrf_token() !!}">

    <div class="col-sm-12">
        <div class="borda">

            @php

                $escondeTollTips = "";

                if($stPermissaoValores == 0){
                    $escondeTollTips = "enableInteractivity: false";
                }

            @endphp

            <div class="row bt-0 pt-0">
                <div class="col-sm-6 borda-div-grafico">
                    
                    <h5 class="text text-info" style="border-bottom:1px solid #CEE3F6;border-top:1px solid #CEE3F6;">Distribuição de Patrimônio por Tipo de Ativo</h5>

                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

                    <script type="text/javascript">
                        google.charts.load("current", {packages:["corechart"]});
                        google.charts.setOnLoadCallback(drawChart);

                        function drawChart() {

                            var data = google.visualization.arrayToDataTable([
                            
                                ['Tipo', 'Valor Por Tipo'],
                            
                                @foreach( $data['contagemTipos'] as $num => $val )
                                    
                                    [ "{{ $val['nomeTipo'] }}",{{ $val['totalTipo'] }} ],

                                @endforeach

                            ]);

                            var options = {
                                title: '',
                                is3D: true,
                                {{$escondeTollTips}}
                            };                            

                            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d1'));
                            chart.draw(data, options);

                        }
                    </script>

                    <div id="piechart_3d1" style="width:100%;height:400px;"></div>

                </div>

                <div class="col-sm-6 borda-div-grafico">
                    
                    <h5 class="text text-info" style="border-bottom:1px solid #CEE3F6;border-top:1px solid #CEE3F6;">Distribuição de Patrimônio por SubTipo de Ativo</h5>

                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

                    <script type="text/javascript">
                        google.charts.load("current", {packages:["corechart"]});
                        google.charts.setOnLoadCallback(drawChart);

                        function drawChart() {

                            var data = google.visualization.arrayToDataTable([
                            
                                ['Tipo', 'Valor Por Sub Tipo'],
                            
                                @foreach( $data['contagemSubTipos'] as $num => $val )
                                    
                                    [ "{{ $val['nomeTipo'] }}",{{ $val['totalTipo'] }} ],

                                @endforeach

                            ]);

                            var options = {
                                title: '',
                                is3D: true,
                                {{$escondeTollTips}}
                            };                            

                            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d2'));
                            chart.draw(data, options);

                        }
                    </script>

                    <div id="piechart_3d2" style="width:100%;height:400px;"></div>

                </div>

            </div>

            @if( $stPermissaoValores == 1 )
                <div class="row bt-0 pt-0">
                    
                    <div class="col-sm-12">

                            <h5 class="text text-info" style="border-bottom:1px solid #CEE3F6;border-top:1px solid #CEE3F6;">Distribuição de Patrimônio por Papel</h5>

                            <table class="table table-sm table-bordered table-striped">
                                <thead>
                                    <tr class="table-info">
                                        <th>Papel</th>
                                        <th>Aplicado</th>
                                        <th>Posição</th>
                                        <th>Lucro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach( $data['contagemPapeis'] as $num => $val )
                                        @if( $val['exibir'] == 1 )
                                            <tr>
                                                <td>{{ $val['nomePapel'] }}</td>
                                                <td>R$ {{number_format($val['aplicado'],2,',','.') }}</td>
                                                <td>R$ {{number_format($val['retorno'],2,',','.') }}</td>
                                                
                                                @if( $val['lucro'] < 0 )
                                                    <td class="text text-danger">R$ {{number_format($val['lucro'],2,',','.') }}</td>
                                                @elseif( $val['lucro'] == 0 )
                                                    <td class="text text-info">R$ {{number_format($val['lucro'],2,',','.') }}</td>
                                                @else
                                                    <td class="text text-success">R$ {{number_format($val['lucro'],2,',','.') }}</td>
                                                @endif
                                                
                                            </tr>
                                        @endif
                                    @endforeach

                                    <tr class="table-info">
                                        <td><b>TOTAL</b></td>
                                        <td colspan='3' style="text-align:right;"><b>R$ {{number_format($data['totalPatrimonio'],2,',','.') }}</b></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h5 class="text text-default mt-0 p-1" style="background-color:#f1f1f1;border:1px solid #dddddd;text-align:right;">Variação Patrimonial: <b>{{number_format($data['variacaoPatrimonial'],2,',','.')}}%</b></h5>

                    </div>
                </div>

            @endif

        </div>
    </div>

</div>

<script src="{{ asset('/js/dashboard.js') }}"></script>

@stop