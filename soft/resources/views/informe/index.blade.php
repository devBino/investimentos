@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Informe Patrimônio - Lançamentos Resumidos Manuais</h6>
    </div>
</div>

<div class="row">

    <div class="col-sm-12">
        <div class="borda">

            <form action="/informe-salvar" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Novo Lançamento</legend>

                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Descrição</label>
                            <select name="descricao" id="descricao" class="form-control form-control-sm" required>
                                <option>Selecione</option>

                                @if( $data['instituicoes'] && count($data['instituicoes']) )

                                    @foreach( $data['instituicoes'] as $num => $val )
                                        <option value="{{$val}}">{{$val}}</option>
                                    @endforeach

                                @endif

                            </select>
                        </div>
                        
                        <div class="col-sm-2 form-group">
                            <label>Valor</label>
                            <input type="number" id="valor" name="valor" step="0.01" class="form-control form-control-sm calculo" required>
                        </div>
                    
                        <div class="col-sm-3 form-group">
                            <label>Data</label>
                            <input type="date" name="dataInforme" id="dataInforme" class="form-control form-control-sm" value="{{date('Y-m-d',time())}}">
                        </div>

                        <div class="col-sm-3 justify-content-start">
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm mt-4 pt-2"><i class="fas fa-check pb-2"></i></button>
                                <span class="btn btn-info btn-sm mt-4 pt-2 limpar_form"><i class="fas fa-eraser pb-2"></i></span>
                                <span class="btn btn-info btn-sm mt-4 pt-2 view-grafico"><i class="fas fa-chart-line pb-2"></i></span>
                                <span class="btn btn-info btn-sm mt-4 pt-2 view-tabela"><i class="fas fa-table pb-2"></i></span>
                            </div>
                        </div>
                    </div>

                </fieldset>
            </form>

        </div>
    </div>
    
</div>

<div class="row" id="rowGrafico">

    <div class="col-sm-12">

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <div id="containerGrafico">
            @include('graficos.informe')
        </div>

        <div class="borda">
            <div id="divInforme" style="width:100%;height:400px;"></div>
        </div>

    </div>
</div>

<div class="row" id="rowTabela">
    <div class="col-sm-12">
        <div class="borda p-2" style="height:800px;overflow-y:scroll;">
            
            <div class="table-responsive">
                @include('tabelas.informe')
            </div>

        </div>

    </div>
</div>


<div id="modal" class="modal">
    <div class="panel-modal">
        <div class="row modal-header pb-0 pt-0 ">
            <div class="col-sm-11">
                <h4 class="text text-info">Informes na Data <span id="sp-data"></span></h4>
            </div>
            <div class="col-sm-1 d-flex justify-content-end">
                <span id="btFechaModal" class="btn btn-default bg-dark text text-light mt-1 mb-1 mr-1"><i class="fas fa-times-circle"></i></span>
            </div>
        </div>
        <div class="row modal-body">
            
            <div class="col-sm-12">

                <table class="table table-bordered table-sm table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <th>Id</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                    </thead>
                    <tbody id="tbody-informes">

                    </tbody>
                </table>

            </div>

        </div>
    </div>
</div>

<script src="{{ asset('/js/informe.js') }}"></script>

@stop