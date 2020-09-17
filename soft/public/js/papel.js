var objConfigs = {
    htmlInProgress: `<i id="icon-bt" class="fas fa-spinner fa-spin"></i> Atualizando Papeis`,
    htmlFinished: `<i id="icon-bt" class="fas fa-sync"></i> Atualizar Cotações  `
}

$(function(){
    
    $('#papel').keyup(function(){
        $('#papel').val( $('#papel').val().toUpperCase() )
    })

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

        $('#nomeAntigo').val(arrDados[0])
        $('#papel').val(arrDados[0])
        $('#tipo').val(arrDados[1]).trigger('change')
        $('#subTipo').val(arrDados[2]).trigger('change')
        $('#taxaIr').val(arrDados[3]).trigger('change')

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