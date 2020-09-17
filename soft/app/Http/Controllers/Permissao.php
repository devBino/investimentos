<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Repositories\Cotacao as COT;

class Permissao{

    public function criarPermissao( Request $request, $params = [] ){
        
        //seta informações de sessão
        $dados = [];
        $dados['id_user']       = $params[0]->cdUsuario;
        $dados['nome_user']     = $params[0]->nmUsuario;
        $dados['senha_user']    = $params[0]->dsSenha;
        $dados['permissao']     = $params[0]->cdPermissao;
        $dados['tipo_papel']    = ['','RENDA FIXA','RENDA VARIAVEL','HEDGE'];
        $dados['tipo_provento'] = ['','DIVIDENDO','JUROS SOB CAP. PRÓPRIO'];

        $taxaSelic      = COT::getTaxaSelic();
        
        $dados['taxa_selic']    = $taxaSelic;

        if( !$request->session()->has('autenticado') ){
            $request->session()->put('autenticado',$dados);
        }else{
            $request->session()->forget('autenticado');
            $request->session()->put('autenticado',$dados);
        }

        //seta informações no Redis
        $chaveRedisUltimaAtualizacao    = "usuario_".$params[0]->cdUsuario.".tempo_atualiza_cotacoes";
        $chaveRedisCotacao              = "usuario_".$params[0]->cdUsuario."_dolarFim";

        Redis::set('tipo_papel',serialize($dados['tipo_papel']) );
        Redis::set('tipo_provento',serialize($dados['tipo_provento']) );
        
        $tempoInicialHistoricoAtualizacoes = time() - (int) getenv('INTERVALO_ATUALIZA_COTACOES');

        Redis::set($chaveRedisUltimaAtualizacao,  $tempoInicialHistoricoAtualizacoes);
        Redis::set($chaveRedisCotacao, serialize([            
            'selic'=>$taxaSelic
        ]));

        Redis::set('sub_tipo_papel',serialize(['','RENDA FIXA','FUNDOS IMOBILIÁRIOS','AÇÕES','HEDGE']));
        Redis::set('meses',serialize(['','JAN','FEV','MAR','ABR','MAI','JUN','JUL','AGO','SET','OUT','NOV','DEZ']));

    }

    public function destroiPermissao(){
        
        if( session()->has('autenticado') ){
            session()->forget('autenticado');
        }

    }


}