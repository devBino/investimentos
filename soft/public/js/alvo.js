var objConfigs = {
    htmlInProgress: `<i id="icon-bt" class="fas fa-spinner fa-spin"></i> Atualizando Papeis`,
    htmlFinished: `<i id="icon-bt" class="fas fa-sync"></i> Atualizar Cotações  `
}

$(function(){
    
    $('.deleta').click(function(){

        if( confirm('DESEJA MESMO DELETAR O REGISTRO SELECIONADO??') ){
            return true
        }else{
            return false
        }

    })

    $('.edita').click(function(){
        
        var strDados    = $(this).attr('data-dados')
        var arrDados    = strDados.split('|')

        $('#papel').val(arrDados[0]).trigger('change')
        $('#tipo').val(arrDados[1]).trigger('change')
        $('#valor').val(arrDados[2])

        $('#papel').focus()

    })

    $('#atualizaCotacao').click(function(){
        
        $('#atualizaCotacao').html(objConfigs.htmlInProgress)

        try{
            var obj = {
                _token:$('#tkn').val()
            }

            send_ajax('/papel-atualiza-cotacao','post', obj, respostaAtualziaCotacao)

        }catch(e){
            $('#atualizaCotacao').html(objConfigs.htmlFinished)
        }
    })

    $('.text-cotacoes').change(function(){
        try{
            var papel   = $(this).attr('data-papel')
            var cotacao = $(this).val()
            
            send_ajax(`/papel-atualiza-cotacao-manual/${papel}/${cotacao}`,'get', {papel:papel,cotacao:cotacao}, respostaAtualziaCotacaoManual)
        }catch(e){
            console.error(e)
        }
    })

    $('#btPesquisar').click(function(){
        
        $('#modal').show()

        setTimeout(function(){
            $('#tipoPesquisa').select2()
            $('#subTipoPesquisa').select2()
            $('#papelPesquisa').focus()
        },50)
    })
    $('#btFechaModal').click(function(){
        $('#modal').hide()
    })
    
    configsInicio()
})

function respostaAtualziaCotacao( resp ){
    try{
        var jsonResp = JSON.parse(resp)
    
        $('#atualizaCotacao').html(objConfigs.htmlFinished)

        alert(jsonResp.msg)

        window.location.href = ''
        
    }catch(e){
        $('#atualizaCotacao').html(objConfigs.htmlFinished)
        console.error(e)
    }
}

function respostaAtualziaCotacaoManual( resp ){
    try{
        var jsonResp = JSON.parse(resp)

        if( jsonResp.success ){
            window.location.href = ''
        }
        
    }catch(e){        
        console.error(e)
    }
}