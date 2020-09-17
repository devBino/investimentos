@php
    $simbolo = "R$ ";
@endphp

@if( isset($data['flagRelatorio']) )
    <table>
        <tr>
            <td colspan=4 style="font-size:18pt;"><center><b>Relatório de Lançamentos no Caixa</b></center></td>
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
            <th>Tipo</th>

            @if( !isset($data['flagRelatorio']) )
                <th><center>-</center></th>
            @endif

        </tr>
    </thead>
    <tbody>
        
        @php $sinalOperacao = "-"; @endphp

        @if( isset( $data['lancamentos'] ) && count($data['lancamentos']) )
            @foreach( $data['lancamentos'] as $num => $val )
                
                @php $sinalOperacao = "-"; @endphp
                
                @if( $val->cdTipo == 1 )
                    @php 
                        $sinalOperacao = "+"; 
                        $total += $val->valor;
                    @endphp

                @endif

                @if( !isset($data['flagRelatorio']) )
                    @php
                        $sinalOperacao = ""; 
                        $total -= $val->valor;
                    @endphp
                @endif

                <tr class="tr_caixa" style="display:none;">

                    <td>{{$val->descricao}}</td>
                    <td>{{ date('d-m-Y H:i:s',strtotime($val->dtLancamento)) }}</td>
                    <td><center>{{$sinalOperacao.$simbolo.number_format($val->valor,2,',','.') }}</center></td>
                    <td>
                        @if( $val->cdTipo == 1 )
                            Depósito
                        @else
                            Retirada
                        @endif
                    </td>

                    @if( !isset($data['flagRelatorio']) )
                        <td><center><a href="/caixa-deletar/{{$val->cdLancamento}}" class="btn btn-danger btn-sm deleta"><i class="fas fa-trash"></i></a></center></td>
                    @endif

                </tr>
                
            @endforeach
        @endif
    </tbody>
    <tfoot>
        <tr class="table-active">
            <th colspan=2>Total</th>
            <th>{{$simbolo.number_format($total,2,',','.')}}</th>
            <th></th>
            
            @if( !isset($data['flagRelatorio']) )
                <th></th>
            @endif
        </tr>
    </tfoot>
</table>