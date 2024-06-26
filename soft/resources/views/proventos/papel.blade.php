@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">
            <div class="row">
                <div class="col-sm-10">        
                    Proventos Por Papel
                </div>
                <div class="col-sm-2 d-flex justify-content-end">
                    <a href="/provento-papel" class="text text-secondary mt-1 ml-2"><i class="fas fa-file"></i> Papel</a>
                    <a href="/provento-mensal" class="text text-secondary mt-1 ml-2"><i class="fas fa-calendar"></i> Mensal</a>
                </div>
            </div>
        </h6>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form action="/provento-papel" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Pesquisa Resultados</legend>

                    <div class="row">
                        
                        <div class="col-sm-7 form-group">
                            <label>Papel</label>
                            <select name="papel[]" id="papel" class="form-control form-control-sm" multiple="multiple">
                                <option></option>
                                
                                @if( isset($data['papeis']) && count($data['papeis']) )
                                    @foreach( $data['papeis'] as $num => $val )
                                        <option value="{{$val->cdPapel}}">{{$val->nmPapel}}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    
                        <div class="col-sm-4 form-group">
                            <label>Sub Tipo</label>
                            <select name="subTipo[]" id="subTipo" class="form-control form-control-sm" multiple="multiple">
                                <option></option>
                                
                                <option value="2">{{ session()->get('autenticado')['sub_tipo'][2] }}</option>
                                <option value="3">{{ session()->get('autenticado')['sub_tipo'][3] }}</option>
                            </select>
                        </div>

                        <div class="col-sm-1 justify-content-start">
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm mt-4 pt-2 pb-2"><i class="fas fa-search pb-1"></i></button>
                                <span class="btn btn-info btn-sm mt-4 pt-2 pb-2 limpar_form"><i class="fas fa-eraser pb-1"></i></span>
                            </div>
                        </div>

                    </div>

                </fieldset>
            </form>
        </div>

    </div>

</div>



<div class="row">
    
    <div class="col-sm-12">
        <div class="borda" style="width:100%;height:355px;">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr class="table-active">
                        <th>Papel</th>
                        <th>Total Cotas</th>
                        <th>Total Aportado</th>
                        <th>Posição Atual</th>
                        <th>% da Carteira</th>
                        <th>Variação</th>
                        <th>Proventos</th>
                        <th>D. Yield</th>
                        <th>Valorização Real</th>
                        <th>Valorização Percentual</th>
                    </tr>
                </thead>
                <tbody>
                    @if( isset($data['proventos']) && count($data['proventos']) )
                        @foreach( $data['proventos'] as $num => $val )
                            <tr>
                                <td>{{$val['papel']}}</td>
                                <td>{{$val['qtdeCotas']}}</td>
                                <td>R$ {{number_format($val['totalAportado'],2,',','.')}}</td>
                                <td>R$ {{number_format($val['posicaoAtual'],2,',','.')}}</td>
                                <td>{{number_format($val['percentualPosicao'],2,',','.')}} %</td>
                                <td>R$ {{number_format($val['variacaoAntesProventos'],2,',','.')}}</td>
                                <td>R$ {{number_format($val['proventosPagos'],2,',','.')}}</td>
                                <td>{{number_format($val['dYield'],2,',','.')}}</td>
                                <td>R$ {{number_format($val['valorizacaoReal'],2,',','.')}}</td>
                                
                                @if( $val['valorizacaoReal'] > 0 )
                                    <th><span class="text text-success">{{number_format($val['valorizacaoPercentual'],2,',','.')}} %</span></th>
                                @else
                                    <th><span class="text text-danger">{{number_format($val['valorizacaoPercentual'],2,',','.')}} %</span></th>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th>Total</th>
                        @if( isset($data['totais']) && count($data['totais']) )
                            <th>{{$data['totais']['qtdeCotas']}}</th>
                            <th>R$ {{number_format($data['totais']['totalAportado'],2,',','.')}}</th>
                            <th>R$ {{number_format($data['totais']['posicaoAtual'],2,',','.')}}</th>
                            <th>100,00 %</th>
                            <th>R$ {{number_format($data['totais']['variacaoAntesProventos'],2,',','.')}}</th>
                            <th>R$ {{number_format($data['totais']['proventosPagos'],2,',','.')}}</th>
                            <th>{{number_format($data['totais']['dYield'],2,',','.')}}</th>
                            <th>R$ {{number_format($data['totais']['valorizacaoReal'],2,',','.')}}</th>

                            @if( $data['totais']['valorizacaoPercentual'] > 0 )
                                <th><span class="text text-success">{{number_format($data['totais']['valorizacaoPercentual'],2,',','.')}} %</span></th>
                            @else
                                <th><span class="text text-danger">{{number_format($data['totais']['valorizacaoPercentual'],2,',','.')}} %</span></th>
                            @endif

                        @endif
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
    
</div>




<div class="row">
    
    <div class="col-sm-12">
        
        @if( isset($data['proventos']) && count($data['proventos']) > 0 )
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

            <script type="text/javascript">
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Papel', 'Posição'],
                        
                        @foreach($data['proventos'] as $num => $val)
                            [ "{{$val['papel']}}",{{$val['percentualPosicao']}} ],
                        @endforeach
                        
                    ]);

                    var view = new google.visualization.DataView(data);

                    var options = {
                        title: 'Distribuição Papeis na Carteira',
                        legend:{
                            position: 'none'
                        }
                    };

                    var chart = new google.visualization.ColumnChart(document.getElementById("divPapeis"));
                    chart.draw(view, options);
                }
            </script>
        @endif

        <div class="borda">
            <div id="divPapeis" style="width:100%;height:350px;"></div>
        </div>
    </div>

</div>




<div class="row">
    
    <div class="col-sm-12">
        
        @if( isset($data['proventos']) && count($data['proventos']) > 0 )
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

            <script type="text/javascript">
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Papel', 'Aportado',{ role: "style" },'Posição Atual',{ role: "style" },'Proventos',{ role: "style" }],
                        
                        @foreach($data['proventos'] as $num => $val)
                            ["{{$val['papel']}}",{{$val['totalAportado']}},'#FE642E',{{$val['posicaoAtual']}},'#2E9AFE',{{$val['proventosPagos']}},'#2EFE64'],
                        @endforeach
                        
                    ]);

                    var view = new google.visualization.DataView(data);

                    var options = {
                        title: 'Distribuição de Proventos por Papel',
                        legend:{
                            position: 'none'
                        }
                    };

                    var chart = new google.visualization.ColumnChart(document.getElementById("divProventos"));
                    chart.draw(view, options);
                }
            </script>
        @endif

        <div class="borda">
            <div id="divProventos" style="width:100%;height:350px;"></div>
        </div>
    </div>

</div>





<script src="{{ asset('/js/papelProventos.js') }}"></script>

@stop