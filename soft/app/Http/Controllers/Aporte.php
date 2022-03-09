<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Movimento as MOV;
use App\Http\Repositories\Papel as PAP;
use App\Http\Repositories\Aporte as APO;
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
        
        //veirificações iniciais
        $params = $request->all();
        
        $valorAporte    = $params['subTotal'];
        $saldoAtual     = CX::getSaldo([],true);
        
        if( (float) $valorAporte > (float) $saldoAtual ){
            return redirect('aporte')->with('status','Saldo insuisciente...|info');
        }

        //prepara dados e salva aporte
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

        //salva movimentação no fluxo de caixa
        $dadosPapel = PAP::getPapel($params['papel']);

        //mensagem e atualização de movimentação em caixa de acordo com acao
        if( $acao > 0 ){

            CX::salvar([
                'descricao' =>'Aportes Realizados - Código: ' . $campos['cdPapel'] . " - " . $dadosPapel[0]->nmPapel,
                'tipo' => '2',
                'valor' => $campos['subTotal'],
                'dataLancamento'=>$campos['dtAporte']
            ]);

            $msg = "Aporte registrado com sucesso!|success";
            
        }else{
            $msg = "Não foi possível registrar o aporte...|danger";
        }

        return redirect('aporte')->with('status',$msg);

    }

    public function deletar( $id ){

        //recupera dados do aporte e do papel
        $dadosAporte = APO::getAporte($id);

        if( !count($dadosAporte) ){
            return redirect('aporte')->with('status','Aporte não localizado...|info');
        }

        $dadosPapel = PAP::getPapel($dadosAporte[0]->cdPapel);

        if( !count($dadosPapel) ){
            return redirect('aporte')->with('status','Papel não localizado...|info');
        }

        //prepara dados e deleta o aporte
        $params = [
            'tabela'=>'aportes',
            'campo'=>'cdAporte',
            'valor'=>$id
        ];

        $acao = CRUD_DB::deletar($params);

        //mensagem e atualização de movimentação em caixa de acordo com acao
        if( $acao > 0 ){

            //salva movimentação no fluxo de caixa
            CX::salvar([
                'descricao' =>'Aportes Cancelados - Código: ' . $dadosPapel[0]->cdPapel . " - " . $dadosPapel[0]->nmPapel,
                'tipo' => '1',
                'valor' => $dadosAporte[0]->subTotal,
                'dataLancamento'=>date('Y-m-d H:i:s')
            ]);

            $msg = "Aporte deletado com sucesso!|success";
        }else{
            $msg = "Não foi possível deletar o aporte...|danger";
        }

        return redirect('aporte')->with('status',$msg);

    }

}