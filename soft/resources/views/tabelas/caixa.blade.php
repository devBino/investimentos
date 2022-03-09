@php
    $simbolo = "R$ ";
    $displayTr = 'none';
@endphp

@if( isset($data['flagRelatorio']) )
    <table>
        <tr>
            <td colspan=4 style="font-size:18pt;"><center><b>Relatório de Lançamentos no Caixa</b></center></td>
        </tr>
    </table>

    @php
        $simbolo = "";
        $displayTr = 'block';
    @endphp
@endif

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
        
        

        @if( isset( $data['lancamentos'] ) && count($data['lancamentos']) )
            @foreach( $data['lancamentos'] as $num => $val )
                 
                @php
                    $sinalOperacao      = "-"; 
                    $classSpanValores   = "text-danger";
                @endphp

                @if( $val->cdTipo == 1 )
                    @php 
                        $sinalOperacao = "+";
                        $classSpanValores   = "text-success";
                    @endphp

                @endif

                <tr class="tr_caixa" style="display:{{$displayTr}};">

                    <td>{{$val->descricao}}</td>
                    <td>{{ date('d-m-Y H:i:s',strtotime($val->dtLancamento)) }}</td>
                    <td>
                        <center>
                            <span class="{{$classSpanValores}}">
                                {{$sinalOperacao.$simbolo.number_format($val->valor,2,',','.') }}
                            </span>
                        </center>
                    </td>
                    <td>
                        @if( $val->cdTipo == 1 )
                            Depósito
                        @else
                            Retirada
                        @endif
                    </td>

                    @if( !isset($data['flagRelatorio'])
                        && strrpos($val->descricao, "Aporte") === false 
                        && strrpos($val->descricao, "Resgate") === false )

                        <!--<td><center><a href="/caixa-deletar/{{$val->cdLancamento}}" class="btn btn-danger btn-sm deleta"><i class="fas fa-trash"></i></a></center></td>-->

                        <td><center>-</center></td>

                    @else

                        <td><center>-</center></td>

                    @endif

                </tr>
                
            @endforeach
        @endif
    </tbody>
    <tfoot>
        <tr class="table-active">
            <th colspan=2>Total</th>
            <th>
                <center>
                    {{$simbolo.number_format($data['saldo'],2,',','.')}}
                </center>
            </th>
            <th></th>
            
            @if( !isset($data['flagRelatorio']) )
                <th></th>
            @endif
        </tr>
    </tfoot>
</table>