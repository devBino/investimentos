<?php
namespace App\Http\Repositories;

class ExportData{

    protected $html;
    protected $arquivo;
    
    public function setHtml($html){
        $this->html = utf8_decode($html);
    }

    public function getHtml(){
        return $this->html;
    }

    public function Output($params){
        
        $html = $this->getHtml();

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D,d M YH:i:s') . " GMT" );
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        if( isset($params['html']) && $params['html'] == '1' ){
            $this->arquivo  = "Export_".date('dmY',time()).".html";
        }else{
            $this->arquivo  = "Export_".date('dmY',time()).".xls";
            header('Content-type: application/x-msexcel');
        }

        header('Content-Disposition: attachment; filename="'.$this->arquivo.'"');
        header('Content-Description: PHP Generated Data');

        echo $html;
        exit;    
    }

}