$(function(){
    
    $('.deleta').click(function(){

        if( confirm('DESEJA MESMO DELETAR O REGISTRO SELECIONADO??') ){
            return true
        }else{
            return false
        }

    })

    $('#qtde').keyup(function(){
        calculaTotalProvento()
    })

    $('#valor').keyup(function(){
        calculaTotalProvento()
    })

    $('#btPesquisar').click(function(){
        
        $('#modal').show()

        setTimeout(function(){
            $('#papelPesquisa').select2()
            $('#tipoPesquisa').select2()
            $('#subTipo').select2()
            $('#papelPesquisa').focus()
        },50)
    })
    
    $('#btFechaModal').click(function(){
        $('#modal').hide()
    })

    
    
    configsInicio()

})

function calculaTotalProvento(){
    try{
        
        var qtde    = $('#qtde').val()
        var valor   = $('#valor').val()
        var total   = parseFloat(qtde) * parseFloat(valor)

        $('#subTotal').val(total.toFixed(2))

    }catch(e){
        console.error(e)
    }
}