<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use Illuminate\Http\Request;
use App\Http\Repositories\ExportData as EXPDATA;
use DB;

class Informe{
    public $expData;

    public function __construct(){
        $this->expData = new EXPDATA();
    }

    public function index(){

        $lancamentos = DB::table('informe')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->orderBy('cdInforme','desc')
            ->get();
        
        $agrupamentos = DB::table('informe')
            ->select(
                'dtInforme',
                DB::raw('sum(valor) as valor')
            )
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->groupBy('dtInforme')
            ->get();

        $tiposLocais = DB::table('informe')
            ->select('descricao')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->distinct('descricao')
            ->get();
        
        $ultimoLancamento = DB::table('informe')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->orderBy('dtInforme','desc')
            ->limit( count($tiposLocais) )
            ->get();

        $data['lancamentos']        = $lancamentos;
        $data['marcador']           = count($data['lancamentos']) / 5;
        $data['agrupamento']        = $agrupamentos;
        $data['ultimoLancamento']   = $ultimoLancamento;

        return view('informe.index')->with(['data'=>$data]);
        
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
        $lancamentos = DB::table('informe')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'));

            
        //trata filtros
        if( isset($params['descricao']) && !empty($params['descricao']) ){
            $lancamentos = $lancamentos->where('descricao','like','%'.$params['descricao'].'%');
        }

        if( isset($params['ano']) && !empty($params['ano']) ){
            $lancamentos = $lancamentos->whereYear('dtInforme',$params['ano']);
        }

        if( isset($params['mes']) && !empty($params['mes']) ){
            $lancamentos = $lancamentos->whereMonth('dtInforme', $params['mes']);
        }

        $lancamentos = $lancamentos->get();

        $listagem = ['lancamentos'=>$lancamentos];
        
        /*
            caso seja uma chamada de pesquisa normal, vai retornar a view com os dados
            se não vai retornar os dados para que seja montado o relatório
        */
        if( !$flagExportar ){
            return view('informe.index')->with(['data'=>$listagem]);
        }else{
            return $listagem;
        }
    }

    public function salvar( Request $request ){
        //resgata requisição
        $params = $request->all();
        
        //seta os campos previamente
        $campos = [
            'descricao'=>$params['descricao'],
            'valor'=>$params['valor'],
            'dtInforme'=> ( !is_null($params['dataInforme']) && !empty($params['dataInforme']) ) ? date('Y-m-d', strtotime($params['dataInforme'])) . date(' H:i:s') : date('Y-m-d H:i:s'),
            'cdUsuario'=>session()->get('autenticado.id_user')
        ];
            
        $acao = CRUD_DB::salvar(['tabela'=>'informe','dados'=>$campos]);

        //redireciona com status
        if( $acao > 0 ){
            $msg = "Informe de Patrimônio registrado com sucesso!|success";
        }else{
            $msg = "Não foi possível registrar o informe...|danger";
        }

        return redirect('informe')->with('status',$msg);

    }

    public function deletar( $id ){
        
        $dados = [
            'tabela'=>'informe',
            'campo'=>'cdInforme',
            'valor'=>$id
        ];

        $acao = CRUD_DB::deletar($dados);

        if( $acao > 0 ){
            $msg = "Informe de Patrimônio deletado com sucesso!|success";
        }else{
            $msg = "Não foi possível deletar o informe...|danger";
        }

        return redirect('informe')->with('status',$msg);

    }

    public function exportar(Request $request,$tipoRelatorio = "excel"){
        
        $data = $this->pesquisar($request, true);
        $data['flagRelatorio'] = true;
        
        $htmlRelatorio = view('tabelas.informe')->with(['data'=>$data])->render();
        
        $this->expData->setHtml($htmlRelatorio);

        if( $tipoRelatorio == "excel" ){
            $this->expData->output(['html'=>0]);
        }else{
            $this->expData->output(['html'=>1]);
        }

    }

}