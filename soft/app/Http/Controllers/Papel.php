<?php
namespace App\Http\Controllers;

use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Papel as PAP;
use App\Http\Repositories\Cotacao as COT;
use App\Http\Repositories\RedisCache as RC;
use Illuminate\Http\Request;
use DB;

class Papel{
    public function __construct(){

    }

    public function index(){

        $papeis = [
            'listagem'=>PAP::getContagemPapeis()
        ];

        return view('papel.index')->with(['data'=>$papeis]);
        
    }

    public function pesquisar( Request $request ){
        $params = $request->all();

        $papeis = [
            'listagem'=>PAP::getContagemPapeis($params)
        ];
        
        return view('papel.index')->with(['data'=>$papeis]);
        
    }

    public function salvar( Request $request ){
        //resgata requisição
        $params = $request->all();
             
        //seta os campos previamente
        $campos = [
            'nmPapel'=>$params['papel'],
            'cdTipo'=>$params['tipo'],
            'subTipo'=>$params['subTipo'],
            'taxaIr'=>$params['taxaIr'],
            'cdUsuario'=>session()->get('autenticado.id_user')
        ];
        
        //se vir nome antigo é pra editar, se não se trata de um novo registro
        if( isset($params['nomeAntigo']) && !is_null($params['nomeAntigo']) ){

            //captura identificador
            $dadosPapel = DB::table('papel')
                ->select('cdPapel')
                ->where('nmPapel',$params['nomeAntigo'])
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->get();

            //verifica se o novo nome já nõ existe para o usuário logado
            $dadosNovoNome = DB::table('papel')
                ->select('cdPapel')
                ->where('nmPapel',$params['papel'])
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->get();
            
            if( $params['papel'] != $params['nomeAntigo'] ){
                
                if( count($dadosPapel) && !count($dadosNovoNome) ){
                    $acao = CRUD_DB::alterar([
                        'tabela'=>'papel',
                        'campo'=>'cdPapel',
                        'valor'=>$dadosPapel[0]->cdPapel,
                        'valores'=>$campos
                    ]);
                }else{
                    $acao = 0;
                }

            }else{
                
                $acao = CRUD_DB::alterar([
                    'tabela'=>'papel',
                    'campo'=>'cdPapel',
                    'valor'=>$dadosPapel[0]->cdPapel,
                    'valores'=>$campos
                ]);

            }

        }else{
            
            //captura identificador
            $dadosPapel = DB::table('papel')
                ->select('cdPapel')
                ->where('nmPapel',$params['papel'])
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->get();
            
            if( !count($dadosPapel) ){
                $acao = CRUD_DB::salvar(['tabela'=>'papel','dados'=>$campos]);
            }else{
                $acao = 0;
            }
        }

        //redireciona com status
        if( $acao > 0 ){
            $msg = "Papel Salvo com sucesso!|success";
        }else{
            $msg = "Não foi possível salvar o papel, papel já existe ou não foi localizado...|danger";
        }

        return redirect('papel')->with('status',$msg);

    }

    public function deletar( $id ){
        
        $dados = [
            'tabela'=>'papel',
            'campo'=>'cdPapel',
            'valor'=>$id
        ];

        $acao = CRUD_DB::deletar($dados);

        if( $acao > 0 ){
            $msg = "Papel deletado com sucesso!|success";
        }else{
            $msg = "Não foi possível deletar o papel...|danger";
        }

        return redirect('papel')->with('status',$msg);

    }

    public function atualizaCotacoes(Request $request){
        
        //pega no Redis quando foi a última atualização
        $idUsuario                      = session()->get('autenticado.id_user');
        $chaveRedisUltimaAtualizacao    = "usuario_".$idUsuario.".tempo_atualiza_cotacoes";
        
        $redisUltimaAtualizacao = RC::redisGet($chaveRedisUltimaAtualizacao);
        
        if( !empty($redisUltimaAtualizacao) && !is_null($redisUltimaAtualizacao) ){
            $tempoUltimaAtualizacao = $redisUltimaAtualizacao;
        }else{
            $tempoUltimaAtualizacao = time();
        }

        $dataUltimaAtualizacao = date('d-m-Y H:i:s',$tempoUltimaAtualizacao);
        
        if( time() - $tempoUltimaAtualizacao < (int) getenv('INTERVALO_ATUALIZA_COTACOES') ){
            return json_encode(['msg'=>'Última atualização realizada: '.$dataUltimaAtualizacao]);
        }
        
        RC::redisSet($chaveRedisUltimaAtualizacao,time());

        //recupera parametros do request e busca cotações
        $params = $request->all();
        
        $papeis = DB::table('papel')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->get();
 
        //percorre os papeis atualizando suas cotações
        $cotacoes = [];

        for( $i=0; $i<count($papeis); $i++ ){
            
            $tipoPapel = session()->get('autenticado.tipo_papel')[$papeis[$i]->cdTipo];

            if( strpos($tipoPapel,"RENDA VAR") !== false ){
            
                $nomeAtivo = $papeis[$i]->nmPapel;

                //retira o F do final, pro caso de ações fracionadas
                if( strtoupper( substr($nomeAtivo,strlen($nomeAtivo) - 1) ) == 'F' ){
                    $nomeAtivo = substr($papeis[$i]->nmPapel,0,strlen($nomeAtivo) - 1);
                }

                //caso papel seja uma ação
                if( $papeis[$i]->subTipo == 3 ){
                    $dadosCotacao = COT::getCotacao( $nomeAtivo );
                //caso papel seja um fundo imobiliario
                }else if( $papeis[$i]->subTipo == 2 ){
                    $dadosCotacao = COT::getCotacaoFII( $nomeAtivo );
                }

                /**
                 * @see
                 * Necessário verificar se o valor da cotação é zero, se for zerado 
                 * não deve atualizar
                */

                if( $dadosCotacao['valor'] > 0 ){
                    
                    $cotacoes[] = ['ativo'=>$papeis[$i]->nmPapel,'cotacao'=>$dadosCotacao];

                    $acao = CRUD_DB::alterar([
                        'tabela'=>'papel',
                        'campo'=>'cdPapel',
                        'valor'=>$papeis[$i]->cdPapel,
                        'valores'=>['cotacao'=>$dadosCotacao['valor'] ]
                    ]);

                }else{
                    $cotacoes[] = ['ativo'=>$papeis[$i]->nmPapel,'cotacao'=>['valor'=>$papeis[$i]->cotacao] ];
                }

            }

        }

        return json_encode(['msg'=>'Cotações atualizadas com sucesso...']);
    }

    /**
     * @description Função criada para dar flexibilidade a tela de monitoramento de Alvos
     * uma vez que a API HG Brasil pode retornar dados errados
     * @param int $papel
     * @param float $cotacao
     * @return json retorna apenas status da operação
    */
    public function atualizaCotacaoManual($papel,$cotacao){

        $acao = CRUD_DB::alterar([
            'tabela'=>'papel',
            'campo'=>'cdPapel',
            'valor'=>$papel,
            'valores'=>['cotacao'=>$cotacao ]
        ]);

        if( $acao > 0 ){
            return json_encode(['success'=>true]);
        }else{
            return json_encode(['success'=>false]);
        }

    }

    /**
     * @description recebe o codigo do papel e calcula seu preço médio de aportes
     * @param int $cdPapel
     * @return float $precoMedio
    */
    public function precoMedioPapel($cdPapel){
        return PAP::getPrecoMedioPapel($cdPapel);
    }

}