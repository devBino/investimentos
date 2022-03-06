<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Movimento as MOV;
use App\Http\Repositories\Papel as PAP;
use App\Http\Repositories\Rentabilidade as RENT;
use App\Http\Repositories\ExportData as EXPDATA;
use Illuminate\Http\Request;
use DB;

class Resgate{
    public $expData;

    public function __construct(){
        $this->expData = new EXPDATA();
    }

    public function index(){
        
        $data['papeis'] = PAP::getPapeis();
        $data['aportes'] = MOV::getAportes();

        return view('resgates.index')->with(['data'=>$data]);
    }

    /**
     * @see
     * Função pode ser chamada pelo botão pesquisar da view resgates.index
     * pode ser chamada também pelos botões de exportação excel e html
     * 
     * se for chamada pelo botão pesquisar apenas vai retornar a view passando os dados
     * se for chamada por algum botão de exportação de dados excel ou html
     * primeiro ela verifica se tem a chave 'relatorio' nos parametros, se estiver salva a chave na variavel $tipoRelatorio
     * depois elimina a chave do array $params
     * em seguida chama a função a exportar passando os parametros numa instancia de Request
     * quando a função exportar estiver sendo executada, uma das ações é chamar novamente a pesquisar,
     * assim a remoção da chave relatorio impede que uma função chame a outra
     * eternamente.
    */
    public function pesquisar(Request $request, $flagExportar = false){
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

        //coleta os dados pra view
        $data['papeis'] = PAP::getPapeis();
        $data['aportes'] = MOV::getAportes($params);

        /*
            caso seja uma chamada de pesquisa normal, vai retornar a view com os dados
            se não vai retornar os dados para que seja montado o relatório
        */
        if( !$flagExportar ){
            return view('resgates.index')->with(['data'=>$data]);
        }else{
            return $data;
        }
    }

    public function resgatarAportes(Request $request){

        $params = $request->all();
        
        //verifica se existem aportes
        if( !isset($params['aportes']) || empty($params['aportes']) || is_null($params['aportes']) ){
            return redirect('resgate')->with('status','Por favor, Selecione os aportes|info');
        }

        //verifica se os aportes são do mesmo papel
        $arrAportes = explode(',',$params['aportes']);
        
        $agrupaAportes = DB::table('aportes')
            ->select('cdAporte','cdPapel')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->whereIn('cdAporte',$arrAportes)
            ->groupBy('cdPapel')
            ->get();

        if( count($agrupaAportes) > 1 ){
            return redirect('resgate')->with('status','Os aportes pra resgate devem ser do mesmo Papel|info');
        }

        //efetua os resgates um por um sem redirecionar
        for($i=0;$i<count($arrAportes);$i++){
            if( empty($params['cotacao']) || is_null($params['cotacao']) ){
                self::resgatarAporte($arrAportes[$i],'0.00',false);
            }else{
                self::resgatarAporte($arrAportes[$i],$params['cotacao'],false);
            }
        }

        return redirect('resgate')->with('status','Ação concluida com sucesso!|success');

    }

    public function resgatarAporte($aporte,$cotacao = 0.00,$redirect = true){
        
        $cotacao = preg_replace('/[^0-9,]/','',$cotacao);
        $cotacao = str_replace(',','.',$cotacao);

        $dadosAporte = DB::table('aportes')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdAporte',$aporte)
            ->get();

        if( !count($dadosAporte) ){
            return redirect('resgate');
        }
        
        if( $dadosAporte[0]->cdStatus == 1 ){

            $campos['cdPapel'] = $dadosAporte[0]->cdPapel;
            $campos['cdAporte'] = $dadosAporte[0]->cdAporte;
            $campos['valor'] = $dadosAporte[0]->valor;
            $campos['qtde'] = $dadosAporte[0]->qtde;
            $campos['subTotal'] = $dadosAporte[0]->subTotal;

            $dadosPapel = DB::table('papel')
                ->select('cotacao','cdTipo','nmPapel','taxaIr')
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->where('cdPapel',$dadosAporte[0]->cdPapel)
                ->get();

            $tipoPapel = session()->get('autenticado.tipo_papel')[ $dadosPapel[0]->cdTipo ];
            
            if( strpos($tipoPapel,'RENDA FIX') !== false ){

                $paramsCalculo = [
                    'capital'=>$dadosAporte[0]->subTotal,
                    'dtAporte'=>$dadosAporte[0]->dtAporte,
                    'taxaRetorno'=>$dadosAporte[0]->taxaRetorno,
                    'taxaAdmin'=>$dadosAporte[0]->taxaAdmin,
                    'aplicarIr'=>true
                ];
                
                $detalhesResgate = RENT::calculoRentabilidade($paramsCalculo,true);
                
                $campos['capitalInicial'] = $detalhesResgate['capitalInicial'];
                $campos['diasCorridos'] = $detalhesResgate['diasCorridos'];
                $campos['montanteBruto'] = $detalhesResgate['montanteBruto'];
                $campos['montanteLiquido'] = $detalhesResgate['montanteLiquido'];
                $campos['lucroBruto'] = $detalhesResgate['lucroBruto'];
                $campos['lucroLiquido'] = $detalhesResgate['lucroLiquido'];
                $campos['taxaIof'] = $detalhesResgate['taxaIof'];
                $campos['taxaIr'] = $detalhesResgate['taxaIr'];
                $campos['descontoIof'] = $detalhesResgate['descontoIof'];
                $campos['descontoIr'] = $detalhesResgate['descontoIr'];
                $campos['descontoAdmin'] = $detalhesResgate['descontoAdmin'];

            }else if( strpos($tipoPapel, 'RENDA VAR') !== false ){
                
                //precisa mudar esses dados porque vai pra tabela de resgates
                $campos['valor'] = $cotacao;
                $campos['qtde'] = $dadosAporte[0]->qtde;
                $campos['subTotal'] = $cotacao * $dadosAporte[0]->qtde;

                $paramsCalculo = [
                    'cotacao'=>$cotacao,
                    'qtde'=>$dadosAporte[0]->qtde,
                    'capital'=>$dadosAporte[0]->subTotal,
                    'dtAporte'=>$dadosAporte[0]->dtAporte,
                    'taxaAdmin'=>$dadosAporte[0]->taxaAdmin,
                    'taxaIr'=>$dadosPapel[0]->taxaIr,
                    'aplicarIr'=>true
                ];

                $detalhesResgate = RENT::calculoRentabilidadeRendaVar($paramsCalculo,true);
                
                $campos['capitalInicial'] = $detalhesResgate['capitalInicial'];
                $campos['diasCorridos'] = $detalhesResgate['diasCorridos'];
                $campos['montanteBruto'] = $detalhesResgate['montanteBruto'];
                $campos['montanteLiquido'] = $detalhesResgate['montanteLiquido'];
                $campos['lucroBruto'] = $detalhesResgate['lucroBruto'];
                $campos['lucroLiquido'] = $detalhesResgate['lucroLiquido'];
                $campos['taxaIof'] = $detalhesResgate['taxaIof'];
                $campos['taxaIr'] = $detalhesResgate['taxaIr'];
                $campos['descontoIof'] = $detalhesResgate['descontoIof'];
                $campos['descontoIr'] = $detalhesResgate['descontoIr'];
                $campos['descontoAdmin'] = $detalhesResgate['descontoAdmin'];

            }else{
                
                $paramsCalculo = [
                    'capital'=>$dadosAporte[0]->subTotal,
                    'dtAporte'=>$dadosAporte[0]->dtAporte,
                    'taxaRetorno'=>$dadosAporte[0]->taxaRetorno,
                    'taxaAdmin'=>$dadosAporte[0]->taxaAdmin,
                    'aplicarIr'=>true
                ];
                
                $detalhesResgate = RENT::calculoRentabilidade($paramsCalculo,true);
                
                $campos['capitalInicial'] = $detalhesResgate['capitalInicial'];
                $campos['diasCorridos'] = $detalhesResgate['diasCorridos'];
                $campos['montanteBruto'] = $detalhesResgate['montanteBruto'];
                $campos['montanteLiquido'] = $detalhesResgate['montanteLiquido'];
                $campos['lucroBruto'] = $detalhesResgate['lucroBruto'];
                $campos['lucroLiquido'] = $detalhesResgate['lucroLiquido'];
                $campos['taxaIof'] = $detalhesResgate['taxaIof'];
                $campos['taxaIr'] = $detalhesResgate['taxaIr'];
                $campos['descontoIof'] = $detalhesResgate['descontoIof'];
                $campos['descontoIr'] = $detalhesResgate['descontoIr'];
                $campos['descontoAdmin'] = $detalhesResgate['descontoAdmin'];

            }
            
            
            $campos['taxaRetorno'] = $dadosAporte[0]->taxaRetorno;
            $campos['taxaAdmin'] = $dadosAporte[0]->taxaAdmin;
            $campos['dtResgate'] = date('Y-m-d H:d:s');
            $campos['cdUsuario'] = $dadosAporte[0]->cdUsuario;
        
            $acao = CRUD_DB::salvar(['tabela'=>'resgates','dados'=>$campos]);
            
            $cdStatus = 2;

        }else{

            $acao = CRUD_DB::deletar([
                'tabela'=>'resgates',
                'campo'=>'cdAporte',
                'valor'=>$dadosAporte[0]->cdAporte
            ]);
            
            $cdStatus = 1;
        }
        
        $acaoAlteraStatus = CRUD_DB::alterar([
            'tabela'=>'aportes',
            'campo'=>'cdAporte',
            'valor'=>$dadosAporte[0]->cdAporte,
            'valores'=>['cdStatus'=>$cdStatus]
        ]);

        if( $redirect ){
            return redirect('resgate');
        }else{
            return true;
        }


    }

    public function exportar(Request $request,$tipoRelatorio = "excel"){
        
        $data = $this->pesquisar($request, true);
        $data['flagRelatorio'] = true;
        
        $htmlRelatorio = view('tabelas.resgates')->with(['data'=>$data])->render();
        
        $this->expData->setHtml($htmlRelatorio);

        if( $tipoRelatorio == "excel" ){
            $this->expData->output(['html'=>0]);
        }else{
            $this->expData->output(['html'=>1]);
        }

    }

}