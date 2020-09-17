var objConfigs = {
    htmlInProgress: `<i id="icon-bt" class="fas fa-spinner fa-spin"></i>`,
    htmlFinished: `<i id="icon-bt" class="fas fa-sync"></i>`
}

$(function(){

    $('#permissaoValores').click(function(){
        var status = $('#permissaoValores').attr('data-status')

        try{
            var obj = {
                _token:$('#tkn').val(),
                status:status
            }

            send_ajax('/dashboard-permissao-valores','post', obj, respostaAtualizaPermissao)

        }catch(e){
            console.error(e)
        }

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

    configsInicio()
})

function respostaAtualziaCotacao( resp ){
    try{

        var jsonResp = JSON.parse(resp)
        alert(jsonResp.msg)
        
        window.location.href = ''
        
    }catch(e){
        $('#atualizaCotacao').html(objConfigs.htmlFinished)
        console.error(e)
    }
}

function respostaAtualizaPermissao( resp ){
    try{

        if(resp.success == true){
            window.location.href = ''
        }
        
    }catch(e){
        console.error(e)
    }
}