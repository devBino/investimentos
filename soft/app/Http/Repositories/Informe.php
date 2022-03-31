<?php
namespace App\Http\Repositories;

use DB;
use Exception;

class Informe{

    public static function getInformes($dataFinal){

        $lancamentos = DB::table('informe')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('dtInforme','<=',$dataFinal)
            ->orderBy('cdInforme','desc')
            ->get();

        return $lancamentos;

    }

    public static function getInformesData($params){

        if( !isset($params['data']) || empty($params['data']) ){
            return [];
        }

        $lancamentos = DB::table('informe')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('dtInforme','=',$params['data'])
            ->orderBy('cdInforme','desc')
            ->get();

        return $lancamentos;

    }

    public static function getAgrupamentos($dataFinal){
        
        $agrupamentos = DB::table('informe')
            ->select(
                'cdInforme',
                'dtInforme',
                DB::raw('sum(valor) as valor')
            )
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('dtInforme','<=',$dataFinal)
            ->groupBy('dtInforme')
            ->orderBy('dtInforme', 'desc')
            ->get();

        return $agrupamentos;

    }

    public static function getUltimoLancamento(){

        $dataUltimoLancamento = DB::table('informe')
            ->select('dtInforme')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->orderBy('dtInforme','desc')
            ->limit(1)
            ->get();

        if( count($dataUltimoLancamento) ){
            $infoData   = explode("-",$dataUltimoLancamento[0]->dtInforme);
            
            $registros = DB::table('informe')
                ->select()
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->whereYear('dtInforme', $infoData[0])
                ->whereMonth('dtInforme', $infoData[1])
                ->whereDay('dtInforme', $infoData[2])
                ->get();

            return $registros;

        }else{
            return [];
        }

    }

    public static function getDadosInforme($params = []){

        $dataFinal = date('Y-m-d');

        if( isset($params['data']) && !empty($params['data']) ){
            $dataFinal = date('Y-m-d',strtotime($params['data']));
        }

        $lancamentos        = self::getInformes($dataFinal);
        $agrupamentos       = self::getAgrupamentos($dataFinal);
        $ultimoLancamento   = self::getUltimoLancamento();

        $data['lancamentos']        = $lancamentos;
        $data['agrupamento']        = $agrupamentos;
        $data['ultimoLancamento']   = $ultimoLancamento;

        return $data;
    }



}