@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">
            <div class="row">
                <div class="col-sm-10">        
                    Lançamento de Proventos
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
            <form action="/provento-salvar" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">
                
                @php
                    
                    $cotas = [];

                    foreach($data['papeis'] as $num => $val){
                        $cotas[ $val->nmPapel ] = $val->cotas;
                    }

                @endphp

                <input type="hidden" id="qtdeCotas" value="{{json_encode($cotas)}}">

                <fieldset class="borda">
                    
                    <legend>Novo Dividendo</legend>

                    <div class="row">
                        
                        <div class="col-sm-4 form-group">
                            <label>Papel</label>
                            <select name="papel" id="papel" class="form-control form-control-sm" required autofocus="on">
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
                            <input type="number" id="valor" name="valor" step="0.0001" class="form-control form-control-sm calculo" required>
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
                            <label>Data</label>
                            <input type="date" name="dataProvento" id="dataProvento" class="form-control form-control-sm">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tipo</label>
                            <select name="tipo" id="tipo" class="form-control form-control-sm" required>
                                <option></option>
                                <option value="1">{{session()->get('autenticado.tipo_provento')[1]}}</option>
                                <option value="2">{{session()->get('autenticado.tipo_provento')[2]}}</option>
                            </select>
                        </div>

                        <div class="col-sm-6 justify-content-start">
                            <div class="btn-group">
                                <button id="btSalvar" class="btn btn-success btn-sm mt-4 pt-2"><i class="fas fa-check"></i></button>
                                <span class="btn btn-info btn-sm mt-4 pt-2 limpar_form"><i class="fas fa-eraser"></i></span>
                                <span id="btPesquisar" class="btn btn-info mt-4 pt-2"><i class="fas fa-search"></i></span>
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
                <div class="col-sm-9">
                    <h6 class="text text-secondary">Histórico de Proventos</h6>
                </div>
                <div class="col-sm-3 d-flex justify-content-end">
                    <table class="table table-sm mt-3">
                        <tr class="table-info">
                            <td>Total Proventos: R$ {{number_format($data['totalProventos'],2,',','.')}}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr class="table-active">
                        <th>Papel</th>
                        <th>Tipo</th>
                        <th>Sub Tipo</th>
                        <th>Valor</th>
                        <th>Qtde</th>
                        <th>SubTotal</th>
                        <th>Data Provento</th>
                        <!--<th><center>-</center></th>-->
                    </tr>
                </thead>
                <tbody>
                    @if( isset( $data['proventos'] ) && count($data['proventos']) )
                        @foreach( $data['proventos'] as $num => $val )
                            
                            <tr class="tr_provento">
                                <td>{{ $val->nmPapel }}</td>
                                <td>{{ session()->get('autenticado')['tipo_provento'][$val->cdTipo] }}</td>
                                <td>{{ session()->get('autenticado')['sub_tipo'][$val->subTipo] }}</td>
                                <td>R$ {{ number_format($val->valor,2,',','.') }}</td>
                                <td>{{ $val->qtde }}</td>
                                <td>R$ {{ number_format($val->subTotal,2,',','.') }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($val->dtProvento)) }}</td>
                                <!--<td><center><a href="/provento-deletar/{{$val->cdProvento}}" class="btn btn-danger btn-sm deleta"><i class="fas fa-trash"></i></a></center></td>-->

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
                <h4 class="text text-info">Pesquisar Proventos</h4>
            </div>
            <div class="col-sm-1 d-flex justify-content-end">
                <span id="btFechaModal" class="btn btn-default bg-dark text text-light mt-1 mb-1 mr-1"><i class="fas fa-times-circle"></i></span>
            </div>
        </div>
        <div class="row modal-body">
            
            <div class="col-sm-12">

                <form action="/provento-pesquisar" method="post">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">

                    <div class="row">
                        <div class="col-sm-4 d-flex justify-content-end">
                            <label>Papel</label>
                        </div>
                        <div class="col-sm-6 form-group">
                            <select name="papeis[]" id="papelPesquisa" class="form-control form-control-sm" multiple="multiple">
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
                            <select name="tipo" id="tipoPesquisa" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">{{session()->get('autenticado')['tipo_provento'][1]}}</option>
                                <option value="2">{{session()->get('autenticado')['tipo_provento'][2]}}</option>
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

<script src="{{ asset('/js/provento.js') }}"></script>

@stop