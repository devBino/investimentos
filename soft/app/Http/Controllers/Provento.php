<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Movimento as MOV;
use App\Http\Repositories\Papel as PAP;
use App\Http\Repositories\Caixa as CX;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use DB;

class Provento{
    public function __construct(){

    }

    public function index(){
        
        $data['papeis']         = PAP::getPapeisRendaVariavel();
        $data['proventos']      = MOV::getProventos();
        $data['totalProventos'] = MOV::getTotalProventos();
        
        return view('proventos.index')->with(['data'=>$data]);
    }

    public function pesquisar(Request $request){
        $params = $request->all();
        
        $data['papeis']         = PAP::getPapeis();
        $data['proventos']      = MOV::getProventos($params);
        $data['totalProventos'] = MOV::getTotalProventos($params);
        
        return view('proventos.index')->with(['data'=>$data]);
    }

    public function salvar(Request $request){
        $params = $request->all();

        $dadosPapel = PAP::getPapel($params['papel']);

        if( !count($dadosPapel) ){
            return redirect('provento')->with('status','Ativo não localizado...|danger');
        }
        
        $campos = [
            'cdPapel'=>$params['papel'],
            'valor'=>$params['valor'],
            'qtde'=>$params['qtde'],
            'subTotal'=>$params['subTotal'],
            'cdTipo'=>$params['tipo'],
            'dtProvento'=> ( !is_null($params['dataProvento']) && !empty($params['dataProvento']) ) ? date('Y-m-d', strtotime($params['dataProvento'])) . date(' H:i:s') : date('Y-m-d H:i:s'),
            'cdUsuario'=>session()->get('autenticado.id_user')
        ];

        $acao = CRUD_DB::salvar(['tabela'=>'proventos','dados'=>$campos]);

        if( $acao > 0 ){

            CX::salvar([
                'descricao' =>'Proventos - ' 
                    . $dadosPapel[0]->nmPapel . ' ' 
                    . $campos['qtde'] . ' X ' 
                    . number_format($campos['valor'],2,',','.') . ' = ' 
                    . number_format($campos['subTotal'],2,',','.'),
                'tipo' => '1',
                'valor' => $campos['subTotal'],
                'dataLancamento'=>$campos['dtProvento']
            ]);

            $msg = "Provento registrado com sucesso!|success";
        }else{
            $msg = "Não foi possível registrar o provento...|danger";
        }

        return redirect('provento')->with('status',$msg);

    }

    public function deletar( $id ){
        
        $dados = [
            'tabela'=>'proventos',
            'campo'=>'cdProvento',
            'valor'=>$id
        ];

        $acao = CRUD_DB::deletar($dados);

        if( $acao > 0 ){
            $msg = "Provento deletado com sucesso!|success";
        }else{
            $msg = "Não foi possível deletar o provento...|danger";
        }

        return redirect('provento')->with('status',$msg);

    }

    public function proventosMensais(Request $request){

        $params = $request->all();
        $data = [];

        $data['papeis']         = PAP::getPapeisRendaVariavel();

        for($i=1;$i<=12;$i++){
            
            $mes = ( $i>9 ) ? $i : "0".$i;

            $totalProventosMes = DB::table('proventos as p')
                ->select('p.subTotal')
                ->join('papel as pp','p.cdPapel','=','pp.cdPapel')
                ->where('p.cdUsuario',session()->get('autenticado.id_user'));
                
            //filtros
            if( isset($params['ano']) ){
                $totalProventosMes = $totalProventosMes->whereYear('p.dtProvento',$params['ano'] );
            }else{
                $totalProventosMes = $totalProventosMes->whereYear('p.dtProvento', date('Y',time()) );
            }

            $totalProventosMes = $totalProventosMes->whereMonth('p.dtProvento',$mes);

            if( isset($params['papel']) ){
                $totalProventosMes = $totalProventosMes->where('p.cdPapel',$params['papel']);
            }

            if( isset($params['subTipo']) ){
                $totalProventosMes = $totalProventosMes->where('pp.subTipo',$params['subTipo']);
            }


            $totalProventosMes = $totalProventosMes->sum('p.subTotal');

            $data['proventos'][] = ['mes'=>$mes,'nomeMes'=>unserialize(Redis::get('meses'))[$i],'valor'=>$totalProventosMes];
            
        }
        
        return view('proventos.mensal')->with(['data'=>$data]);

    }

    public function proventosPapeis(Request $request){
        
        //recupera parametros da requisição
        $params = $request->all();

        //buscando papeis, inicia variavel $data que será passada pra view
        $data['papeis']         = PAP::getPapeisRendaVariavel();
        $papeis = $data['papeis'];

        //array delimita Ações e FIIs que tem cdTipo 2
        $tiposPapeisPagadoresProventos = [2];

        //inicia arrays auxiliares, $result e $totais
        $result = [];
        
        $totais['totalAportado']            = 0;
        $totais['qtdeCotas']                = 0;
        $totais['posicaoAtual']             = 0;
        $totais['variacaoAntesProventos']   = 0;
        $totais['proventosPagos']           = 0;
        $totais['dYield']                   = 0;
        $totais['valorizacaoReal']          = 0;
        $totais['valorizacaoPercentual']    = 0;

        $registrosValidos = 0;

        //percorre os papeis fazendo os calculos
        for( $i=0; $i<count($papeis); $i++ ){

            //caso o papel seja uma Ação ou FII
            if( in_array($papeis[$i]->cdTipo,$tiposPapeisPagadoresProventos) ){

                //verifica se foram recebidos filtros do formulário
                
                //papel
                if( isset($params['papel']) && !in_array( $papeis[$i]->cdPapel,$params['papel'] ) ){
                    continue;
                }

                //subtipo
                if( isset($params['subTipo']) && !in_array($papeis[$i]->subTipo, $params['subTipo']) ){
                    continue;
                }

                
                //busca aporte mais antigo não resgatado                
                $dadosUltimoAporte = PAP::getUltimoAporte($papeis[$i]->cdPapel);
                
                //caso nunca tenha aportado no papel
                if( !count($dadosUltimoAporte) || !isset($dadosUltimoAporte[0]->dtAporte) ){
                    continue;
                }

                //soma valor total aportado e não resgatado, desde a data do aporte mais antigo não resgatado, até a presente data
                $totalAportado = DB::table('aportes')
                    ->select('subTotal')
                    ->where('cdUsuario',session()->get('autenticado.id_user'))
                    ->where('cdPapel',$papeis[$i]->cdPapel)
                    ->where('cdStatus',1)
                    ->where('dtAporte','>=',$dadosUltimoAporte[0]->dtAporte)
                    ->sum('subTotal');

                //soma quantidade de cotas totais aportadas e não resgatado, desde a data do aporte mais antigo não resgatado, até a presente data
                $qtdeCotas = PAP::getQuantidadeCotasPapel($papeis[$i]->cdPapel, $dadosUltimoAporte);
                
                //soma valor total aportado e não resgatado, desde a data recuperada até a presente data                
                $proventosPagos = DB::table('proventos')
                    ->select('subTotal')
                    ->where('cdUsuario',session()->get('autenticado.id_user'))
                    ->where('cdPapel',$papeis[$i]->cdPapel)
                    ->where('dtProvento','>=',$dadosUltimoAporte[0]->dtAporte)
                    ->sum('subTotal');

                $dYield = $proventosPagos / $totalAportado * 100;

                //calcula posição atual do papel com base no valor da cotação multiplicado por cotas não resgatadas
                $posicaoAtualPapel = $qtdeCotas * $papeis[$i]->cotacao;
                $variacaoAntesProventos = $posicaoAtualPapel - $totalAportado;

                //( Dividendos pagos ) + ( Valor de Posição Atual - Valor Total Aportado )
                //calcula valoricação do papel
                $valorizacaoReal        = $proventosPagos + ( $posicaoAtualPapel - $totalAportado );
                $valorizacaoPercentual  = $valorizacaoReal / $totalAportado * 100;
                
                //alimenta $result para linhas da tabela
                $resultPapel['papel']                    = $papeis[$i]->nmPapel;
                $resultPapel['totalAportado']            = $totalAportado;
                $resultPapel['qtdeCotas']                = $qtdeCotas;
                $resultPapel['posicaoAtual']             = $posicaoAtualPapel;
                $resultPapel['variacaoAntesProventos']   = $variacaoAntesProventos;
                $resultPapel['proventosPagos']           = $proventosPagos;
                $resultPapel['dYield']                   = $dYield;
                $resultPapel['valorizacaoReal']          = $valorizacaoReal;
                $resultPapel['valorizacaoPercentual']    = $valorizacaoPercentual;
                
                $result[] = $resultPapel;
                
                //incrementa totais
                $totais['qtdeCotas'] += $qtdeCotas;
                $totais['totalAportado'] += $totalAportado;
                $totais['posicaoAtual'] += $posicaoAtualPapel;
                $totais['variacaoAntesProventos'] += $variacaoAntesProventos;
                $totais['proventosPagos'] += $proventosPagos;
                $totais['valorizacaoReal'] += $valorizacaoReal;

                $registrosValidos += 1;

            }

        }
 
        //atualiza dados médios em totais
        if( 
            ($totais['totalAportado'] > 0 && ($totais['proventosPagos'] >= 0 || $totais['valorizacaoReal'] > 0) )
            ||
            ($totais['totalAportado'] > 0 && ($totais['proventosPagos'] >= 0 || $totais['valorizacaoReal'] < 0) )
        )
        {
            $totais['dYield']                   = $totais['proventosPagos'] / $totais['totalAportado'] * 100;
            $totais['valorizacaoPercentual']    = $totais['valorizacaoReal'] / $totais['totalAportado'] * 100;
        }

        //calcula percentual da posição de cada papel em relação a posição geral
        for($i=0;$i<count($result);$i++){
            $result[$i]['percentualPosicao'] = $result[$i]['posicaoAtual'] / $totais['posicaoAtual'] * 100;
        }
        
        $data['proventos'] = $result;
        $data['totais'] = $totais;

        return view('proventos.papel')->with(['data'=>$data]);
    }



}