@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Informe de Resgates</h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form id="frResgate" action="/resgate-pesquisa" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Dados Resgate</legend>

                    <div class="row">
                        
                        <div class="col-sm-5 form-group">
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
                            <label>Data Inicio</label>
                            <input type="date" name="dataInicio" id="dataInicio" class="form-control form-control-sm">
                        </div>

                        <div class="col-sm-2 form-group">
                            <label>Data Final</label>
                            <input type="date" name="dataFinal" id="dataFinal" class="form-control form-control-sm">
                        </div>
                        <div class="col-sm-3">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-5 form-group">
                            <label>Tipo</label>
                            <select name="tipo" id="tipo" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">RENDA FIXA</option>
                                <option value="2">RENDA VARIAVEL</option>
                                <option value="3">HEDGE</option>
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <label>Sub Tipo</label>
                            <select name="subTipo" id="subTipo" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">RENDA FIXA</option>
                                <option value="2">FUNDOS IMOBILIÁRIOS</option>
                                <option value="3">AÇÕES</option>
                                <option value="4">HEDGE</option>
                            </select>
                        </div>

                        <div class="col-sm-2 form-group">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">NÃO RESGATADO</option>
                                <option value="2">RESGATADO</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-3 justify-content-start">
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm mt-4 pt-2" title="Pesquisar Resgates"><i class="fas fa-search pb-2"></i></button>
                                <button name="relatorio" value="html" class="btn btn-info btn-sm mt-4 pt-2" title="Exportar Para HTML"><i class="fas fa-file-code pb-2"></i></button>
                                <button name="relatorio" value="excel" class="btn btn-info btn-sm mt-4 pt-2" title="Exportar Para Excel"><i class="fas fa-file-excel pb-2"></i></button>
                                <span class="btn btn-info btn-sm mt-4 pt-2 limpar_form" title="Limpar Formulário"><i class="fas fa-eraser pb-2"></i></span>
                                <span class="btn btn-info btn-sm mt-4 pt-2 visualiza_registros" data-linha="tr_resgate" title="Ocultar/Visualizar Registros"><i class="fas fa-eye pb-2"></i></span>
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
            <div class="row">
                <div class="col-sm-8">
                    <h6 class="text text-secondary">Listagem de Papeis e Respectivos Resgates</h6>
                </div>
                <div class="col-sm-4 d-flex justify-content-end">
                    <div class="btn-group">
                        <form action="/resgate-aportes" method="post">
                            
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <input type="hidden" name="aportes" id="aportes">
                            <input type="hidden" name="cotacao" id="cotacao">
                            
                            <span id="btMarcarAportes" class="btn btn-default btn-sm bg-dark text text-light"><i class="fas fa-check"></i> Todos</span>
                            <button id="btResgatar" class="btn btn-success btn-sm"><i class="fas fa-trophy"></i> Alterar Status Resgate</button>

                        </form>
                    </div>
                </div>
            </div>

            @include('tabelas.resgates')

        </div>
    </div>
</div>

<script src="{{ asset('/js/resgate.js') }}"></script>

@stop