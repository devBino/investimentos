var objGlobal = {
    checado:true
}

$(function(){

    $('.resgatar').click(function(){
        try{
            
            if( confirm('Confirma a alteração de status de resgate do aposte selecionado?\nA ação não poderá ser desfeita futuramente...') ){

                var tipo    = $(this).attr('data-tipo')
                var status  = $(this).attr('data-status')
                
                if( tipo.indexOf('RENDA VAR') != -1 && status == '1' ){
                    var id      = $(this).attr('data-id')
                    var cotacao = $(this).attr('data-cotacao')
                    
                    cotacao = cotacao.replace('.',',')

                    cotacao = prompt(`Confirme o valor de cotação: `,cotacao)

                    if( cotacao != undefined && cotacao != null && cotacao != '' ){
                        var url = $(`#${id}`).attr('href')
                        
                        url = `${url}/${cotacao}`
                        $(`#${id}`).attr('href',url)

                        setTimeout(function(){
                            return true
                        },500)

                    }else{
                        return false
                    }
                }

            }else{
                return false
            }

        }catch(e){
            console.error(e)
            return false
        }
    })

    $('.chec_resgate').click(function(){
        try{
            $('#aportes').val('')

            var aportes = $('.chec_resgate')
            var arrAportes = []

            for( var i=0;i<aportes.length;i++ ){
                
                if( $(aportes[i]).is(':checked') == true ){
                    arrAportes.push( $(aportes[i]).attr('data-id') )
                }
            }

            var strAportes = arrAportes.toLocaleString()
            $('#aportes').val(strAportes)
            
        }catch(e){
            console.error(e)
        }

    })

    $('#btMarcarAportes').click(function(){
        $('#aportes').val('')

        var aportes = $('.chec_resgate')
        var arrAportes = []

        for( var i=0;i<aportes.length;i++ ){
            $(aportes[i]).prop('checked',objGlobal.checado)

            if( objGlobal.checado ){
                arrAportes.push( $(aportes[i]).attr('data-id') )
            }
        }

        var strAportes = arrAportes.toLocaleString()

        $('#aportes').val(strAportes)

        if( objGlobal.checado ){
            objGlobal.checado = false
        }else{
            objGlobal.checado = true
        }

    })

    $('#btResgatar').click(function(){
  
        if( strNuloOuVazio( $('#aportes').val() ) ){
            alert('Por favor, selecione os aportes...')
            return false
        }

        if( confirm('Confirma a alteração de status de resgate dos apostes?\nA ação não poderá ser desfeita futuramente...') ){

            var capturaCotacao  = prompt('Informe uma cotação para Renda Variável\nOu digite 0 zero para renda fixa:')
            
            while( strNuloOuVazio(capturaCotacao) ){
                capturaCotacao  = prompt('Informe uma cotação para Renda Variável\nOu digite 0 zero para renda fixa:')
            }

            var valorCotacao    = ''

            if( capturaCotacao != '0' ){
                valorCotacao = capturaCotacao
            }

            $('#cotacao').val(valorCotacao)
            return true
        }else{
            $('#cotacao').val('')
            return false
        }

    })

    configsInicio()
})