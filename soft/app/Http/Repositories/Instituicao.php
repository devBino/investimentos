<?php
namespace App\Http\Repositories;

use Illuminate\Support\Facades\Storage;

/**
 * Instituicao prove métodos para ler e retornar 
 * instituições financeiras
 * 
 * @author Fernando Bino Machado
 */
class Instituicao{

    public static function getInstituicoes(){
    
        $contents = Storage::disk('public')->get('bancos_financeiros.txt');
        
        $data = explode("\n", $contents);
        $instituicoes = [];

        $cont = 0;

        $instituicao = "";

        while( $cont < count($data) ){
            
            if( $cont % 2 == 0 ){
                $instituicao = $data[$cont] . " - ";
            }else{
                $instituicao .= $data[$cont];
                $instituicoes[] = $instituicao;
                $instituicao    = "";
            }

            $cont += 1;

        }
        
        return $instituicoes;
        
    }

}