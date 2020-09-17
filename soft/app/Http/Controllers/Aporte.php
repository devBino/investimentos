<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Movimento as MOV;
use App\Http\Repositories\Papel as PAP;
use App\Http\Repositories\Caixa as CX;
use Illuminate\Http\Request;
use DB;

class Aporte{
    public function __construct(){

    }

    public function index(){
        
        $data['papeis']     = PAP::getPapeis();
        $data['aportes']    = MOV::getAportes();
        
        return view('aportes.index')->with(['data'=>$data]);
    }

    public function pesquisar(Request $request){
        $params = $request->all();
        
        $data['papeis']     = PAP::getPapeis();
        $data['aportes']    = MOV::getAportes($params);
        
        return view('aportes.index')->with(['data'=>$data]);
    }

    public function salvar(Request $request){
        $params = $request->all();
        
        $valorAporte    = $params['subTotal'];
        $saldoAtual     = CX::getSaldo([],true);
        
        if( (float) $valorAporte > (float) $saldoAtual ){
            return redirect('aporte')->with('status','Saldo insuisciente...|info');
        }

        $campos = [
            'cdPapel'=>$params['papel'],
            'valor'=>$params['valor'],
            'qtde'=>$params['qtde'],
            'subTotal'=>$params['subTotal'],
            'dtAporte'=> ( !is_null($params['dataAporte']) && !empty($params['dataAporte']) ) ? date('Y-m-d', strtotime($params['dataAporte'])) . date(' H:i:s') : date('Y-m-d H:i:s'),
            'cdStatus'=>1,
            'taxaRetorno'=>$params['taxaRetorno'],
            'taxaAdmin'=>$params['taxaAdmin'],
            'cdUsuario'=>session()->get('autenticado.id_user')
        ];

        $acao = CRUD_DB::salvar(['tabela'=>'aportes','dados'=>$campos]);

        if( $acao > 0 ){
            $msg = "Aporte registrado com sucesso!|success";
        }else{
            $msg = "Não foi possível registrar o aporte...|danger";
        }

        return redirect('aporte')->with('status',$msg);

    }

    public function deletar( $id ){
        
        $dados = [
            'tabela'=>'aportes',
            'campo'=>'cdAporte',
            'valor'=>$id
        ];

        $acao = CRUD_DB::deletar($dados);

        if( $acao > 0 ){
            $msg = "Aporte deletado com sucesso!|success";
        }else{
            $msg = "Não foi possível deletar o aporte...|danger";
        }

        return redirect('aporte')->with('status',$msg);

    }

}