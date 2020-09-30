@extends('template.app')
@section('telas')

<input type="hidden" id="taxaSelic" value="{{session()->get('autenticado.taxa_selic')}}">

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Lançamento de Aportes</h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form action="/aporte-salvar" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Dados do Aporte</legend>

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
                            <label>Valor</label>
                            <input type="number" id="valor" name="valor" step="0.01" class="form-control form-control-sm calculo" required>
                        </div>
                        
                        <div class="col-sm-2 form-group">
                            <label>Quant.</label>
                            <input type="number" id="qtde" name="qtde" class="form-control form-control-sm calculo" required>
                        </div>
                        
                        <div class="col-sm-2 form-group">
                            <label>Sub Total</label>
                            <input type="number" id="subTotal" name="subTotal" step="0.01" class="form-control form-control-sm" required readonly>
                        </div>

                        <div class="col-sm-2">
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-sm-2 form-group">
                            <label>Taxa Retorno</label>
                            <input type="number" name="taxaRetorno" id="taxaRetorno" step="0.01" value="0" class="form-control form-control-sm">
                        </div>
                        
                        <div class="col-sm-2 form-group">
                            <label>Taxa Administração</label>
                            <input type="number" name="taxaAdmin" id="taxaAdmin" step="0.01" value="0" class="form-control form-control-sm">
                        </div>

                        <div class="col-sm-2 form-group">
                            <label>Data</label>
                            <input type="date" name="dataAporte" id="dataAporte" class="form-control form-control-sm">
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

</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <div class="row">
                <div class="col-sm-10">
                    <h6 class="text text-secondary">Aportes Realizados</h6>
                </div>
                <div class="col-sm-2 d-flex justify-content-end">
                    <div class="btn-group">
                        <span id="btPesquisar" class="btn btn-info btn-sm mr-1 mt-1 mb-1"><i class="fas fa-search pb-2"></i></span>
                    </div>                            
                </div>
            </div>

            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr class="table-active">
                        <th>Papel</th>
                        <th>Tipo</th>
                        <th>Sub Tipo</th>
                        <th>Qtde</th>
                        <th>Valor</th>
                        <th>Cotação</th>
                        <th>SubTotal</th>
                        <th>Data Aporte</th>
                        <th>% Retorno</th>
                        <th>% Administração</th>
                        <th><center>-</center></th>
                    </tr>
                </thead>
                <tbody>
                    @if( isset( $data['aportes'] ) && count($data['aportes']) )
                        @foreach( $data['aportes'] as $num => $val )
                            
                            <tr class="tr_aporte">

                                <td>{{$val->nmPapel}}</td>
                                <td>{{ session()->get('autenticado')['tipo_papel'][$val->cdTipo] }}</td>
                                <td>{{ session()->get('autenticado')['sub_tipo'][$val->subTipo] }}</td>
                                <td>{{ $val->qtde }}</td>
                                <td>R$ {{ number_format($val->valor,2,',','.') }}</td>
                                <td>R$ {{ number_format($val->cotacao,2,',','.') }}</td>
                                <td>R$ {{ number_format($val->subTotal,2,',','.') }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($val->dtAporte)) }}</td>
                                <td>{{ number_format( $val->taxaRetorno,2,',','.' ) }}</td>
                                <td>{{ number_format( $val->taxaAdmin,2,',','.' ) }}</td>
                                <td><center><a href="/aporte-deletar/{{$val->cdAporte}}" class="btn btn-danger btn-sm deleta"><i class="fas fa-trash"></i></a></center></td>

                            </tr>
                            
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    </div>
</div>

<div id="modal" class="modal">
    <div class="panel-modal">
        <div class="row modal-header pb-0 pt-0 ">
            <div class="col-sm-11">
                <h4 class="text text-info">Pesquisar Aportes</h4>
            </div>
            <div class="col-sm-1 d-flex justify-content-end">
                <span id="btFechaModal" class="btn btn-default bg-dark text text-light mt-1 mb-1 mr-1"><i class="fas fa-times-circle"></i></span>
            </div>
        </div>
        <div class="row modal-body">
            
            <div class="col-sm-12">

                <form action="/aporte-pesquisar" method="post">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">

                    <div class="row">
                        <div class="col-sm-4 d-flex justify-content-end">
                            <label>Papel</label>
                        </div>
                        <div class="col-sm-6 form-group">
                            <select name="papel" id="papelPesquisa" class="form-control form-control-sm">
                                <option></option>
                                
                                @if( isset($data['papeis']) && count($data['papeis']) )
                                    @foreach( $data['papeis'] as $num => $val )
                                        <option value="{{$val->cdPapel}}">{{$val->nmPapel}}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="row">                            
                        <div class="col-sm-4 d-flex justify-content-end">
                            <label>Tipo</label>
                        </div>
                        <div class="col-sm-6 form-group">
                            <select name="tipo" id="tipo" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">RENDA FIXA</option>
                                <option value="2">RENDA VARIAVEL</option>
                                <option value="3">HEDGE</option>
                            </select>
                        </div>
                        <div class="col-sm-2 form-group">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 d-flex justify-content-end">
                            <label>Sub Tipo</label>
                        </div>
                        <div class="col-sm-6 form-group">
                            <select name="subTipo" id="subTipo" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">RENDA FIXA</option>
                                <option value="2">FUNDOS IMOBILIÁRIOS</option>
                                <option value="3">AÇÕES</option>
                                <option value="4">HEDGE</option>
                            </select>
                        </div>
                        <div class="col-sm-2 form-group">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 d-flex justify-content-end">
                            <label>Data Inicio</label>
                        </div>
                        <div class="col-sm-6 form-group">
                            <input type="date" name="dataInicio" id="dataInicio" class="form-control form-control-sm">
                        </div>
                        <div class="col-sm-2 form-group">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 d-flex justify-content-end">
                            <label>Data Final</label>
                        </div>
                        <div class="col-sm-6 form-group">
                            <input type="date" name="dataFinal" id="dataFinal" class="form-control form-control-sm">
                        </div>
                        <div class="col-sm-2 form-group">
                            <div class="btn-group">
                                <button class="btn btn-default bg-dark text text-light btn-sm mt-1"><i class="fas fa-filter"></i></button>
                            </div>                            
                        </div>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>

<script src="{{ asset('/js/aporte.js') }}"></script>

@stop