@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Preços Alvo Para Aportes/Resgates - Monitoramento de Alvos</h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <div class="row p-0">

                <div class="col-sm-12">
                    
                    <form action="/alvo-ordenar" method="post">
                        <input id="tkn" type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <div class="row">
                            
                            <div class="col-sm-2 form-group">
                                <input type="number" id="valorSimulacao" name="valorSimulacao" placeholder="Valor Simulação" class="form-control form-control-sm"
                                    
                                    @if( isset($data['params']) && isset($data['params']['valorSimulacao']))
                                        value="{{$data['params']['valorSimulacao']}}"
                                    @endif
                                    
                                >
                            </div>

                            @php
                                $filtros = [
                                    "0"=>"Ordernar",
                                    "1"=>"Preço Médio",
                                    "2"=>"Cotação",
                                    "3"=>"Último Valor Pago - Cotação",
                                    "4"=>"Preço Médio - Cotação",
                                    "5"=>"DY",
                                    "6"=>"Valor Retornado Anual",
                                    "7"=>"Quantidade de Cotas",
                                    "8"=>"Dias Último Aporte"
                                ];
                            @endphp

                            <div class="col-sm-4 form-group">
                                <select name="ordenacao" id="ordenacao" class="form-control form-control-sm" required>
                                    @foreach($filtros as $num => $val)
                                        @if( isset($data['params']) && isset($data['params']['ordenacao']) && $num == $data['params']['ordenacao'])
                                            <option value="{{$num}}" selected>{{$val}}</option>
                                        @else
                                            <option value="{{$num}}">{{$val}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            @php
                                $filtros = [
                                    "0"=>"Tipo",
                                    "1"=>"Asc",
                                    "2"=>"Desc"
                                ];
                            @endphp

                            <div class="col-sm-2 form-group">
                                <select name="tipo" id="tipo" class="form-control form-control-sm" required>
                                    
                                    @foreach($filtros as $num => $val)
                                        @if( isset($data['params']) && isset($data['params']['tipo']) && $num == $data['params']['tipo'])
                                            <option value="{{$num}}" selected>{{$val}}</option>
                                        @else
                                            <option value="{{$num}}">{{$val}}</option>
                                        @endif
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-sm-2 form-group">
                                <input type="number" id="historicoMeses" name="historicoMeses" placeholder="Meses de Histórico" class="form-control form-control-sm"
                                    @if( isset($data['params']) && isset($data['params']['historicoMeses']))
                                        value="{{$data['params']['historicoMeses']}}"
                                    @endif
                                >
                            </div>

                            <div class="col-sm-2 form-group d-flex justify-content-start">
                                <button class="btn btn-info btn-sm"><i class="fas fa-filter"></i></button>   
                                <span class="btn btn-info btn-sm ml-1 limpar_form"><i class="fas fa-eraser"></i></span>
                                <span id="atualizaCotacao" class="btn btn-info btn-sm ml-1"><i id="icon-bt" class="fas fa-sync"></i></span>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>

            <table class="table table-sm table-bordered table-striped" style="margin-top:-25px;">
                <thead>
                    <tr class="table-active">
                        <th>Papel</th>
                        <th>Preço Médio</th>
                        <th>Ultimo Valor Pago</th>
                        <th>Cotação</th>
                        <th>Ultimo Valor Pago - Cotação</th>
                        <th>Preço Médio - Cotação</th>
                        <th>Valor Simulado</th>
                        <th>Cotas</th>
                        <th>Dy Ano</th>
                        <th>Proventos Mensais</th>
                        <th>Proventos Anuais</th>
                        <th>Dias Último Aporte</th>
                        <th><center>-</center></th>
                    </tr>
                </thead>
                <tbody>
                    @if( isset( $data['listagem'] ) && count($data['listagem']) )
                        @foreach( $data['listagem'] as $num => $val )
                            
                            <tr class="tr_alvo">
                            
                                @if( date('Y-m-d',strtotime($val['atualizacaoDiaria'])) == date('Y-m-d',time()) )
                                    <td><span class="text text-success">{{$val['nmPapel']}}</span></td>
                                @else
                                    <td><span class="text text-danger">{{$val['nmPapel']}}</span></td>
                                @endif

                                <td>R$ {{number_format($val['precoMedio'],2,',','.')}}</td>
                                <td>R$ {{number_format($val['ultimoPrecoPago'],2,',','.')}}</td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm text-cotacoes" data-papel="{{$val['cdPapel']}}" value="{{$val['cotacao']}}">
                                </td>
                                <td>
                                    @if( (float) $val['diferenca'] >= (float) env('DIFERENCA_ALVO_OK') )
                                        <span class="text text-success">R$ {{number_format($val['diferenca'],2,',','.')}}</span>
                                    @else
                                        <span class="text text-danger">R$ {{number_format($val['diferenca'],2,',','.')}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if( (float) $val['comparaPrecoMedioCotacao'] >= (float) env('DIFERENCA_PRECO_MEDIO_OK') )
                                        <span class="text text-success">R$ {{number_format($val['comparaPrecoMedioCotacao'],2,',','.')}}</span>
                                    @else
                                        <span class="text text-danger">R$ {{number_format($val['comparaPrecoMedioCotacao'],2,',','.')}}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($val['valorSimulacao'],2,',','.') }}
                                </td>
                                <td>
                                    {{ $val['cotasSimulacao'] }}
                                </td>
                                <td>
                                    {{ number_format($val['dyAno'],2,',','.') }}%
                                </td>
                                <td>
                                    {{ number_format($val['dividendosSimulacaoMensal'],2,',','.') }}
                                </td>
                                <td>
                                    {{ number_format($val['dividendosSimulacaoAnual'],2,',','.') }}
                                </td>
                                <td>{{$val['diasUltimoAporte']}}</td>
                                <td><center><a href="/alvo-deletar/{{$val['codPapel']}}_{{$val['tipo']}}" class="btn btn-danger btn-sm deleta"><i class="fas fa-trash"></i></a></center></td>
                            </tr>
                            
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    </div>
</div>

<script src="{{ asset('/js/alvo.js') }}"></script>

@stop