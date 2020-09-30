@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">
            <div class="row">
                <div class="col-sm-10">        
                    Proventos Mensais
                </div>
                <div class="col-sm-2 d-flex justify-content-end"></div>
            </div>
        </h6>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form action="/provento-mensal" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Pesquisa Resultados</legend>

                    <div class="row">
                        
                        <div class="col-sm-2 form-group">
                            <label>Ano</label>
                            <select name="ano" id="ano" class="form-control form-control-sm">
                                <option></option>
                                <option value="2019">2019</option>
                                <option value="2020">2020</option>
                                <option value="2021">2021</option>
                                <option value="2022">2022</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                            </select>
                        </div>                        

                        <div class="col-sm-3 form-group">
                            <label>Papel</label>
                            <select name="papel" id="papel" class="form-control form-control-sm">
                                <option></option>
                                
                                @if( isset($data['papeis']) && count($data['papeis']) )
                                    @foreach( $data['papeis'] as $num => $val )
                                        <option value="{{$val->cdPapel}}">{{$val->nmPapel}}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    
                        <div class="col-sm-2 form-group">
                            <label>Sub Tipo</label>
                            <select name="subTipo" id="subTipo" class="form-control form-control-sm">
                                <option></option>
                                
                                <option value="2">{{ session()->get('autenticado')['sub_tipo'][2] }}</option>
                                <option value="3">{{ session()->get('autenticado')['sub_tipo'][3] }}</option>
                            </select>
                        </div>

                        <div class="col-sm-1 justify-content-start">
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm mt-4 pt-2"><i class="fas fa-search pb-1"></i></button>
                                <span class="btn btn-info btn-sm mt-4 pt-2 limpar_form"><i class="fas fa-eraser pb-1"></i></span>
                            </div>
                        </div>

                        <div class="col-sm-4 form-group">
                            
                        </div>                        
                    </div>

                </fieldset>
            </form>
        </div>

    </div>

</div>



<div class="row">
    <div class="col-sm-3">
        <div class="borda" style="width:100%;height:355px;">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr class="table-active">
                        <th>Mês</th>
                        <th>Proventos</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalProventos = 0;
                    @endphp

                    @if( isset($data['proventos']) && count($data['proventos']) > 0 )
                        @foreach($data['proventos'] as $num => $val)
                            <tr>
                                <td>{{$val['nomeMes']}}</td>
                                <td><center>R$ {{ number_format($val['valor'],2,',','.') }}</center></td>
                            </tr>

                            @php
                                $totalProventos += $val['valor'];
                            @endphp

                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th>Total</th>
                        <th>R$ {{number_format($totalProventos,2,',','.')}}</th>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
    <div class="col-sm-9">
        
        @if( isset($data['proventos']) && count($data['proventos']) > 0 )
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

            <script type="text/javascript">
                google.charts.load('current', {'packages':['bar']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Mes', 'Proventos'],
                        
                        @foreach($data['proventos'] as $num => $val)
                            ["{{$val['nomeMes']}}",{{$val['valor']}}],
                        @endforeach
                        
                    ]);

                    var options = {
                        chart: {
                            title: 'Proventos Mensais'
                        },
                        legend:{
                            position: 'none'
                        }
                    };

                    var chart = new google.charts.Bar(document.getElementById('divProventos'));
                    
                    chart.draw(data, google.charts.Bar.convertOptions(options));
                }
            </script>
        @endif

        <div class="borda">
            <div id="divProventos" style="width:100%;height:350px;"></div>
        </div>
    </div>
</div>

<script src="{{ asset('/js/mensal.js') }}"></script>

@stop