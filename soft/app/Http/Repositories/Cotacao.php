<?php
namespace App\Http\Repositories;

use DB;
use Exception;

class Cotacao{

    public function __construct(){
    }

    /**
     * @author Fernando Bino
     * @description recupera a cotação de uma ação listada na bolsa de valores consumindo dados da API hgbrasil
     * @return array $return
    */
    public static function getCotacao($codAcao){

        try{

            $url = "https://api.hgbrasil.com/finance/stock_price?key=".getenv('API_HGBRASIL')."&symbol=".$codAcao;
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            
            $dadosReponse = json_decode($response,true);

            if ( isset($dadosReponse['results'][strtoupper($codAcao)]['error']) && $dadosReponse['results'][strtoupper($codAcao)]['error']  == true ) {
                $cotacaoAlternativa = self::getCotacaoAlternativa($codAcao);
                $return = ['valor'=>$cotacaoAlternativa['valor']];
            }else{     
                $valorCotacao = $dadosReponse['results'][strtoupper($codAcao)]['price'];
                $return = ['valor'=>$valorCotacao];
            }

            return $return;

        }catch(Exception $e){
            return ['valor'=>0.00];
        }

    }

    /**
     * @author Fernando Bino
     * @description recupera a cotação de uma ação listada na bolsa de valores
     *  de forma alternativa, caso a função getCotacao($codAcao) não encontre dados do ativo
     *  nesse caso vai recuperar o conteúdo do site fundamentus por file_get_contents como segue abaixo
     * @return array
    */
    public static function getCotacaoAlternativa($codAcao){
        
        try{

            $url = "https://www.fundamentus.com.br/detalhes.php?papel=".$codAcao;
            $contents = file_get_contents($url);

            $posIni = strpos($contents,">Cota");
            
            if( !($posIni !== false) ){
                return ['valor'=>'0.00'];
            }

            $textoCotacao = substr($contents,$posIni+70,20);
            
            $num = preg_replace('/[^0-9,]/','',$textoCotacao);
            $num = preg_replace('/\"/','',$num);
            $num = str_replace(',','.',$num);
            $num = (float) $num;
            
            return ['valor'=>$num];

        }catch(Exception $e){
            return ['valor'=>0.00];
        }

    }

    /**
     * @author Fernando Bino
     * @description Busca cotação de um Fundo Imobiliário fazendo file_get_contents num get para o site fundsexplorer
     * @return array
    */
    public static function getCotacaoFII($codFii){
        try{
        
            $url = "https://www.fundsexplorer.com.br/funds/".$codFii;
            $contents = file_get_contents($url);

            $posIni = strpos($contents,"<span class=\"price\">");

            if( !($posIni !== false) ){
                return ['valor'=>'0.00'];
            }

            $textoCotacao = substr($contents,$posIni,70);
            
            $num = preg_replace('/[^0-9,]/','',$textoCotacao);
            $num = preg_replace('/[^0-9,]/','',$textoCotacao);
            $num = preg_replace('/\"/','',$num);
            $num = str_replace(',','.',$num);
            $num = (float) $num;
            
            return ['valor'=>$num];

        }catch(Exception $e){
            return ['valor'=>0.00];
        }

    }

    /**
     * @author Fernando Bino
     * @description recupera dados da taxa selic e cdi
     * @return array $dadosReponse
    */
    public static function getTaxaSelicCdi(){
        $url = "https://api.hgbrasil.com/finance/taxes?key=".getenv('API_HGBRASIL');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        $dadosReponse = json_decode($response,true);

        return $dadosReponse;
    }

    /**
     * @author Fernando Bino
     * @description busca no serviço hgbrasil a taxa selic atual
     *      consome a resposta da API e retorna '' ou a taxa selic
    */
    public static function getTaxaSelic(){
        $url = "https://api.hgbrasil.com/finance/taxes?key=".getenv('API_HGBRASIL');

        $curl = curl_init($url);

        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($curl,CURLOPT_HTTPHEADER, array(
            "content-type: application/json"
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $dadosSelic = json_decode($response,true);
        
        if( isset($dadosSelic['results']) && count($dadosSelic['results']) ){
            return $dadosSelic['results'][0]['selic_daily'];
        }else{
            return '';
        }

    }


    /**
     * @author Fernando Bino
     * @desctiption Recupera rentabilidade do Fundo Trend Bolsa Americana Dolar Fim
     *  fazendo file_get_contents no site da XP que traz informações sobre
    */
    public static function getPerformaceDolarFim(){
        
        //busca os dados
        $url = "https://institucional.xpi.com.br/investimentos/fundos-de-investimento/detalhes-de-fundos-de-investimento.aspx?F=800626";
        $response = file_get_contents($url);

        //extrai o html da tabela que contem os dados da cotação
        $arrTrechosCabecalhos = [
            ["<table class=\"table table-bordered table-hover tabelaXP\">","</table>"],
            ["<thead>","</thead>"],
            ["<tr>","</tr>"]
        ];
        $arrTrechosCorpo = [
            ["<table class=\"table table-bordered table-hover tabelaXP\">","</table>"],
            ["<tbody>","</tbody>"],
            ["<tr>","</tr>"]
        ];

        $htmlCabecalhos = self::getTexto($arrTrechosCabecalhos,$response);
        $htmlCorpo      = self::getTexto($arrTrechosCorpo,$response);

        //transforma o thead e tbody da tabela em arrays
        $arrCabecalhos = explode("<th>",$htmlCabecalhos);
        $arrCabecalhos = array_filter($arrCabecalhos);

        $arrCorpo = explode("<td>",$htmlCorpo);
        $arrCorpo = array_filter($arrCorpo);

        //identifica no array thead em que posição está a cotação de rentabilidade no ano
        $posicaoCotacao = 5;
        
        for( $i=0; $i<count($arrCabecalhos); $i++ ){
            if( strpos(strtolower($arrCabecalhos[$i]),"ano") !== false ){
                $posicaoCotacao = $i;
                break;
            }
        }

        $taxaAno = preg_replace('/[^0-9,]/','',$arrCorpo[$posicaoCotacao]);
        
        return $taxaAno;
        
    }

    public static function getTexto($array,$texto){

        $textoFinal = $texto;

        for( $i=0;$i<count($array); $i++ ){
            $textoFinal = substr(
                $textoFinal,
                strpos( $textoFinal,$array[$i][0] ) + strlen($array[$i][0]),
                strpos( $textoFinal,$array[$i][1] ) - ( strpos( $textoFinal,$array[$i][0] ) + strlen($array[$i][0]) )
            );
        }

        return $textoFinal;

    }


    
}