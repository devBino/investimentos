<?php
namespace App\Http\Repositories;

use DB;
use Exception;
use App\Http\Repositories\Movimento as MOV;

class Caixa{
    
    public function __construct(){

    }

    /**
     * @author Fernando Bino
     * @description retorna o saldo do simples histórico de depósitos e saques
     * ou retorna saldo compacto, considerando proventos, aportes e resgates e saques
     * 1 => Quando array $params = [] E $saldoCompacto = false vai retornar o saldo de depositos menos saques
     * 2 => Quando array $params != [] E $saldoCompacto = true vai retornar saldo compacto, onde
     * saldo = (depositos + proventos + resgates) - (aportes - saques)
     * @return float $totalSaldo
    */
    public static function getSaldo($params = [], $saldoCompacto = false){

        //soma total depósitos
        $totalDepositos = DB::table('lancamentos')
            ->select('valor')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdTipo',1);

        if( isset($params['descricao']) && !empty($params['descricao']) ){
            $totalDepositos = $totalDepositos->where('descricao','like','%'.$params['descricao'].'%');
        }

        $totalDepositos = $totalDepositos->sum('valor');
        
        //soma total retiradas
        $totalRetiradas = DB::table('lancamentos')
            ->select('valor')
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdTipo',2);

        //verifica parametros
        if( isset($params['descricao']) && !empty($params['descricao']) ){
            $totalRetiradas = $totalRetiradas->where('descricao','like','%'.$params['descricao'].'%');
        }

        $totalRetiradas = $totalRetiradas->sum('valor');
        
        $totalSaldo = $totalDepositos - $totalRetiradas;

        return $totalSaldo;

    }


}