@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Cadastro de Papeis</h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form action="/papel-salvar" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Dados do Papel</legend>

                    <div class="row">
                        <div class="col-sm-2 form-group">
                            <label>Papel</label>
                            <input type="hidden" name="nomeAntigo" id="nomeAntigo">
                            <input type="text" id="papel" name="papel" class="form-control form-control-sm" autofocus="true" required autocomplete="off">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label>Tipo</label>
                            <select name="tipo" id="tipo" class="form-control form-control-sm" required>
                                <option></option>
                                <option value="1">RENDA FIXA</option>
                                <option value="2">RENDA VARIAVEL</option>
                                <option value="3">HEDGE</option>
                            </select>
                        </div>
                        <div class="col-sm-2 form-group">
                            <label>Sub Tipo</label>
                            <select name="subTipo" id="subTipo" class="form-control form-control-sm" required>
                                <option></option>
                                <option value="1">RENDA FIXA</option>
                                <option value="2">FUNDOS IMOBILIÁRIOS</option>
                                <option value="3">AÇÕES</option>
                                <option value="4">HEDGE</option>
                            </select>
                        </div>
                        <div class="col-sm-2 form-group">
                            <label>Imposto IR</label>
                            <select name="taxaIr" id="taxaIr" class="form-control form-control-sm" required>
                                <option value="0">PADRÃO</option>
                                <option value="20">FUNDO IMOBILIÁRIOS</option>
                                <option value="15">AÇÕES</option>
                            </select>
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
            <div class="row">
                <div class="col-sm-10">
                    <h6 class="text text-secondary">Listagem de Papeis Cadastrados</h6>
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
                        <th>Cotação</th>
                        <th>Tipo</th>
                        <th>Sub Tipo</th>
                        <th>Aportes</th>
                        <th>Total Aportado</th>
                        <th>Resgates</th>
                        <th>Total Resgatado</th>
                        <th>Proventos</th>
                        <th>Total Proventos</th>
                        <th>IR</th>
                        <th colspan='2'><center>-</center></th>
                    </tr>
                </thead>
                <tbody>
                    @if( isset( $data['listagem'] ) && count($data['listagem']) )
                        @foreach( $data['listagem'] as $num => $val )
                            
                            <tr class="tr_papel">

                                <td>{{$val->nmPapel}}</td>
                                <td><center>R$ {{ number_format($val->cotacao,2,',','.') }}</center></td>
                                <td>{{ session()->get('autenticado')['tipo_papel'][$val->cdTipo] }}</td>
                                <td>{{ session()->get('autenticado')['sub_tipo'][$val->subTipo] }}</td>
                                <td> <center>{{$val->qtdeAportes}}x </center></td>
                                <td><center>R$ {{ number_format($val->totalAportado,2,',','.') }}</center></td>
                                <td><center>{{$val->qtdeResgates}}</center></td>
                                <td><center>R$ {{number_format($val->totalResgatado,2,',','.')}}</center></td>
                                <td><center>{{$val->qtdeProventos}}</center></td>
                                <td><center>R$ {{number_format($val->totalProventos,2,',','.')}}</center></td>
                                <td><center>{{number_format($val->taxaIr,2,',','.')}}%</center></td>
                                <td><center><span class="btn btn-info btn-sm edita" data-dados="{{$val->nmPapel}}|{{$val->cdTipo}}|{{$val->subTipo}}|{{ (int) $val->taxaIr}}"><i class="fas fa-edit"></i></span></center></td>
                                <td><center><a href="/papel-deletar/{{$val->cdPapel}}" class="btn btn-danger btn-sm deleta"><i class="fas fa-trash"></i></a></center></td>

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
                <h4 class="text text-info">Pesquisar Papeis</h4>
            </div>
            <div class="col-sm-1 d-flex justify-content-end">
                <span id="btFechaModal" class="btn btn-default bg-dark text text-light mt-1 mb-1 mr-1"><i class="fas fa-times-circle"></i></span>
            </div>
        </div>
        <div class="row modal-body">
            
            <div class="col-sm-12">

                <form action="/papel-pesquisar" method="post">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">

                    <div class="row">
                        <div class="col-sm-4 d-flex justify-content-end">
                            <label>Papel</label>
                        </div>
                        <div class="col-sm-6 form-group">
                            <input type="text" id="papelPesquisa" name="papel" class="form-control form-control-sm" autofocus="true" autocomplete="off">
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
                            <select name="subTipo" id="subTipoPesquisa" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">RENDA FIXA</option>
                                <option value="2">FUNDOS IMOBILIÁRIOS</option>
                                <option value="3">AÇÕES</option>
                                <option value="4">HEDGE</option>
                            </select>
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

<script src="{{ asset('/js/papel.js') }}"></script>

@stop