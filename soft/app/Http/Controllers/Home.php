<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Permissao as Perm;
use DB;

class Home{

    public $permissao;

    public function __construct(){
        $this->permissao = new Perm();
    }

    public function home(){
        $this->permissao->destroiPermissao();
        return view('home');
    }

    public function login( Request $request ){
        $params = $request->all();
        
        $dados = DB::table('usuario')
            ->select()
            ->where('nmUsuario',$params['usuario'])
            ->where('dsSenha',sha1($params['senha']))
            ->get();

        if( count($dados) ){
            $this->permissao->criarPermissao( $request, $dados );
            return redirect('dashboard');
        }else{
            $this->permissao->destroiPermissao();
            return view('home')->with(['msg'=>'Login Inválido!!']);
        }

    }

    public function sistema(){
        return view('template.app');
    }

}