<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Movimento as MOV;
use App\Http\Repositories\Papel as PAP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use DB;

class Provento{
    public function __construct(){

    }

    public function index(){
        
        $data['papeis']         = PAP::getPapeis();
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

        $data['papeis']         = PAP::getPapeis();

        for($i=1;$i<=12;$i++){
            
            $mes = ( $i>9 ) ? $i : "0".$i;

            $totalProventosMes = DB::table('proventos as p')
                ->select('p.subTotal')
                ->join('papel as pp','p.cdPapel','=','pp.cdPapel')
                ->where('p.cdUsuario',session()->get('autenticado.id_user'));
                
            //filtros
            if( isset($params['ano']) ){
                $totalProventosMes = $totalProventosMes->whereYear('p.dtProvento',$params['ano'] );
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

}