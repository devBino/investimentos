<?php
namespace App\Http\Repositories;

use DB;
use Exception;
use App\Http\Repositories\CRUD as CRUD_DB;
use App\Http\Repositories\Movimento as MOV;

class Caixa{
    
    public function __construct(){

    }

    public static function salvar($params = []){
        
        $campos = [
            'descricao'=>$params['descricao'],
            'valor'=>$params['valor'],
            'dtLancamento'=> ( !is_null($params['dataLancamento']) && !empty($params['dataLancamento']) ) ? date('Y-m-d', strtotime($params['dataLancamento'])) . date(' H:i:s') : date('Y-m-d H:i:s'),
            'cdTipo'=>$params['tipo'],
            'cdUsuario'=>session()->get('autenticado.id_user')
        ];
            
        $acao = CRUD_DB::salvar(['tabela'=>'lancamentos','dados'=>$campos]);

        return $acao;

    }

    public static function deletar($id){
        
        $dados = [
            'tabela'=>'lancamentos',
            'campo'=>'cdLancamento',
            'valor'=>$id
        ];

        $acao = CRUD_DB::deletar($dados);

        return $acao;

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