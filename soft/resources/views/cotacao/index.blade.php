@extends('template.app')
@section('telas')

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary">Cotações</h6>
    </div>
</div>



<div class="row">
    <div class="col-sm-12">
        <div class="borda">

            <div class="row p-0">
                <div class="col-sm-12">
                    <h6 class="text text-secondary">Monitoramento de Cotações</h6>
                </div>
            </div>

            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr class="table-active">
                        <th>Papel</th>
                        <th>Cotação</th>                        
                    </tr>
                </thead>
                <tbody>
                    @if( isset( $data['listagem'] ) && count($data['listagem']) )
                        @foreach( $data['listagem'] as $num => $val )
                            
                            <tr>
                                <td>{{$val->nmPapel}}</td>
                                <td>R$ {{number_format($val->cotacaoAtual,2,',','.')}}</td>
                            </tr>
                            
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    </div>
</div>


@stop