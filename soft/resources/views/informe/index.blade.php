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
                            <select name="descricao" id="descricao" class="form-control form-control-sm" required>
                                <option>Selecione</option>
                                <option value="XP">XP</option>
                                <option value="CLEAR">CLEAR</option>
                                <option value="NUBANK">NUBANK</option>
                                <option value="COFRE">COFRE</option>
                                <option value="CAIXA ECO.">CAIXA ECO.</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Valor</label>
                            <input type="number" id="valor" name="valor" step="0.01" class="form-control form-control-sm calculo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Data</label>
                            <input type="date" name="dataInforme" id="dataInforme" class="form-control form-control-sm" value="{{date('Y-m-d',time())}}">
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
                            <td colspan=3> 
                                <center> <b>Ultimo Lançamento</b> </center>
                            </td>
                            
                            <input type="hidden" id="marcador" value="{{$data['marcador']}}">
                            <input type="hidden" id="marcadorFixo" value="{{$data['marcador']}}">
                        </tr>
                        @foreach( $data['ultimoLancamento'] as $num => $val )
                            <tr>
                                <td colspan=2 id="td_desc_{{$num}}">{{$val->descricao}}</td>
                                <td  id="td_val_{{$num}}" class="totais"><center>R$ {{number_format($val->valor,2,',','.')}}</center></td>
                            </tr>
                            
                            @php $valorTotal += $val->valor; @endphp

                        @endforeach
                        
                        <tr class="table-active">
                            <th colspan=2>Total</th>
                            <td id="td_total">
                                <center>R$ {{number_format($valorTotal,2,',','.')}}</center>
                            </td>
                        </tr>
                        
                    </tbody>
                    
                @endif
                
            </table>

            <div class="row mt-0">
                <div class="col-sm-9">
                    @if( isset($data['ultimoLancamento']) && count($data['ultimoLancamento']) )
                        <h6 class="text text-secondary" id="h3-move-informe"><i> Data: {{ date('d-m-Y',strtotime( $data['ultimoLancamento'][0]->dtInforme )) }} </i></h6>
                    @else
                        <h6 class="text text-secondary" id="h3-move-informe"><i>Sem Lançamentos Informados...</i></h6>
                    @endif
                </div>
                <div class="col-sm-3">
                    <div class="btn-group ml-4">
                        <button class="bt-move-informe" data-move="-1"> <b> << </b> </button>
                        <button class="bt-move-informe" data-move="1"> <b> >> </b> </button>
                    </div>
                </div>
            </div>

        </div>
        
    </div>
</div>

<div class="row">

    <div class="col-sm-12">
        
        <div id="containerGrafico">
            @include('graficos.informe')
        </div>

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