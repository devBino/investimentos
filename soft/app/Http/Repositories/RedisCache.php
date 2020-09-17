<?php
namespace App\Http\Repositories;

/**
 * @author Fernando Bino
 * @description Classe para facilitar o uso do Redis, ainda poderá receber atualizações
 *      nela existem abstrações dos métodos Redis::get(); Redis::set(); Redis::del();
*/

use Illuminate\Support\Facades\Redis;

class RedisCache{
    
    public function __construct(){

    }

    public static function redisGet($chave){
        $return = Redis::get($chave);

        if( is_null($return) ){
            Redis::set($chave,null);
        }

        $return = Redis::get($chave);

        return $return;
    }

    public static function redisSet($chave, $conteudo){
        Redis::set($chave,$conteudo);

        $return = Redis::get($chave);

        return $return;
    }

    public static function redisSetArray($chave,$array){
        Redis::set($chave, serialize($array));
    }

    public static function redisGetArray($chave){
        $return = Redis::get($chave);

        if( !is_null($return) ){
            $return = unserialize($return);
        }

        return $return;
    }

    public static function redisDel($chave){
        $dados = Redis::get($chave);

        if( !is_null($dados) ){
            Redis::del($chave);
        }
    }
   
}