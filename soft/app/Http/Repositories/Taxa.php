<?php
namespace App\Http\Repositories;

use DB;
use Exception;

class Taxa{

    public function __construct(){

    }

    /**
     * @author Fernando Bino
     * @description recebe um número de dias e com base nesse
     *      retorna a taxa IOF do Investimento
     * 
    */
    public static function getTaxaIOF($dias){
        $dias = (int) $dias;

        $periodos = [
            '0' => 96.00,
            '1' => 96.00,
            '2' => 93.00,
            '3' => 90.00,
            '4' => 86.00,
            '5' => 83.00,
            '6' => 80.00,
            '7' => 76.00,
            '8' => 73.00,
            '9' => 70.00,
            '10' => 66.00,
            '11' => 63.00,
            '12' => 60.00,
            '13' => 56.00,
            '14' => 53.00,
            '15' => 50.00,
            '16' => 46.00,
            '17' => 43.00,
            '18' => 40.00,
            '19' => 36.00,
            '20' => 33.00,
            '21' => 30.00,
            '22' => 26.00,
            '23' => 23.00,
            '24' => 20.00,
            '25' => 16.00,
            '26' => 13.00,
            '27' => 10.00,
            '28' => 6.00,
            '29' => 3.00,
            '30' => 0.00
        ];

        if( !array_key_exists( $dias, $periodos) ){
            return '0.00';
        }else{
            return $periodos[$dias];
        }
    }
    
    /**
     * @author Fernando Bino
     * @description recebe um número de dias e com base nesse
     *      retorn a taxa IR do investimento
    */
    public static function getTaxaIR($dias){
        
        if( (int) $dias <= 180 ){
            return 22.5;
        }else if( (int) $dias <= 360 ){
            return 20.00;            
        }else if( (int) $dias <= 720 ){
            return 17.5;    
        }else if( (int) $dias <= 999999 ){
            return 15.00;
        }
    }


}