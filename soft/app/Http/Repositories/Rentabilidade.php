<?php
namespace App\Http\Repositories;

use App\Http\Repositories\Taxa as TX;

class Rentabilidade{

    public function __construct(){

    }

    /**
     * @param array $params
     * @description calcula rentabilidade para renda fixa e hedge
     * @example calculoRentabilidade([
     *      'capital'=>1000.00,
     *      'dtAporte'=>'2020-03-15 12:45:12',
     *      'taxaRetorno'=>3.0,
     *      'taxaAdmin'=>0.25
     * ])
    */
    public static function calculoRentabilidade($params = [], $returnDetalhes = false){
        $capital        = $params['capital'];
        $dtAporte       = $params['dtAporte'];
        $dtAtual        = date('Y-m-d H:i:s');
        $taxaRetorno    = $params['taxaRetorno'];

        $timesDia       = 24 * 3600;
        $timesAno       = 365 * $timesDia;
        $timesAporte    = strtotime($params['dtAporte']);
        $timesAtual     = strtotime($dtAtual);

        $timesRend      = $timesAtual - $timesAporte;
        $anos           = $timesRend / $timesAno;
        $dias           = $timesRend / $timesDia;

        $taxaIof        = TX::getTaxaIof($dias);
        $taxaIr         = TX::getTaxaIr($dias);

        $montanteBruto  = $capital * pow( ( 1 + ($params['taxaRetorno'] / 100) ), $anos);
        $lucroBruto     = $montanteBruto - $capital;
        
        $descIof        = ( $lucroBruto / 100 ) * $taxaIof;
        $descIr         = ( $lucroBruto / 100 ) * $taxaIr;
        $descAdmin      = ( $lucroBruto / 100 ) * $params['taxaAdmin'];
        
        $montanteFinal  = $montanteBruto - ( $descIof + $descIr + $descAdmin );

        $lucroLiquido   = $montanteFinal - $capital;

        if( !$returnDetalhes ){
            return $montanteFinal;
        }else{
            return [
                'capitalInicial'=>$capital,
                'diasCorridos'=> (int) $dias,
                'montanteBruto'=>$montanteBruto,
                'montanteLiquido'=>$montanteFinal,
                'lucroBruto'=>$lucroBruto,
                'lucroLiquido'=>$lucroLiquido,
                'taxaIof'=>$taxaIof,
                'descontoIof'=>$descIof,
                'taxaIr'=>$taxaIr,
                'descontoIr'=>$descIr,
                'taxaAdmin'=>$params['taxaAdmin'],
                'descontoAdmin'=>$descAdmin
            ];
        }

    }

    /**
     * @author Fernando Bino
     * @description calcula rentabilidade pra renda variável
     * @example calculoRentabilidade([
     *      'cotacao'=>12.89,
     *      'qtde'=>10,
     *      'capital'=>1000.00,
     *      'dtAporte'=>'2020-03-15 12:45:12',
     *      'taxaAdmin'=>0.25,
     *      'taxaIr'=>15
     * ])
    */
    public static function calculoRentabilidadeRendaVar($params = [], $returnDetalhes = false){
        
        $capital        = $params['capital'];
        $dtAporte       = $params['dtAporte'];
        $dtAtual        = date('Y-m-d H:i:s');

        $timesDia       = 24 * 3600;
        $timesAno       = 365 * $timesDia;
        $timesAporte    = strtotime($params['dtAporte']);
        $timesAtual     = strtotime($dtAtual);

        $timesRend      = $timesAtual - $timesAporte;
        $anos           = $timesRend / $timesAno;
        $dias           = $timesRend / $timesDia;

        $taxaIof        = TX::getTaxaIof($dias);
        $taxaIr         = $params['taxaIr'];

        $montanteBruto  = (float) $params['qtde'] * (float) $params['cotacao'];
        $lucroBruto   = $montanteBruto - $capital;
        
        if( $lucroBruto > 0 ){
            //$descIof        = ( $lucroBruto / 100 ) * $taxaIof;
            $descIof = 0.00;
            $descIr         = ( $lucroBruto / 100 ) * $taxaIr;
            $descAdmin      = ( $lucroBruto / 100 ) * $params['taxaAdmin'];
        }else{
            $descIof        = 0.00;
            $descIr         = 0.00;
            $descAdmin      = 0.00;
        }

        $montanteFinal  = $montanteBruto - ( $descIof + $descIr + $descAdmin );

        $lucroLiquido   = $montanteFinal - $capital;

        if( !$returnDetalhes ){
            return $montanteFinal;
        }else{
            return [
                'capitalInicial'=>$capital,
                'diasCorridos'=> (int) $dias,
                'montanteBruto'=>$montanteBruto,
                'montanteLiquido'=>$montanteFinal,
                'lucroBruto'=>$lucroBruto,
                'lucroLiquido'=>$lucroLiquido,
                'taxaIof'=>$taxaIof,
                'descontoIof'=>$descIof,
                'taxaIr'=>$taxaIr,
                'descontoIr'=>$descIr,
                'taxaAdmin'=>$params['taxaAdmin'],
                'descontoAdmin'=>$descAdmin
            ];
        }

    }

}