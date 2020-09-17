<?php
namespace App\Http\Repositories;

use DB;
use Exception;
use App\Http\Repositories\Rentabilidade as RENT;

class Movimento{
    
    public function __construct(){

    }
    
    /**
     * 
     * @description Recupera todos os aportes feitos pelo usuário e para todos, calcula a rentabilidade
     * atual de acordo com o tipo de ativo
     * @return array $aportes
     * 
    */
    public static function getAportes($params=[]){

        $qtdePaginas = ( isset($params['paginate']) && !is_null($params['paginate']) ) ? $params['paginate'] : 10000;

        $aportes = DB::table('aportes as a')
            ->select(
                'a.cdAporte',
                'a.cdPapel',
                'pp.nmPapel',
                'pp.cotacao',
                'pp.cdTipo',
                'pp.subTipo',
                'pp.taxaIr',
                'a.valor',
                'a.qtde',
                'a.subTotal',
                'a.dtAporte',
                'a.cdStatus',
                DB::raw('if(a.cdStatus = 1,"success","secondary") as classeStatus'),
                DB::raw('if(a.cdStatus = 1,"Resgatar","Cancelar") as statusResgate'),
                'a.taxaRetorno',
                'a.taxaAdmin'
            )
            ->join('papel as pp','a.cdPapel','=','pp.cdPapel');
        
        if( count($params) ) {
            
            if( isset($params['papel']) && !empty($params['papel']) ){
                $aportes = $aportes->where('pp.cdPapel',$params['papel']);
            }

            if( isset($params['dataInicio']) && !empty($params['dataInicio']) && isset($params['dataFinal']) && !empty($params['dataFinal']) ){
                $params['dataInicio']   = date('Y-m-d',strtotime($params['dataInicio'])).' 00:00:00';
                $params['dataFinal']    = date('Y-m-d',strtotime($params['dataFinal'])).' 23:59:59';
                
                $aportes = $aportes->whereBetween('dtAporte',[$params['dataInicio'],$params['dataFinal']]);
            }

            if( isset($params['tipo']) && !empty($params['tipo']) ){
                $aportes = $aportes->where('pp.cdTipo',$params['tipo']);
            }

            if( isset($params['subTipo']) && !empty($params['subTipo']) ){
                $aportes = $aportes->where('pp.subTipo',$params['subTipo']);
            }

            if( isset($params['status']) && !empty($params['status']) ){
                $aportes = $aportes->where('a.cdStatus',$params['status']);
            }

        }


        $aportes = $aportes->where('pp.cdUsuario',session()->get('autenticado.id_user'))
            ->where('a.cdUsuario',session()->get('autenticado.id_user'))
            ->orderBy('pp.cdTipo','asc')
            ->orderBy('pp.cdPapel','asc')
            ->orderBy('a.dtAporte','asc')
            ->paginate($qtdePaginas);
            
        foreach( $aportes as $num => $val ){
            $tipoPapel = session()->get('autenticado.tipo_papel')[$val->cdTipo];
            
            if( strpos($tipoPapel,"RENDA FIX") !== false ){
                $montante = RENT::calculoRentabilidade([
                    'capital'=>$val->subTotal,
                    'dtAporte'=>$val->dtAporte,
                    'taxaRetorno'=>$val->taxaRetorno,
                    'taxaAdmin'=>$val->taxaAdmin
                ]);
                
                $rentabilidade = $montante - $val->subTotal;
                $aportes[$num]->cotacao = $val->valor;
                $aportes[$num]->montante        = $montante;
                $aportes[$num]->rentabilidade   = $rentabilidade;
            
            }else if( strpos($tipoPapel,"RENDA VAR") !== false ){
                
                $nomeAtivo = substr($val->nmPapel,0,5);

                //verifica se deve pegar cotações ou dados do resgate
                if( $val->cdStatus == 1 ){
                    $aportes[$num]->cotacao = $val->cotacao;
                    $valorMontante      = $val->qtde * (float) $val->cotacao;
                    $valorRentabilidade = $valorMontante - $val->subTotal;

                    $aportes[$num]->montante        = $valorMontante;
                    $aportes[$num]->rentabilidade   = $valorRentabilidade;
                }else{
                    
                    $dadosResgate = DB::table('resgates')
                        ->select()
                        ->where('cdUsuario',session()->get('autenticado.id_user'))
                        ->where('cdAporte',$val->cdAporte)
                        ->get();

                    if( count($dadosResgate) ){
                        $aportes[$num]->cotacao = $dadosResgate[0]->valor;
                        $valorMontante      = $val->qtde * (float) $dadosResgate[0]->valor;
                        $valorRentabilidade = $valorMontante - $val->subTotal;

                        $aportes[$num]->montante        = $valorMontante;
                        $aportes[$num]->rentabilidade   = $valorRentabilidade;
                    }else{
                        $aportes[$num]->cotacao = $val->cotacao;
                        $valorMontante      = $val->qtde * (float) $val->cotacao;
                        $valorRentabilidade = $valorMontante - $val->subTotal;

                        $aportes[$num]->montante        = $valorMontante;
                        $aportes[$num]->rentabilidade   = $valorRentabilidade;
                    }
                }
                
            }else if( strpos($tipoPapel,"HEDGE") !== false ){
                
                $montante = RENT::calculoRentabilidade([
                    'capital'=>$val->subTotal,
                    'dtAporte'=>$val->dtAporte,
                    'taxaRetorno'=>$val->taxaRetorno,
                    'taxaAdmin'=>$val->taxaAdmin
                ]);
                
                $rentabilidade = $montante - $val->subTotal;
                $aportes[$num]->cotacao = $val->valor;
                $aportes[$num]->montante        = $montante;
                $aportes[$num]->rentabilidade   = $rentabilidade;

            }else{

                $aportes[$num]->cotacao         = 0.00;
                $aportes[$num]->montante        = 0.00;
                $aportes[$num]->rentabilidade   = 0.00;

            }

        }

        return $aportes;
    }

    public static function getProventos($params=[]){
        
        //inicia busca
        $proventos = DB::table('proventos as p')
            ->select(
                'p.cdProvento',
                'p.cdPapel',
                'pp.nmPapel',
                'pp.subTipo',
                'p.cdTipo',
                'p.valor',
                'p.qtde',
                'p.subTotal',
                'p.dtProvento'
            )
            ->join('papel as pp','p.cdPapel','=','pp.cdPapel')
            ->where('p.cdUsuario',session()->get('autenticado.id_user'));

        //continua com filtros
        if( count($params) ){
            
            if( isset($params['papeis']) && !empty($params['papeis']) && is_array($params['papeis']) ){
                $proventos  = $proventos->whereIn('p.cdPapel',$params['papeis']);
            }

            if( isset($params['tipo']) && !empty($params['tipo']) ){
                $proventos = $proventos->where('p.cdTipo',$params['tipo']);
            }

            if( isset($params['subTipo']) && !empty($params['subTipo']) ){
                $proventos = $proventos->where('pp.subTipo',$params['subTipo']);
            }

            if( isset($params['dataInicio']) && !empty($params['dataInicio']) && isset($params['dataFinal']) && !empty($params['dataFinal']) ){
                $params['dataInicio']   = date('Y-m-d 00:00:00',strtotime($params['dataInicio']));
                $params['dataFinal']    = date('Y-m-d 23:59:59',strtotime($params['dataFinal']));
                
                $proventos = $proventos->whereBetween('p.dtProvento',[$params['dataInicio'],$params['dataFinal']]);
            }

        }

        //finaliza
        $proventos = $proventos->get();

        return $proventos;
    }

    /**
     * @author Fernando Bino
     * @description soma total de proventos recebidos pelo usuario
     * @return float $total
    */
    public static function getTotalProventos($params=[]){
        //inicia busca
        $total = DB::table('proventos as p')
            ->select('p.subTotal')
            ->join('papel as pp','p.cdPapel','=','pp.cdPapel')
            ->where('p.cdUsuario',session()->get('autenticado.id_user'));


        //continua com filtros
        if( count($params) ){
            
            if( isset($params['papeis']) && !empty($params['papeis']) && is_array($params['papeis']) ){
                $total = $total->whereIn('p.cdPapel',$params['papeis']);
            }

            if( isset($params['tipo']) && !empty($params['tipo']) ){
                $total = $total->where('p.cdTipo',$params['tipo']);
            }

            if( isset($params['subTipo']) && !empty($params['subTipo']) ){
                $total = $total->where('pp.subTipo',$params['subTipo']);
            }

            if( isset($params['dataInicio']) && !empty($params['dataInicio']) && isset($params['dataFinal']) && !empty($params['dataFinal']) ){
                $params['dataInicio']   = date('Y-m-d 00:00:00',strtotime($params['dataInicio']));
                $params['dataFinal']    = date('Y-m-d 23:59:59',strtotime($params['dataFinal']));
                
                $total = $total->whereBetween('p.dtProvento',[$params['dataInicio'],$params['dataFinal']]);
            }

        }

        //finaliza soma
        $total = $total->sum('p.subTotal');

        return $total;
    }

    /**
     * @author Fernando Bino
     * @description soma todos os aportes feitos pelo usuário logado
     * @return float $soma
    */
    public static function getSomaAportesUsuario(){

        $soma = DB::table('aportes')
            ->select('subTotal','cdUsuario')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdStatus',1)
            ->sum('subTotal');

        return $soma;

    }

    /**
     * @author Fernando Bino
     * @description soma total de aportes inativos, isto são aportes já resgatados pelo usuario
     * @return float $soma
    */
    public static function getSomaAportesInativos(){
        $soma = DB::table('aportes')
            ->select('subTotal','cdUsuario')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdStatus',2)
            ->sum('subTotal');

        return $soma;
    }

    /**
     * @author Fernando Bino
     * @description soma toda a rentabilidade geral do que foi aplicado pelo usuário e cujo aportes ainda não
     * foram resgatados
     * @return float $soma
    */
    public static function getRendimentosUsuario($detalhes = false){
        $aportes = DB::table('aportes as a')
            ->select(
                'a.cdAporte',
                'a.qtde',
                'a.subTotal',
                'a.dtAporte',
                'a.taxaRetorno',
                'a.taxaAdmin',
                'p.cdTipo',
                'p.nmPapel',
                'p.cotacao'
            )
            ->join('papel as p','a.cdPapel','=','p.cdPapel')
            ->where('a.cdUsuario',session()->get('autenticado.id_user'))
            ->where('a.cdStatus',1)
            ->get();

        $soma = 0;

        for( $i=0; $i<count($aportes); $i++ ){
         
            $tipoPapel = session()->get('autenticado.tipo_papel')[$aportes[$i]->cdTipo];
            
            if( strpos($tipoPapel,"RENDA FIX") !== false ){

                $detalhesRendimento = RENT::calculoRentabilidade([
                    'capital'=>$aportes[$i]->subTotal,
                    'dtAporte'=>$aportes[$i]->dtAporte,
                    'taxaRetorno'=>$aportes[$i]->taxaRetorno,
                    'taxaAdmin'=>$aportes[$i]->taxaAdmin
                ],true);
                
                $aportes[$i]->montanteLiquido = $detalhesRendimento['montanteLiquido'];

                $soma += $detalhesRendimento['montanteLiquido'];

            }else if( strpos($tipoPapel,"RENDA VAR") !== false ){
                $aportes[$i]->montanteLiquido = $aportes[$i]->qtde * $aportes[$i]->cotacao;
                $soma += ($aportes[$i]->qtde * $aportes[$i]->cotacao);
            
            }else if( strpos($tipoPapel,"HEDGE") !== false ){
            
                $detalhesRendimento = RENT::calculoRentabilidade([
                    'capital'=>$aportes[$i]->subTotal,
                    'dtAporte'=>$aportes[$i]->dtAporte,
                    'taxaRetorno'=>$aportes[$i]->taxaRetorno,
                    'taxaAdmin'=>$aportes[$i]->taxaAdmin
                ],true);
                
                $aportes[$i]->montanteLiquido = $detalhesRendimento['montanteLiquido'];

                $soma += $detalhesRendimento['montanteLiquido'];

            }else{
                $aportes[$i]->montanteLiquido = $aportes[$i]->subTotal;
                $soma += $aportes[$i]->subTotal;
                
            }
            

        }

        if( $detalhes ){
            return $aportes;
        }else{
            return $soma;
        }

    }

    /**
     * @author Fernando Bino
     * @description soma todos os resgates feitos pelo usuário
     * @return float $soma
    */
    public static function getSomaResgatesUsuario(){

        $soma = DB::table('resgates')
            ->select('montanteLiquido')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->sum('montanteLiquido');

        return $soma;

    }

    /**
     * @author Fernando Bino
     * @description soma todos os proventos recebidos pelo usuário
     * @return float $soma
    */
    public static function getSomaProventosUsuario(){

        $soma = DB::table('proventos')
            ->select('subTotal')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->sum('subTotal');

        return $soma;

    }



}