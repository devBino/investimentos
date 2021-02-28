<?php
namespace App\Http\Controllers;

use App\Http\Repositories\Papel as PAP;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use DB;

class Alvo{
    
    public $tipoOrdenacao   = 2;
    public $ordem           = 1;

    public function __construct(){

    }

    public function index(){
        
        $listaPapeis = PAP::getPapeisRendaVariavel();

        $papeis = [
            'papeis'=>$listaPapeis,
            'listagem'=>self::getListaAlvos($listaPapeis)
        ];
        
        return view('alvo.index')->with(['data'=>$papeis]);
        
    }

    public function pesquisaOrdenar(Request $request){
        
        //pega parametro de ordenação
        $params = $request->all();

        if( isset($params['ordenacao']) && !empty($params['ordenacao']) ){
            $this->tipoOrdenacao = $params['ordenacao'];
        }

        if( isset($params['tipo']) && !empty($params['tipo']) ){
            $this->ordem = $params['tipo'];
        }
        
        //pega dados e monta data para view
        $listaPapeis = DB::table('papel')
            ->select()
            ->where('cdUsuario',session()->get('autenticado.id_user'))
            ->orderBy('cdTipo','asc')
            ->orderBy('subTipo','asc')
            ->orderBy('nmPapel','asc')
            ->get();

        $papeis = [
            'papeis'=>$listaPapeis,
            'listagem'=>self::getListaAlvos($listaPapeis)
        ];
        
        return view('alvo.index')->with(['data'=>$papeis]);    
    }

    public function salvar(Request $request){
        $params = $request->all();
        $chaveUsuarioPapelAlvo = session()->get('autenticado.id_user') . "_" . $params['papel'] . "_" . $params['tipo'];

        Redis::set(
            $chaveUsuarioPapelAlvo,
            serialize([ 'codPapel'=>$params['papel'],'precoAlvo'=>$params['valor'],'tipo'=>$params['tipo'] ]),
            'EX',
            env('LIMITE_CACHE_PRECO_ALVO')
        );

        return redirect('alvo')->with('status','Preço Alvo salvo com sucesso...|success');

    }

    public function deletar($chave){
        $chaveUsuarioPapelAlvo = session()->get('autenticado.id_user') . "_" . $chave;
        Redis::del($chaveUsuarioPapelAlvo);

        return redirect('alvo')->with('status','Preço Alvo deletado com sucesso...|success');
    }

    public function getListaAlvos( $papeis = [] ){
        
        $return = [];

        foreach($papeis as $num => $val){
            
            if($val->cdTipo != 2){
                continue;
            }

            $dadosUltimoAporte = DB::table('aportes')
                ->select('dtAporte')
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->where('cdPapel',$val->cdPapel)
                ->orderBy('dtAporte','desc')
                ->limit('1')
                ->get();

            $dadosAtualizacaoDiaria = DB::table('historicoCotacoes')
                ->select('dtCotacao')
                ->where('cdUsuario',session()->get('autenticado.id_user'))
                ->where('cdPapel',$val->cdPapel)
                ->orderBy('dtCotacao','desc')
                ->limit(1)
                ->get();
            
            $ultimoAporte = "Nunca";

            if( count($dadosUltimoAporte) ){
                
                $dias = ( time() - strtotime($dadosUltimoAporte[0]->dtAporte) ) / (24 *  3600);
                
                $horas = ( time() - strtotime($dadosUltimoAporte[0]->dtAporte) ) / 3600;
                $horas = (int) $horas;

                $dias = (int) $dias;

                if( $dias > 0 ){
                    $ultimoAporte = date('Y-m-d',strtotime($dadosUltimoAporte[0]->dtAporte)) . " / Há ".$dias. " Dia(s).";
                }else{
                    if( $horas > 24 ){
                        $ultimoAporte = date('Y-m-d',strtotime($dadosUltimoAporte[0]->dtAporte)) . " / Há ".$dias. " Dia(s).";
                    }else if( $horas < 24 ){
                        if( date('d',strtotime($dadosUltimoAporte[0]->dtAporte)) == date('d',time()) ){
                            $ultimoAporte = date('Y-m-d',strtotime($dadosUltimoAporte[0]->dtAporte)) . " / Hoje. ";    
                        }else{
                            $ultimoAporte = date('Y-m-d',strtotime($dadosUltimoAporte[0]->dtAporte)) . " / Há ".$horas. " Hora(s).";
                        }
                    }else{
                        $ultimoAporte = date('Y-m-d',strtotime($dadosUltimoAporte[0]->dtAporte)) . " / Hoje. ";
                    }
                }
            }

            
            $valorPrecoMedioPapel = $this->precoMedioPapel($val->cdPapel);

            $dadosAlvoAporte    = [];
            
            if( $val->cdTipo == 2 && $valorPrecoMedioPapel > 0 ){
                $dadosAlvoAporte['precoAlvo']   = (float) self::getPrecoAlvo($val->cdPapel, session()->get('autenticado.id_user'));
                $dadosAlvoAporte['precoAlvo']   = number_format($dadosAlvoAporte['precoAlvo'],2,'.','');
                $dadosAlvoAporte['tipo']        = 1;
                $dadosAlvoAporte['codPapel']    = $val->cdPapel;

                $dadosAlvoAporte['cdPapel']     = $val->cdPapel;
                $dadosAlvoAporte['nmPapel']     = $val->nmPapel;
                $dadosAlvoAporte['precoMedio']  = $valorPrecoMedioPapel;
                $dadosAlvoAporte['cotacao']     = $val->cotacao;
                $dadosAlvoAporte['diferenca']   = $dadosAlvoAporte['precoAlvo'] - $val->cotacao;
                $dadosAlvoAporte['ativo']       = unserialize(Redis::get('sub_tipo_papel'))[$val->subTipo];
                $dadosAlvoAporte['ultimaOco']   = $ultimoAporte;
                $dadosAlvoAporte['comparaPrecoMedioCotacao']    = $dadosAlvoAporte['precoMedio'] - $dadosAlvoAporte['cotacao'];
                $dadosAlvoAporte['atualizacaoDiaria'] = count($dadosAtualizacaoDiaria) ? $dadosAtualizacaoDiaria[0]->dtCotacao : 0;
                
                $return[] = $dadosAlvoAporte;
            }
            
            /*$dadosAlvoResgate   = [];

            if( $dadosAlvoResgate ){
                $dadosAlvoResgate['cdPapel']    = $val->cdPapel;
                $dadosAlvoResgate['nmPapel']    = $val->nmPapel;
                $dadosAlvoResgate['precoMedio'] = 0.00;
                $dadosAlvoResgate['cotacao']    = $val->cotacao;
                $dadosAlvoResgate['diferenca']  = $val->cotacao - $dadosAlvoResgate['precoAlvo'];
                $dadosAlvoResgate['ativo']      = unserialize(Redis::get('sub_tipo_papel'))[$val->subTipo];
                $dadosAlvoResgate['ultimaOco']  = "-";
                $dadosAlvoResgate['comparaPrecoMedioCotacao']    = $dadosAlvoResgate['precoMedio'] - $dadosAlvoResgate['cotacao'];
                $dadosAlvoResgate['atualizacaoDiaria'] = count($dadosAtualizacaoDiaria) ? $dadosAtualizacaoDiaria[0]->dtCotacao : 0;
                $return[] = $dadosAlvoResgate;
            }*/

        }

        $return = $this->ordenaListaAlvos($return);

        return $return;
    }

    public function ordenaListaAlvos($lista){
        $arrMemoria = [];
        $return     = $lista;

        $arrCamposOrdenacao = [
            'precoMedio',
            'precoMedio',
            'cotacao',
            'diferenca',
            'comparaPrecoMedioCotacao'
        ];

        for( $i=0;$i<count($return);$i++ ){
            for($j=0;$j<count($return);$j++){
                if( $this->ordem == 1 ){
                    if( $return[$i][$arrCamposOrdenacao[$this->tipoOrdenacao]] < $return[$j][$arrCamposOrdenacao[$this->tipoOrdenacao]] ){
                        $arrMemoria = $return[$i];
                        $return[$i]  = $return[$j];
                        $return[$j]  = $arrMemoria;
                    }
                }else{
                    if( $return[$i][$arrCamposOrdenacao[$this->tipoOrdenacao]] > $return[$j][$arrCamposOrdenacao[$this->tipoOrdenacao]] ){
                        $arrMemoria = $return[$i];
                        $return[$i]  = $return[$j];
                        $return[$j]  = $arrMemoria;
                    }
                }
            }
        }

        return $return;

    }

    /**
     * @description recebe o codigo do papel e calcula seu preço médio de aportes
     * @param int $cdPapel
     * @return float $precoMedio
    */
    public function precoMedioPapel($cdPapel){
        return PAP::getPrecoMedioPapel($cdPapel);
    }

    public function getPrecoAlvo($cdPapel,$cdUsuario){
        $dados = DB::select( DB::raw( 'call spSugerePrecoAporte( '.$cdPapel.','.$cdUsuario.')' ) );

        if( count($dados) && isset( $dados[0]->precoAlvo ) ){
            return $dados[0]->precoAlvo;
        }else{
            return '0.00';
        }

    }

}