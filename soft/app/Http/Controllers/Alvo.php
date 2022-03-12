<?php
namespace App\Http\Controllers;

use App\Http\Repositories\Papel as PAP;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use DB;

class Alvo{
    
    private $tipoOrdenacao   = 2;
    private $ordem           = 1;
    private $valorSimulacao  = 1000.00;
    private $historicoMeses = 6;

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

        if( isset($params['valorSimulacao']) && !empty($params['valorSimulacao']) ){
            $this->valorSimulacao = $params['valorSimulacao'];
        }

        if( isset($params['ordenacao']) && !empty($params['ordenacao']) ){
            $this->tipoOrdenacao = $params['ordenacao'];
        }

        if( isset($params['tipo']) && !empty($params['tipo']) ){
            $this->ordem = $params['tipo'];
        }

        if( isset($params['historicoMeses']) && !empty($params['historicoMeses']) ){
            $this->historicoMeses = $params['historicoMeses'];
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
            'listagem'=>self::getListaAlvos($listaPapeis),
            'params'=>$params
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

            $dadosUltimoAporte = PAP::getUltimoAporte($val->cdPapel);
            $dadosAtualizacaoDiaria = PAP::getUltimaCotacao($val->cdPapel);
            
            $mediaProventos = PAP::mediaProventos($val->cdPapel, $this->historicoMeses);
            $val->mediaProventos = $mediaProventos;
            $val->dyMes = ($mediaProventos / $val->cotacao) * 100;
            $val->dyAno = $val->dyMes * 12;

            $diasUltimoAporte = 0;

            if( count($dadosUltimoAporte) ){
                $dias = ( time() - strtotime($dadosUltimoAporte[0]->dtAporte) ) / (24 *  3600);                
                $diasUltimoAporte = intval($dias);
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

                $dadosAlvoAporte['ultimoPrecoPago']     = $dadosUltimoAporte[0]->valor;
                $dadosAlvoAporte['diasUltimoAporte']    = $diasUltimoAporte;
                $dadosAlvoAporte['diferenca']           = $dadosAlvoAporte['ultimoPrecoPago'] - $val->cotacao;
                $dadosAlvoAporte['ativo']               = unserialize(Redis::get('sub_tipo_papel'))[$val->subTipo];

                $dadosAlvoAporte['comparaPrecoMedioCotacao']    = $dadosAlvoAporte['precoMedio'] - $dadosAlvoAporte['cotacao'];
                $dadosAlvoAporte['atualizacaoDiaria']           = count($dadosAtualizacaoDiaria) ? $dadosAtualizacaoDiaria[0]->dtCotacao : 0;
                $dadosAlvoAporte['mediaProventos']              = number_format($val->mediaProventos,2,',','.');

                $dadosAlvoAporte['dyAno'] = $val->dyAno;
                $dadosAlvoAporte['dyMes'] = $val->dyMes;

                $dadosAlvoAporte['valorSimulacao']              = $this->valorSimulacao;
                $dadosAlvoAporte['cotasSimulacao']              = floor($this->valorSimulacao / $val->cotacao);
                $dadosAlvoAporte['dividendosSimulacaoMensal']   = ($dadosAlvoAporte['cotasSimulacao'] * $val->mediaProventos);
                $dadosAlvoAporte['dividendosSimulacaoAnual']    = ($dadosAlvoAporte['cotasSimulacao'] * $val->mediaProventos) * 12;
                
                $return[] = $dadosAlvoAporte;

            }
            
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
            'comparaPrecoMedioCotacao',
            'dyAno',
            'dividendosSimulacaoAnual',
            "cotasSimulacao",
            'diasUltimoAporte'
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