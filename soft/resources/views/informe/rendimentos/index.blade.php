@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">
            <div class="row">
                <div class="col-sm-12">        
                    Informe de Rendimentos
                </div>
            </div>
        </h6>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <form action="/rendimentos" method="post">
                
                <input type="hidden" id="tkn" name="_token" value="{!! csrf_token() !!}">

                <fieldset class="borda">
                    
                    <legend>Pesquisa Resultados</legend>

                    <div class="row">
                        
                        <div class="col-sm-3 form-group">
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

                        <div class="col-sm-4 form-group">
                            <label>Sub Tipo</label>
                            <select name="subTipo" id="subTipo" class="form-control form-control-sm">
                                <option></option>
                                <option value="1">{{ session()->get('autenticado')['sub_tipo'][1] }}</option>
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
    <div class="col-sm-12">
        <div class="borda" style="width:100%;height:1000px;">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr class="table-active">
                        <th>Totais</th>
                        <th>Aportes</th>
                        <th>Resgates</th>
                        <th>Lucro</th>
                        <th>IR Bruto</th>
                        <th>IR Devido</th>

                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td></td>
                        <td>{{number_format($data['totais']['aportes'],2,',','.')}}</td>
                        <td>{{number_format($data['totais']['resgates'],2,',','.')}}</td>
                        <td>{{number_format($data['totais']['lucro'],2,',','.')}}</td>
                        <td>{{number_format($data['totais']['ir'],2,',','.')}}</td>
                        <td>{{number_format($data['totais']['irDevido'],2,',','.')}}</td>
                    </tr>
                </tbody>

                <thead>
                    <tr class="table-active">
                        <th>Papel</th>
                        <th>Aportes</th>
                        <th>Resgates</th>
                        <th>Lucro</th>
                        <th>IR</th>
                        <th>IR Devido</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @if( isset($data['rendimentos']) && count($data['rendimentos']) > 0 )
                        @foreach($data['rendimentos'] as $num => $val)
                            <tr>
                                <td>{{$val->cdPapel}} - {{$val->nmPapel}}</td>
                                <td>{{number_format($val->rendimentos[0]->aportes,2,',','.')}}</td>
                                <td>{{number_format($val->rendimentos[0]->resgates,2,',','.')}}</td>
                                <td>{{number_format($val->rendimentos[0]->lucro,2,',','.')}}</td>
                                <td>{{number_format($val->rendimentos[0]->descontoIr,2,',','.')}}</td>
                                <td>{{number_format($val->irDevido,2,',','.')}}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    </div>
    
</div>

<script src="{{ asset('/js/rendimentos.js') }}"></script>

@stop