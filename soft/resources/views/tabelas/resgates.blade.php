@php
    $simbolo = "R$ ";
@endphp

@if( isset($data['flagRelatorio']) )
    <table>
        <tr>
            <td colspan=12 style="font-size:18pt;"><center><b>Relatório de Resgates</b></center></td>
        </tr>
    </table>

    @php
        $simbolo = "";
    @endphp
@endif

@php
    $qtdeTotal      = 0.00;
    $aplicadoTotal  = 0.00;
    $posicaoTotal   = 0.00;
    $lucroTotal     = 0.00;
@endphp

<table class="table table-sm table-bordered table-striped">
    <thead>
        <tr class="table-active">
            <th>Papel</th>
            <th>Tipo</th>
            <th>Sub Tipo</th>
            <th>Qtde</th>
            <th>Valor</th>
            <th>Cotação</th>
            <th>Total Aplicado</th>
            <th>Posição</th>
            <th>Lucro</th>
            <th>Data Aporte</th>
            <th>% Retorno</th>
            <th>% Administração</th>
            
            @if( !isset($data['flagRelatorio']) )
                <th>Seleção</th>
                <th><center>Status Resgate</center></th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if( isset( $data['aportes'] ) && count($data['aportes']) )
            @foreach( $data['aportes'] as $num => $val )
                
                @php
                    $qtdeTotal      += $val->qtde;
                    $aplicadoTotal  += $val->subTotal;
                    $posicaoTotal   += $val->montante;
                    $lucroTotal     += $val->rentabilidade;
                @endphp

                <tr class="tr_resgate" style="display:none;">

                    <td>{{$val->nmPapel}}</td>
                    <td>{{ session()->get('autenticado')['tipo_papel'][$val->cdTipo] }}</td>
                    <td>{{ session()->get('autenticado')['sub_tipo'][$val->subTipo] }}</td>
                    <td>{{ $val->qtde }}</td>
                    <td>{{$simbolo.number_format($val->valor,2,',','.') }}</td>
                    <td>{{$simbolo.number_format($val->cotacao,2,',','.') }}</td>
                    <td>{{$simbolo.number_format($val->subTotal,2,',','.') }}</td>
                    <td>{{$simbolo.number_format($val->montante,2,',','.') }}</td>
                    
                    @if( $val->rentabilidade > 0 )
                        <td class="text text-success">{{$simbolo.number_format($val->rentabilidade,2,',','.') }}</td>
                    @elseif( $val->rentabilidade == 0 )
                        <td class="text text-info">{{$simbolo.number_format($val->rentabilidade,2,',','.') }}</td>
                    @else
                        <td class="text text-danger">{{$simbolo.number_format($val->rentabilidade,2,',','.') }}</td>
                    @endif

                    <td>{{ date('d-m-Y H:i:s',strtotime($val->dtAporte)) }}</td>
                    <td>{{ number_format( $val->taxaRetorno,2,',','.' ) }}</td>
                    <td>{{ number_format( $val->taxaAdmin,2,',','.' ) }}</td>
                    
                    @if( !isset($data['flagRelatorio']) )
                        <td>
                            <center>
                                <input type="checkbox" class="chec_resgate" data-id="{{$val->cdAporte}}" >
                            </center>
                        </td>
                        <td>
                            <center>
                                <a id="resgate{{$num}}" data-id="resgate{{$num}}" data-cotacao="{{$val->cotacao}}" 
                                    data-tipo="{{session()->get('autenticado.tipo_papel')[ $val->cdTipo ]}}"
                                    data-status="{{$val->cdStatus}}"
                                    href="/resgate-aporte/{{$val->cdAporte}}" class="btn btn-{{$val->classeStatus}} btn-sm resgatar">
                                        <i class="fas fa-trophy"></i>
                                </a>
                            </center>
                        </td>
                    @endif

                </tr>
                
            @endforeach
        @endif
    </tbody>

    <tfoot>
        <tr class="table-active">
            <th colspan=3>Total</th>
            <th>{{$simbolo.number_format($qtdeTotal,2,',','.')}}</th>
            <th colspan=2></th>
            <th>{{$simbolo.number_format($aplicadoTotal,2,',','.')}}</th>
            <th>{{$simbolo.number_format($posicaoTotal,2,',','.')}}</th>
            <th>{{$simbolo.number_format($lucroTotal,2,',','.')}}</th>
            <th colspan=3></th>
            
            @if( !isset($data['flagRelatorio']) )
                <th colspan=2></th>
            @endif
        </tr>
    </tfoot>

</table>