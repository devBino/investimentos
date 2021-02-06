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

    $('.btMoveInforme').click(function(){
        var move        = $(this).attr('data-move')
        
        if( move == '1' ){
            marcadorMaisMais()
        }else{
            marcadorMenosMenos()
        }

    })

    
    configsInicio()
})

function marcadorMaisMais(){
    var marcador    = $('#marcador').val()
    marcador = parseInt(marcador) + 5
    $('#marcador').val(marcador)
    percorreRowsLancamentos(marcador)
}

function marcadorMenosMenos(){
    var marcador    = $('#marcador').val()
    marcador = parseInt(marcador) - 5
    $('#marcador').val(marcador)
    percorreRowsLancamentos(marcador)
}

function percorreRowsLancamentos(marcador){
    
    var rows = document.querySelectorAll('.tr_informe')
    
    var cont = 0
    
    for( var i=marcador; i<marcador+5; i++ ){
        
        var strDados = $(rows[i]).attr('data-dados')
        var arrDados = strDados.split('|')
        
        $(`#td_desc_${cont}`).text(arrDados[0])
        $(`#td_val_${cont}`).text(arrDados[2])

        cont += 1

        if( cont % 4 == 0 ) {
            cont    = 0
            
            setTimeout( function(){
                somaTotal()
            },250)       
            
        }

    }

}

function somaTotal(){

    var totais = document.querySelectorAll('.totais')
    var total = 0

    for( var i=0; i<totais.length; i++ ){
        
        var strTotal = $(totais[i]).text()
        
        var valorDesc   = strTotal.replace('R$','')
        valorDesc       = valorDesc.replace('.','')
        valorDesc       = valorDesc.replace(',','.')

        total += parseFloat(valorDesc)
    }

    $('#td_total').text(`R$ ${total.toFixed(2)}`)
}