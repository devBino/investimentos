<?php
namespace App\Http\Controllers;

use App\Http\Repositories\Papel as PAP;
use App\Http\Repositories\Rendimentos as REND;
use Illuminate\Http\Request;
use DB;

class Rendimentos{
    public function __construct(){

    }

    public function index(Request $request){
        
        $data['papeis']         = PAP::getPapeisRendaVariavel();
        $data['rendimentos']    = REND::getRendimentos( $request->all() );
        $data['totais']         = REND::getTotais( $data['rendimentos'] );
        
        return view('informe.rendimentos.index')->with(['data'=>$data]);
    }


}