<?php
namespace App\Http\Repositories;

use DB;
use Exception;

class Aporte{

    public static function getAporte($id){

        $aporte = DB::table('aportes')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->where('cdAporte',$id)
            ->get();

        return $aporte;

    }

}