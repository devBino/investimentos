@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Preços Alvo Para Aportes/Resgates</h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form action="/alvo-salvar" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Dados do Alvo</legend>

                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Papel</label>
                            <select name="papel" id="papel" class="form-control form-control-sm" required>
                                <option></option>
                                
                                @if( isset($data['papeis']) && count($data['papeis']) )
                                    @foreach( $data['papeis'] as $num => $val )
                                        <option value="{{$val->cdPapel}}">{{$val->nmPapel}}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                        <div class="col-sm-2 form-group">
                            <label>Tipo Alvo</label>
                            <select name="tipo" id="tipo" class="form-control form-control-sm" required>
                                <option></option>
                                <option value="1">APORTE</option>
                                <option value="2">RESGATE</option>
                            </select>
                        </div>
                        <div class="col-sm-2 form-group">
                            <label>Valor</label>
                            <input type="number" step="0.01" id="valor" name="valor" class="form-control form-control-sm">    
                        </div>
                        <div class="col-sm-2 justify-content-start">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm mt-4 pt-2"><i class="fas fa-check pb-2"></i></button>
                                <span class="btn btn-info btn-sm mt-4 pt-2 limpar_form"><i class="fas fa-eraser pb-2"></i></span>
                            </div>                            
                        </div>

                        <div class="col-sm-2 justify-content-end">
                            <div class="btn-group">
                                <span id="atualizaCotacao" class="btn btn-info btn-sm mt-4 pt-2"><i id="icon-bt" class="fas fa-sync"></i> Atualizar Cotações</span>
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
        <div class="borda">
            <div class="row p-0">
                <div class="col-sm-6">
                    <h6 class="text text-secondary">Monitoramento de Alvos</h6>
                </div>
                <div class="col-sm-6">
                    
                    <form action="/alvo-ordenar" method="post">
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <div class="row">
                            <div class="col-sm-5 form-group">
                                <select name="ordenacao" id="ordenacao" class="form-control form-control-sm" required>
                                    <option value="1">Ordenar</option>
                                    <option value="1">Preço Médio</option>
                                    <option value="2">Cotação</option>
                                    <option value="3">Alvo - Cotação</option>
                                    <option value="4">Preço Médio - Cotação</option>
                                </select>
                            </div>
                            <div class="col-sm-5 form-group">
                                <select name="tipo" id="tipo" class="form-control form-control-sm" required>
                                    <option value="1">Tipo</option>
                                    <option value="1">Asc</option>
                                    <option value="2">Desc</option>
                                </select>
                            </div>
                            <div class="col-sm-2 form-group d-flex justify-content-start">
                                <button class="btn btn-info btn-sm"><i class="fas fa-filter"></i></button>   
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>

            <table class="table table-sm table-bordered table-striped" style="margin-top:-25px;">
                <thead>
                    <tr class="table-active">
                        <th>Papel</th>
                        <th>Ativo</th>
                        <th>Preço Médio</th>
                        <th>Preço Alvo</th>
                        <th>Cotação</th>
                        <th>Alvo - Cotação</th>
                        <th>Preço Médio - Cotação</th>
                        <th>Tipo</th>
                        <th>Último Aporte</th>
                        <th colspan='2'><center>-</center></th>
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

                                <td>{{$val['ativo']}}</td>
                                <td>R$ {{number_format($val['precoMedio'],2,',','.')}}</td>
                                <td>R$ {{number_format($val['precoAlvo'],2,',','.')}}</td>
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
                                    @if( $val['tipo'] == 1 )
                                        Aporte
                                    @else
                                        Resgate
                                    @endif
                                </td>
                                <td>{{$val['ultimaOco']}}</td>
                                <td><center><span class="btn btn-info btn-sm edita" data-dados="{{$val['codPapel']}}|{{$val['tipo']}}|{{$val['precoAlvo']}}"><i class="fas fa-edit"></i></span></center></td>
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