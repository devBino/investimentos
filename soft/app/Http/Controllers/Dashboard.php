<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Movimento as MOV;
use App\Http\Repositories\Caixa as CX;
use App\Http\Repositories\Papel as PAP;
use App\Http\Repositories\Patrimonio as PAT;
use App\Http\Repositories\RedisCache as RC;
use App\Http\Repositories\Cotacao as COT;
use DB;

class Dashboard{

    public function index(){
        
        $data['papeis']             = PAP::getPapeis();
        $data['totalCaixa']         = CX::getSaldo([],true);

        if( !count($data['papeis']) ){
            return redirect('papel')->with('status','Você ainda não tem papeis cadastrados...|info');
        }

        if( !($data['totalCaixa'] > 0 ) && !count($data['papeis']) ){
            return redirect('caixa')->with('status','Você não possui saldo para fazer aportes...|info');
        }

        $data['totalAportes']           = MOV::getSomaAportesUsuario();
        $data['rendimentoAportes']      = MOV::getRendimentosUsuario();

        $data['variacaoPatrimonial']    = 0.00;

        if( $data['rendimentoAportes'] > 0.00 && $data['totalAportes'] > 0.00 ){
            $data['variacaoPatrimonial']    = ( $data['rendimentoAportes'] - $data['totalAportes'] ) / $data['totalAportes'] * 100;
        }

        $data['totalResgates']          = MOV::getSomaResgatesUsuario();
        $data['totalPatrimonio']        = $data['totalCaixa'] + $data['rendimentoAportes'];
        $data['contagemTipos']          = PAT::getContagemTiposAtivos();
        $data['contagemSubTipos']       = PAT::getContagemTiposAtivos([],'subTipo');
        $data['contagemPapeis']         = PAT::getContagemPapeis();
        $data['permissaoValores']       = RC::redisGet( session()->get('autenticado.id_user').'_permissao_valores' );
        
        return view('dashboard.index')->with(['data'=>$data]);
    }

    public function permissaoValores(Request $request){
        $params     = $request->all();
        $chave      = session()->get('autenticado.id_user').'_permissao_valores';
        
        if( $params['status'] == 1 ){
            $conteudo = 0;
        }else{
            $conteudo = 1;
        }
        
        RC::redisDel($chave);
        RC::redisSet($chave,$conteudo);

        $return = ['success'=>true,'chave'=>$chave,'conteudo'=>$conteudo];
        
        return response($return)->header('Content-Type','application/json');
    }


}