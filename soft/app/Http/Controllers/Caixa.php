<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Caixa as CX;
use Illuminate\Http\Request;
use App\Http\Repositories\ExportData as EXPDATA;
use DB;

class Caixa{
    public $expData;

    public function __construct(){
        $this->expData = new EXPDATA();
    }

    public function index(){

        $lancamentos = DB::table('lancamentos')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->orderBy('cdLancamento','desc')
            ->get();
        
        $listagem['lancamentos'] = $lancamentos;
        $listagem['saldo'] = CX::getSaldo([]);

        return view('caixa.index')->with(['data'=>$listagem]);
        
    }

    public function pesquisar(Request $request,$flagExportar=false){
        $params = $request->all();

        //caso tenha sido solicitado exportar relatórios
        if( isset($params['relatorio']) ){
            
            //remove o parametro relatorio
            $tipoRelatorio = $params['relatorio'];
            unset($params['relatorio']);

            $reqParams = new Request($params);

            //exporta
            $this->exportar($reqParams,$tipoRelatorio);
            exit;
        }

        //coleta os dados para view
        $lancamentos = DB::table('lancamentos')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'));

            
        //trata filtros
        if( isset($params['descricao']) && !empty($params['descricao']) ){
            $lancamentos = $lancamentos->where('descricao','like','%'.$params['descricao'].'%');
        }

        if( isset($params['ano']) && !empty($params['ano']) ){
            $lancamentos = $lancamentos->whereYear('dtLancamento',$params['ano']);
        }

        if( isset($params['mes']) && !empty($params['mes']) ){
            $lancamentos = $lancamentos->whereMonth('dtLancamento', $params['mes']);
        }

        $lancamentos = $lancamentos->orderBy('cdLancamento','desc');
        $lancamentos = $lancamentos->get();

        $listagem = ['lancamentos'=>$lancamentos];
        $listagem['saldo'] = CX::getSaldo($params);

        /*
            caso seja uma chamada de pesquisa normal, vai retornar a view com os dados
            se não vai retornar os dados para que seja montado o relatório
        */
        if( !$flagExportar ){
            return view('caixa.index')->with(['data'=>$listagem]);
        }else{
            return $listagem;
        }
    }

    public function salvar( Request $request ){
        
        $acao = CX::salvar($request->all());

        if( $acao > 0 ){
            $msg = "Lançamento registrado com sucesso!|success";
        }else{
            $msg = "Não foi possível registrar o lançamento...|danger";
        }

        return redirect('caixa')->with('status',$msg);

    }

    public function deletar( $id ){
        
        $acao = CX::deletar($id);

        if( $acao > 0 ){
            $msg = "Lançamento deletado com sucesso!|success";
        }else{
            $msg = "Não foi possível deletar o lançamento...|danger";
        }

        return redirect('caixa')->with('status',$msg);

    }

    public function exportar(Request $request,$tipoRelatorio = "excel"){
        
        $data = $this->pesquisar($request, true);
        $data['flagRelatorio'] = true;
        
        $htmlRelatorio = view('tabelas.caixa')->with(['data'=>$data])->render();
        
        $this->expData->setHtml($htmlRelatorio);

        if( $tipoRelatorio == "excel" ){
            $this->expData->output(['html'=>0]);
        }else{
            $this->expData->output(['html'=>1]);
        }

    }

}