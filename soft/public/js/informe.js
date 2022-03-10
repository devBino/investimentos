$(function(){
    
    $('#descricao').change(function(){
        setTimeout(function(){
            $('#valor').focus()
        },300)
    })

    $('.pesquisa-dia').click(function(){

        var dia = $(this).attr('data-dia')

    })

    $('.deleta').click(function(){

        if( confirm('DESEJA MESMO DELETAR O REGISTRO SELECIONADO??') ){
            return true
        }else{
            return false
        }

    })

    $('.view-grafico').click(function(){
        $('#rowGrafico').fadeToggle()
    })

    $('.view-tabela').click(function(){
        $('#rowTabela').fadeToggle()
    })

    configsInicio()
    
})