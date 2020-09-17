$(function(){
    
    $('#descricao').keyup(function(){
        $('#descricao').val( $('#descricao').val().toUpperCase() )
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