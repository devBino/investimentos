<?php
namespace App\Http\Repositories;

use DB;
use Exception;

/**
 * @author Fernando Bino
 * @description Classe para reaproveitar funções referentes aos papeis
 * bem como contagem de aportes por papel, proventos por papel, resgate por papel
*/

class Papel{
    
    public function __construct(){
        
    }

    public static function getPapeis($params=[]){
        $papeis = DB::table('papel')
            ->select(
                'cdPapel',
                'nmPapel',
                'cotacao',
                'cdTipo',
                'subTipo',
                DB::raw('0 as cotas')
            )
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->get();
        
        for( $i=0; $i<count($papeis); $i++ ){
            $papeis[$i]->cotas = self::getQuantidadeCotasPapel(
                $papeis[$i]->cdPapel, self::getUltimoAporte($papeis[$i]->cdPapel)
            );
        }

        return $papeis;
    }

    public static function getPapel($cdPapel){

        $papel = DB::table('papel')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdPapel',$cdPapel)
            ->get();

        return $papel;

    }

    public static function getPapeisRendaVariavel(){

        $papeis = DB::table('papel')
            ->select(
                'cdPapel',
                'nmPapel',
                'cotacao',
                'cdTipo',
                'subTipo',
                'taxaIr',
                'cdUsuario',
                DB::raw('0 as cotas')
            )
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdTipo',2)
            ->orderBy('cdTipo','asc')
            ->orderBy('subTipo','asc')
            ->orderBy('nmPapel','asc')
            ->get();

        

        for( $i=0; $i<count($papeis); $i++ ){
            $papeis[$i]->cotas = self::getQuantidadeCotasPapel(
                $papeis[$i]->cdPapel, self::getPrimeiroAporteAtivo($papeis[$i]->cdPapel)
            );
        }

        return $papeis;
    }

    public static function getPrimeiroAporteAtivo($cdPapel){
        
        $aporte = DB::table('aportes')
            ->select('valor','dtAporte')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdPapel',$cdPapel)
            ->where('cdStatus',1)
            ->orderBy('dtAporte','asc')
            ->limit(1)
            ->get();

        return $aporte;

    }

    public static function getUltimoAporte($cdPapel){
        
        $aporte = DB::table('aportes')
            ->select('valor','dtAporte')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdPapel',$cdPapel)
            ->where('cdStatus',1)
            ->orderBy('dtAporte','desc')
            ->limit(1)
            ->get();

        return $aporte;

    }

    public static function getQuantidadeCotasPapel($cdPapel, $dadosUltimoAporte){
        
        if( !count($dadosUltimoAporte) ){
            return 0;
        }
        
        $quantidade = DB::table('aportes')
            ->select('qtde')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdPapel',$cdPapel)
            ->where('cdStatus',1)
            ->where('dtAporte','>=',$dadosUltimoAporte[0]->dtAporte)
            ->sum('qtde');
        
        return $quantidade;

    }

    public static function getAportesPapel($cdPapel){
        $aportes = DB::table('aportes')
            ->select(
                DB::raw('count(cdPapel) as qtdeAportes'),
                DB::raw('sum(subTotal) as totalAportado')
            )
            ->where('cdPapel',$cdPapel)
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->get();

        return $aportes;
    }

    public static function getPrecoMedioPapel($cdPapel){
        $aportes = DB::table('aportes')
            ->select(
                DB::raw('sum(qtde) as Qtde'),
                DB::raw('sum(subTotal) as Total')
            )
            ->where('cdPapel',$cdPapel)
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdStatus',1)
            ->get();

        if( count($aportes) ){
            if( $aportes[0]->Total > 0 && $aportes[0]->Qtde > 0){
                return $aportes[0]->Total / $aportes[0]->Qtde;
            }else{
                return 0.00;
            }
        }else{
            return 0.00;
        }
    }

    public static function getResgatesPapel($cdPapel){
        $resgates = DB::table('resgates')
            ->select(
                DB::raw('count(cdPapel) as qtdeResgates'),
                DB::raw('sum(montanteLiquido) as totalResgatado')
            )
            ->where('cdPapel',$cdPapel)
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->get();

        return $resgates;
    }

    public static function getRendimentosPapel($cdPapel){

        $rendimentos = DB::table('resgates')
            ->select(
                DB::raw('sum(capitalInicial) as aportes'),
                DB::raw('sum(montanteLiquido) as resgates'),
                DB::raw('sum(lucroLiquido) as lucro'),
                DB::raw('sum(descontoIr) as descontoIr')
            )
            ->where('cdPapel',$cdPapel)
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->get();
        
        return $rendimentos;

    }

    public static function getProventosPapel($cdPapel){
        $proventos = DB::table('proventos')
            ->select(
                DB::raw('count(cdPapel) as qtdeProventos'),
                DB::raw('sum(subTotal) as totalProventos')
            )
            ->where('cdPapel',$cdPapel)
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->get();

        return $proventos;
    }

 
    public static function getResgatePapelMes($ano,$mes,$papel){
 
        $totalResgates = DB::table('resgates')
                    ->select('subTotal','dtResgate')
                    ->where('cdPapel',$papel)
                    ->whereYear('dtResgate',$ano)
                    ->whereMonth('dtResgate',$mes)
                    ->sum('subTotal');

        return (float) $totalResgates;

    }

    public static function getQtdeResgatePapelMes($ano,$mes,$papel){

        $qtdeResgates = DB::table('resgates')
                    ->select('qtde','dtResgate')
                    ->where('cdPapel',$papel)
                    ->whereYear('dtResgate',$ano)
                    ->whereMonth('dtResgate',$mes)
                    ->sum('qtde');
        
        return (int) $qtdeResgates;

    }

    public static function getMediaResgatePapelMes($ano,$mes,$papel){
        
        $mediaResgates = DB::table('resgates')
                    ->select('valor')
                    ->where('cdPapel',$papel)
                    ->whereYear('dtResgate',$ano)
                    ->whereMonth('dtResgate',$mes)
                    ->avg('valor');
                    
        return (float) $mediaResgates;
        
    }

    public static function getValorUnitarioProventoMes($ano,$mes,$papel){

        $dadosProvento = DB::table('proventos')
                    ->select('valor')
                    ->where('cdPapel',$papel)
                    ->whereYear('dtProvento',$ano)
                    ->whereMonth('dtProvento',$mes)
                    ->get();

        if( count($dadosProvento) ){
            return (float) $dadosProvento[0]->valor;
        }else{
            return (float) 0.00;
        }
                    
    }

    public static function getValorAportePapelMes($ano,$mes,$papel){
        
        $valorAportes = DB::table('aportes')
                    ->select('subTotal','dtAporte')
                    ->where('cdPapel',$papel)
                    ->whereYear('dtAporte',$ano)
                    ->whereMonth('dtAporte',$mes)
                    ->sum('subTotal');

        return (float) $valorAportes;

    }

    public static function getValorProventoPapelMes($ano,$mes,$papel){
        
        $valorProventos = DB::table('proventos')
                    ->select('subTotal','dtProvento')
                    ->where('cdPapel',$papel)
                    ->whereYear('dtProvento',$ano)
                    ->whereMonth('dtProvento',$mes)
                    ->sum('subTotal');

        return (float) $valorProventos;

    }

    public static function mediaProventos($cdPapel, $historicoMeses){

        $dados = DB::table('proventos')
            ->select('valor')
            ->where('cdPapel',$cdPapel)
            ->orderBy('dtProvento','desc')
            ->limit($historicoMeses)
            ->get();
        
        $media = 0.00;
        $total = 0.00;

        for($i=0; $i<count($dados); $i++){
            
            $total += $dados[$i]->valor;

            if( $i == count($dados) - 1 ){
                $media = $total / count($dados);
            }

        }
        
        return $media;

    }

    public static function getContagemPapeis($params=[]){
            
        $papeis = DB::table('papel')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'));

        //trata os filtros
        if( count($params) ){
            
            if( isset($params['papel']) && !empty($params['papel']) && !is_null($params['papel']) ){
                $papeis = $papeis->where('nmPapel','like','%'.$params['papel'].'%');
            }

            if( isset($params['tipo']) && !empty($params['tipo']) && !is_null($params['tipo']) ){   
                $papeis = $papeis->where('cdTipo',$params['tipo']);
            }

            if( isset($params['subTipo']) && !empty($params['subTipo']) && !is_null($params['subTipo']) ){   
                $papeis = $papeis->where('subTipo',$params['subTipo']);
            }

        }

        $papeis = $papeis->get();

        for( $i=0; $i<count($papeis); $i++ ){
            
            $aportes = self::getAportesPapel( $papeis[$i]->cdPapel );
            $papeis[$i]->qtdeAportes    = $aportes[0]->qtdeAportes;
            $papeis[$i]->totalAportado  = $aportes[0]->totalAportado;

            $resgates = self::getResgatesPapel( $papeis[$i]->cdPapel );
            $papeis[$i]->qtdeResgates   = $resgates[0]->qtdeResgates;
            $papeis[$i]->totalResgatado = $resgates[0]->totalResgatado;

            $proventos = self::getProventosPapel( $papeis[$i]->cdPapel );
            $papeis[$i]->qtdeProventos  = $proventos[0]->qtdeProventos;
            $papeis[$i]->totalProventos = $proventos[0]->totalProventos;
        }

        return $papeis;
        
    }

    public static function getUltimaCotacao($cdPapel){
        
        $dados = DB::table('historicoCotacoes')
                ->select('dtCotacao')
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->where('cdPapel',$cdPapel)
                ->orderBy('dtCotacao','desc')
                ->limit(1)
                ->get();

        return $dados;

    }

    


}