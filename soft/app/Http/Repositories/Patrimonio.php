<?php
namespace App\Http\Repositories;

use DB;
use Exception;
use App\Http\Repositories\Caixa as CX;
use App\Http\Repositories\Cotacao as COT;
use App\Http\Repositories\Rentabilidade as RENT;
use App\Http\Repositories\RedisCache as RC;

/**
 * @author Fernando Bino
 * @description classe que fornece dados agrupados
 *      agrupamento por tipo de ativo
 *          esse agrupamento traz a posição de retorno atual de cada tipo de ativo, o quanto esse tipo está
 *          rendendo no momento com base na última atualização das cotações
 *          @example Tipo Renda Fixa 5.876,67
 * 
 *      agrupamento por papel
 *          o agrupamento traz dados referentes a quanto foi aportado, 
 *          qual valor de retorno e qual lucro
 *          @example papel ITSA4F aportado 1.000,00 retorno 1.150,00 lucro 150,00
*/

class Patrimonio{
    
    public function __construct(){

    }

    /**
     * @author Fernando Bino
     * @description busca ativos do usuário, percorre todos calculando a rentabilidade e 
     *  agrupando e somando por tipo de ativo
     * @param array $params array para futuros filtros, ainda não está implementado
     * @param string $ordem parametro que define a ordenação bem como agrupamento, para evitar duplicar essa função grande
     * @return array $agrupamentoFinal
    */
    public static function getContagemTiposAtivos($params = [], $ordem = 'cdTipo'){
        
        //busca ativos do usuario
        $dados = DB::table('papel as p')
            ->select(
                'p.cdTipo',
                'p.subTipo',
                'a.cdPapel',
                'a.qtde',
                'a.subTotal',
                'a.dtAporte',
                'a.taxaRetorno',
                'a.taxaAdmin'
            )
            ->leftJoin('aportes as a','p.cdPapel','=','a.cdPapel')
            ->where('a.cdUsuario',session()->get('autenticado.id_user'))
            ->where('a.cdStatus',1)
            ->orderBy('p.'.$ordem)
            ->get();

        //percorre calculando rentabilidade
        $agrupamento = [];

        for( $i=0; $i<count($dados); $i++ ){
            
            $tipoPapel = session()->get('autenticado.tipo_papel')[$dados[$i]->cdTipo];
            
            if( strpos($tipoPapel,"RENDA FIX") !== false ){
                $rentabilidade = RENT::calculoRentabilidade([
                    'capital'=>$dados[$i]->subTotal,
                    'dtAporte'=>$dados[$i]->dtAporte,
                    'taxaRetorno'=>$dados[$i]->taxaRetorno,
                    'taxaAdmin'=>$dados[$i]->taxaAdmin
                ]);
            }else if( strpos($tipoPapel,"RENDA VAR") !== false ){
                
                $dadosCotacao = DB::table('papel')
                    ->select()
                    ->where('cdUsuario',session()->get('autenticado.id_user'))
                    ->where('cdPapel',$dados[$i]->cdPapel)
                    ->get();
                
                if( count($dadosCotacao) ){
                    $rentabilidade = $dados[$i]->qtde * $dadosCotacao[0]->cotacao;
                }else{
                    $rentabilidade = $dados[$i]->subTotal;
                }
                
            }else if( strpos($tipoPapel,"HEDGE") !== false ){
                $rentabilidade = RENT::calculoRentabilidade([
                    'capital'=>$dados[$i]->subTotal,
                    'dtAporte'=>$dados[$i]->dtAporte,
                    'taxaRetorno'=>$dados[$i]->taxaRetorno,
                    'taxaAdmin'=>$dados[$i]->taxaAdmin
                ]);
            }else{
                $rentabilidade = $dados[$i]->subTotal;
            }

            $dados[$i]->montanteLiquido = $rentabilidade;

            $chaveAgrupamento = $ordem == 'cdTipo' ? $dados[$i]->cdTipo : $dados[$i]->subTipo;
            
            $agrupamento[ $chaveAgrupamento ][] = $dados[$i];
        }

        //percorre agrupando e somando
        $agrupamentoFinal = [];
        
        foreach( $agrupamento as $num => $val ){
            $rentabilidadeTipo = 0;            

            for($i=0;$i<count($val);$i++){
                $rentabilidadeTipo += $val[$i]->montanteLiquido;
            }
            
            $agrupamento[$num]['totalTipo'] = $rentabilidadeTipo;

            if( $ordem == 'cdTipo' ){
                $agrupamento[$num]['nomeTipo']  = unserialize(RC::redisGet('tipo_papel'))[$num];
            }else{
                $agrupamento[$num]['nomeTipo']  = unserialize(RC::redisGet('sub_tipo_papel'))[$num];
            }

            $agrupamentoFinal[] = [
                'nomeTipo'=>$agrupamento[$num]['nomeTipo'],
                'totalTipo'=>$agrupamento[$num]['totalTipo']
            ];
        }
        
        $agrupamentoFinal[] = [
            'nomeTipo' => 'SALDO',
            'totalTipo' => CX::getSaldo([],true)
        ];
        
        return $agrupamentoFinal;

    }

    /**
     * @author Fernando Bino
     * @description busca ativos do usuário, percorre todos calculando a rentabilidade e 
     *  agrupando e somando por papel
     * @return array $agrupamentoFinal
    */
    public static function getContagemPapeis($params = []){

        //pega os papeis
        $papeis = DB::table('papel')
            ->select('cdPapel','nmPapel','cdTipo')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->orderBy('cdTipo','asc')
            ->orderBy('nmPapel','asc')
            ->get();

        $agrupamentoFinal = [];

        $totalGeralAplicado         = 0;
        $totalGeralRentabilidade    = 0;
        $totalGeralLucro            = 0;

        //pra cada papel vai buscar seus aportes e agrupa 
        foreach( $papeis as $num => $val ){
            $aportes = DB::table('aportes')
                ->select(
                    'cdPapel',
                    'qtde',
                    'subTotal',
                    'dtAporte',
                    'taxaRetorno',
                    'taxaAdmin',
                    'cdStatus'
                )
                ->where('cdPapel',$val->cdPapel)
                ->where('cdStatus',1)
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->get();

            $rentabilidadePapel = 0;
            $totalAplicadoPapel = 0;

            //pra cada aporte calcula rentabilidade e soma total aplicado,retorno,lucro
            for( $i=0; $i<count($aportes); $i++ ){
                $tipoPapel = session()->get('autenticado.tipo_papel')[$val->cdTipo];
                
                if( strpos($tipoPapel,"RENDA FIX") !== false ){

                    $rentabilidade = RENT::calculoRentabilidade([
                        'capital'=>$aportes[$i]->subTotal,
                        'dtAporte'=>$aportes[$i]->dtAporte,
                        'taxaRetorno'=>$aportes[$i]->taxaRetorno,
                        'taxaAdmin'=>$aportes[$i]->taxaAdmin
                    ]);

                }else if( strpos($tipoPapel,"RENDA VAR") !== false ){
                    
                    $dadosCotacao = DB::table('papel')
                        ->select('cotacao')
                        ->where('cdUsuario',session()->get('autenticado.id_user'))
                        ->where('cdPapel',$aportes[$i]->cdPapel)
                        ->get();

                    if( count($dadosCotacao) ){
                        $rentabilidade = $aportes[$i]->qtde * $dadosCotacao[0]->cotacao;
                    }else{
                        $rentabilidade = $aportes[$i]->subTotal;
                    }

                }else if( strpos($tipoPapel,"HEDGE") !== false ){
                    
                    $rentabilidade = RENT::calculoRentabilidade([
                        'capital'=>$aportes[$i]->subTotal,
                        'dtAporte'=>$aportes[$i]->dtAporte,
                        'taxaRetorno'=>$aportes[$i]->taxaRetorno,
                        'taxaAdmin'=>$aportes[$i]->taxaAdmin
                    ]);

                }else{
                    $rentabilidade = $aportes[$i]->subTotal;
                }               

                $rentabilidadePapel += $rentabilidade;
                $totalAplicadoPapel += $aportes[$i]->subTotal;

            }

            $agrupamentoFinal[] = [
                'nomePapel'=>$val->nmPapel,
                'aplicado'=>$totalAplicadoPapel,
                'retorno'=>$rentabilidadePapel,
                'lucro'=> $rentabilidadePapel - $totalAplicadoPapel
            ];
        }

        $agrupamentoFinal[] = [
            'nomePapel' => 'SALDO',
            'aplicado' => CX::getSaldo([],true),
            'retorno' => CX::getSaldo([],true),
            'lucro' => CX::getSaldo([],true)
        ];

        //percorre agrupamento final setando apenas papeis que contenham ao menos um dos totais
        for($i=0;$i<count($agrupamentoFinal);$i++){
            if( !( $agrupamentoFinal[$i]['aplicado'] != 0 || $agrupamentoFinal[$i]['retorno'] != 0 || $agrupamentoFinal[$i]['lucro'] != 0 ) ){
                $agrupamentoFinal[$i]['exibir'] = 0;
            }else{
                $agrupamentoFinal[$i]['exibir'] = 1;
            }
        }

        return $agrupamentoFinal;
    }



}