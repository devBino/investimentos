$(function(){
    
    $('#descricao').keyup(function(){
        $('#descricao').val( $('#descricao').val().toUpperCase() )
    })

    $('#btSalvar').click(function(){
        if( confirm('CONFIRMA O LANÇAMENTO? NÃO PODERÁ SER DELETADO FUTURAMENTE...') ){
            return true
        }else{
            return false
        }
    })
        

    $('.deleta').click(function(){

        if( confirm('DESEJA MESMO DELETAR O REGISTRO SELECIONADO??') ){
            return true
        }else{
            return false
        }

    })
    
    configsInicio()
})