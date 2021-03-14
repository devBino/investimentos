<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Cotacao as COT;
use App\Http\Repositories\Papel as PAP;

class Cotacao{

    public function index(){

        $data = [];
        $papeis = PAP::getPapeisRendaVariavel();
        $return = [];

        for($i=0; $i<count($papeis); $i++){
        
            $precoMedioPapel = PAP::getPrecoMedioPapel($papeis[$i]->cdPapel);
            
            if( $precoMedioPapel <= 0 ){
                continue;
            }

            if($papeis[$i]->subTipo == 3){
                
                if( $papeis[$i]->nmPapel[   strlen($papeis[$i]->nmPapel) - 1] == 'F' ){
                    $codAcao = substr($papeis[$i]->nmPapel,0,strlen($papeis[$i]->nmPapel));
                }else{
                    $codAcao = $papeis[$i]->nmPapel;
                }
                
                $valorCotacao = COT::getCotacao($codAcao)['valor'];

            }else if($papeis[$i]->subTipo == 2){
                $valorCotacao = COT::getCotacaoFII($papeis[$i]->nmPapel)['valor'];
            }

            $papeis[$i]->cotacaoAtual = $valorCotacao;
            $return[] = $papeis[$i];
            
        }

        $data['listagem'] = $return;

        return view('cotacao.index')->with(['data'=>$data]);
    }

}