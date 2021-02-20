<?php
namespace App\Http\Repositories;

use DB;
use Exception;

class Informe{

    public static function getDadosInforme($params = []){

        $dataFinal = date('Y-m-d');

        if( isset($params['data']) && !empty($params['data']) ){
            $dataFinal = date('Y-m-d',strtotime($params['data']));
        }

        $lancamentos = DB::table('informe')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('dtInforme','<=',$dataFinal)
            ->orderBy('cdInforme','desc')
            ->get();
        
        $agrupamentos = DB::table('informe')
            ->select(
                'dtInforme',
                DB::raw('sum(valor) as valor')
            )
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('dtInforme','<=',$dataFinal)
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

        return $data;
    }

}