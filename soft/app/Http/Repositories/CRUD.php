<?php
namespace App\Http\Repositories;
use DB;
use Exception;

/**
 * @author Fernando Bino
 * @description Classe fornece abstração pra operações básicas no banco de dados
 *  de forma que pode ser usada em vários controllers apenas passando os parâmetros devidos
 * @example insert update delete
*/

class CRUD{

    public function __construct(){

    }

    public static function dadosLog( $dados ){
        $log = [];
        $return = array_merge($dados, $log);
        
        return $return;
    }
    
    public static function salvar( $params = [] ){        
        try{
            $dados  = self::dadosLog($params['dados']);
            $acao   = DB::table($params['tabela'])->insert($dados);
            return $acao;
        }catch(Exception $e){
            return false;
        }
    }

    public static function alterar( $params = [] ){
        try{
            
            $dados  = self::dadosLog($params['valores']);
            
            $acao   = DB::table($params['tabela'])
                ->where($params['campo'],$params['valor'])
                ->update($dados);

            return $acao;
        }catch(Exception $e){
            return 0;
        }
    }

    public static function deletar( $params = [] ){
        try{
            $acao = DB::table($params['tabela'])
                ->where($params['campo'],$params['valor'])
                ->delete();
        
            return $acao;
        }catch(Exception $e){
            return 0;
        }
    }

    public static function todos( $tabela, $paginate = 5 ){
        try{
            $return = DB::table($tabela)->select()->paginate($paginate);
            return $return;
        }catch(Exception $e){
            return 0;
        }
    }

    public static function pesquisaCampo( $params = [] ){
        try{
            $return = DB::table($params['tabela'])
                ->where($params['campo'],$params['valor'])
                ->get();
            
            return $return;
        }catch(Exception $e){
            return [];
        }
    }

}