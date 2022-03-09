$(function(){
    
    $('.deleta').click(function(){

        if( confirm('DESEJA MESMO DELETAR O REGISTRO SELECIONADO??') ){
            return true
        }else{
            return false
        }

    })

    $('#papel').change(function(){
        
        var strPapel = $('#papel option:selected').text()

        if( strNuloOuVazio(strPapel) ){
            return false
        }

        if( strPapel.toUpperCase().indexOf('SELIC') > -1 ){
            
            var taxa = $('#taxaSelic').val()
            $('#taxaRetorno').val(taxa)

        }else{
            $('#taxaRetorno').val('0')
        }

        setTimeout(() => {
            $('#valor').focus()
        }, 300)
        
    })

    $('#qtde').keyup(function(){
        calculaTotalAporte()
    })

    $('#valor').keyup(function(){
        calculaTotalAporte()
    })

    $('#btPesquisar').click(function(){
        
        $('#modal').show()

        setTimeout(function(){
            $('#papelPesquisa').select2()
            $('#tipo').select2()
            $('#subTipo').select2()
            $('#papelPesquisa').focus()
        },50)
    })

    $('#btFechaModal').click(function(){
        $('#modal').hide()
    })   

    
    
    configsInicio()

})

function calculaTotalAporte(){
    try{
        
        var qtde    = $('#qtde').val()
        var valor   = $('#valor').val()
        var total   = parseFloat(qtde) * parseFloat(valor)

        $('#subTotal').val(total.toFixed(2))

    }catch(e){
        console.error(e)
    }
}