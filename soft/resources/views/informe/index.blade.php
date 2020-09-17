@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Informe Patrimônio - Lançamentos Resumidos Manuais</h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-7">
        <div class="borda">

            <form action="/informe-salvar" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Novo Lançamento</legend>

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Descrição</label>
                            <input type="text" id="descricao" name="descricao" class="form-control form-control-sm" autofocus="true" required autocomplete="off">
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Valor</label>
                            <input type="number" id="valor" name="valor" step="0.01" class="form-control form-control-sm calculo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Data</label>
                            <input type="date" name="dataInforme" id="dataInforme" class="form-control form-control-sm">
                        </div>

                        <div class="col-sm-6 justify-content-start">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm mt-4 pt-2"><i class="fas fa-check pb-2"></i></button>
                                <span class="btn btn-info btn-sm mt-4 pt-2 limpar_form"><i class="fas fa-eraser pb-2"></i></span>
                            </div>
                        </div>
                    </div>

                </fieldset>
            </form>

        </div>
    </div>
    <div class="col-sm-5">
        
        <div class="borda p-2" style="height:205px;">
            <table class="table table-sm">
                
                @if( isset($data['ultimoLancamento']) && count($data['ultimoLancamento']) )
                    
                    @php $valorTotal = 0; @endphp

                    <tbody>
                        <tr class="table-active">
                            <td colspan=2> <center> <b>Ultimo Lançamento</b> </center></td>
                        </tr>
                        @foreach( $data['ultimoLancamento'] as $num => $val )
                            <tr>
                                <td>{{$val->descricao}}</td>
                                <td><center>R$ {{number_format($val->valor,2,',','.')}}</center></td>
                            </tr>
                            
                            @php $valorTotal += $val->valor; @endphp

                        @endforeach
                        
                        <tr class="table-active">
                            <th>Total</th>
                            <td>
                                <center>R$ {{number_format($valorTotal,2,',','.')}}</center>
                            </td>
                        </tr>
                        
                    </tbody>
                    
                @endif
                
            </table>
        </div>
        
    </div>
</div>

<div class="row">

    <div class="col-sm-12">
        
        @if( isset($data['agrupamento']) && count($data['agrupamento']) > 0 )
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

            <script type="text/javascript">
                google.charts.load('current', {'packages':['bar']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Data', 'Valor'],
                        
                        @foreach($data['agrupamento'] as $num => $val)
                            ["{{date('d-m-Y',strtotime($val->dtInforme))}}",{{$val->valor}}],
                        @endforeach
                        
                    ]);

                    var options = {
                        chart: {
                            title: 'Evolução Patrimonio'
                        },
                        legend:{
                            position: 'none'
                        }
                    };

                    var chart = new google.charts.Bar(document.getElementById('divInforme'));
                    
                    chart.draw(data, google.charts.Bar.convertOptions(options));
                }
            </script>
        @endif

        <div class="borda">
            <div id="divInforme" style="width:100%;height:400px;"></div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda p-2" style="height:400px;overflow-y:scroll;">
            
            <div class="table-responsive">
                @include('tabelas.informe')
            </div>

        </div>

    </div>
</div>


<script src="{{ asset('/js/informe.js') }}"></script>

@stop