@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Fluxo de Caixa - Histórico Depósitos/Retiradas</h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form action="/caixa-salvar" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Novo Lançamento</legend>

                    <div class="row">
                        <div class="col-sm-2 form-group">
                            <label>Descrição</label>
                            <input type="text" id="descricao" name="descricao" class="form-control form-control-sm" autofocus="true" required autocomplete="off">
                        </div>
                        
                        <div class="col-sm-2 form-group">
                            <label>Valor</label>
                            <input type="number" id="valor" name="valor" step="0.01" class="form-control form-control-sm calculo" required>
                        </div>
                        
                        <div class="col-sm-3 form-group">
                            <label>Data</label>
                            <input type="date" name="dataLancamento" id="dataLancamento" class="form-control form-control-sm">
                        </div>

                        <div class="col-sm-2 form-group">
                            <label>Tipo</label>
                            <select name="tipo" id="tipo" class="form-control form-control-sm" required>
                                <option value="1">Depósito</option>
                                <option value="2">Saque/Retirada</option>
                            </select>
                        </div>

                        <div class="col-sm-3 justify-content-start">
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

</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda p-2">
            
            <form action="/caixa-pesquisar" method="post">

                <input type="hidden" name="_token" value="{!! csrf_token() !!}">

                <div class="row">
                    <div class="col-sm-3">
                        <label>Descrição</label>
                        <input type="text" id="descricao_pesquisa" name="descricao" class="form-control form-control-sm" autofocus="true" autocomplete="off">                        
                    </div>
                    <div class="col-sm-3">
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
                    <div class="col-sm-3">
                        <label>Mês</label>
                        <select name="mes" id="mes" class="form-control form-control-sm">
                            <option></option>
                            @for( $i=1;$i<=12;$i++ )
                                @if( $i<=9 )
                                    @php $mes = "0".$i; @endphp
                                @else
                                    @php $mes = $i; @endphp
                                @endif

                                <option value="{{$mes}}">{{unserialize(Redis::get('meses'))[$i]}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="btn-group pt-1">
                            <button class="btn btn-info btn-sm mt-4 pt-2 pb-2" title="Pesquisar Lançamentos"><i class="fas fa-search"></i></button>
                            <button name="relatorio" value="html" class="btn btn-info btn-sm mt-4 pt-2 pb-2" title="Exportar Para HTML"><i class="fas fa-file-code"></i></button>
                            <button name="relatorio" value="excel" class="btn btn-info btn-sm mt-4 pt-2 pb-2" title="Exportar Para Excel"><i class="fas fa-file-excel"></i></button>    
                            <span class="btn btn-info btn-sm mt-4 pt-2 pb-2 visualiza_registros" data-linha="tr_caixa" title="Ocultar/Visualizar Registros"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                </div>
            </form>
        
            <div class="table-responsive">
                @include('tabelas.caixa')
            </div>
        </div>
    </div>
</div>



<script src="{{ asset('/js/caixa.js') }}"></script>

@stop