<?php
namespace App\Http\Repositories;

use DB;
use Exception;
use App\Http\Repositories\Papel as PAP;

class Rendimentos{


    public static function getRendimentos($params = []){

        $dados = DB::table('resgates as r')
            ->select(
                'r.cdPapel',
                'p.nmPapel',
                'p.cdTipo',
                'p.subTipo',
                DB::raw('null as rendimentos'),
                DB::raw('0.00 as irDevido')
            )
            ->join('papel as p','p.cdPapel','=','r.cdPapel');
        
        if( isset($params['ano']) && !empty($params['ano']) ){
            $dados = $dados->whereYear('r.dtResgate',$params['ano']);
        }

        if( isset($params['subTipo']) && !empty($params['subTipo']) ){
            $dados = $dados->where('p.subTipo', $params['subTipo']);
        }
            
        $dados = $dados->groupBy('r.cdPapel')
            ->orderBy('p.nmPapel')
            ->get();

        for($i=0; $i<count($dados); $i++){
            
            $rendimentosPapel = PAP::getRendimentosPapel($dados[$i]->cdPapel);
            
            $dados[$i]->irDevido = $rendimentosPapel[0]->descontoIr;

            if( $rendimentosPapel[0]->lucro < 0.00 ){
                $dados[$i]->irDevido = ($rendimentosPapel[0]->descontoIr - ($rendimentosPapel[0]->lucro * -1) );
            }

            $dados[$i]->rendimentos = $rendimentosPapel;

        }
        
        return $dados;

    }

    public static function getTotais($rendimentos){

        $aportes    = 0.00;
        $resgates   = 0.00;
        $lucro      = 0.00;
        $ir         = 0.00;
        $prejuizo   = 0.00;
        $irDevido   = 0.00;

        for($i=0; $i<count($rendimentos); $i++){
            
            $aportes += $rendimentos[$i]->rendimentos[0]->aportes;
            $resgates += $rendimentos[$i]->rendimentos[0]->resgates;

            $lucro += $rendimentos[$i]->rendimentos[0]->lucro;

            if( $rendimentos[$i]->rendimentos[0]->lucro <= 0.00 ){
                $prejuizo += $rendimentos[$i]->rendimentos[0]->lucro;
            }
            
            $ir += $rendimentos[$i]->rendimentos[0]->descontoIr;
        }
        
        $irDevido = $ir;

        if( $prejuizo < 0 ){
            $irDevido = ($ir - ( $prejuizo * -1 ));
        }

        return [
            'aportes'=>$aportes,
            'resgates'=>$resgates,
            'lucro'=>$lucro,
            'ir'=>$ir,
            'irDevido'=>$irDevido
        ];

    }

}