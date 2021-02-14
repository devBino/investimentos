@php
    $simbolo = "R$ ";
@endphp

@if( isset($data['flagRelatorio']) )
    <table>
        <tr>
            <td colspan=4 style="font-size:18pt;"><center><b>Relatório de Informe de Patrimônio</b></center></td>
        </tr>
    </table>

    @php
        $simbolo = "";
    @endphp
@endif

@php
    $total = 0;
@endphp

<table class="table table-bordered table-sm table-striped" id="dataTable" width="100%" cellspacing="0">
    <thead>
        <tr class="table-active">
            <th>Descrição</th>
            <th>Data</th>
            <th>Valor</th>

            @if( !isset($data['flagRelatorio']) )
                <th><center>-</center></th>
            @endif

        </tr>
    </thead>
    <tbody>
        
        @if( isset( $data['lancamentos'] ) && count($data['lancamentos']) )
            
            @php
                $dataLinha = $data['lancamentos'][0]->dtInforme;
                $linhaGrupo = 1;
            @endphp

            @foreach( $data['lancamentos'] as $num => $val )
                
                @php
                    if( $val->dtInforme != $dataLinha ){
                        $dataLinha = $val->dtInforme;
                        $linhaGrupo += 1;
                    }
                @endphp

                <tr class="tr_informe linha_grupo_{{$linhaGrupo}}" data-dados="{{$val->descricao}}|{{date('d-m-Y',strtotime($val->dtInforme))}}|{{$simbolo.number_format($val->valor,2,',','.')}}">

                    <td>{{$val->descricao}}</td>
                    <td>{{ date('d-m-Y',strtotime($val->dtInforme)) }}</td>
                    <td><center>{{$simbolo.number_format($val->valor,2,',','.') }}</center></td>

                    @if( !isset($data['flagRelatorio']) )
                        <td><center><a href="/informe-deletar/{{$val->cdInforme}}" class="btn btn-danger btn-sm deleta"><i class="fas fa-trash"></i></a></center></td>
                    @endif

                </tr>
                
            @endforeach
        @endif
    </tbody>
    
</table>