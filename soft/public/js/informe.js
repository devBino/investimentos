$(function(){
    
    $('#descricao').change(function(){
        setTimeout(function(){
            $('#valor').focus()
        },300)
    })

    $('.deleta').click(function(){

        if( confirm('DESEJA MESMO DELETAR O REGISTRO SELECIONADO??') ){
            return true
        }else{
            return false
        }

    })

    $('.bt-move-informe').click(function(){
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
    marcador = parseInt(marcador) + 1
    $('#marcador').val(marcador)

    var marcadorFixo = $('#marcadorFixo').val()
    marcadorFixo = parseInt(marcadorFixo)

    marcador = (marcador - marcadorFixo) * (-1)
    percorreRowsLancamentos(marcador)
}

function marcadorMenosMenos(){
    var marcador    = $('#marcador').val()
    marcador = parseInt(marcador) - 1
    $('#marcador').val(marcador)

    var marcadorFixo = $('#marcadorFixo').val()
    marcadorFixo = parseInt(marcadorFixo)

    marcador = (marcador - marcadorFixo) * (-1)

    percorreRowsLancamentos(marcador)
}

async function pesquisarHistoricoInforme( data ){
    try{
        
        var params = {
            _token:$('#tkn').val(),
            data:data
        }

        var dadosHistorico = await promiseAjax('/informe-busca-dados','post',params)
        
        if( dadosHistorico.data === undefined || dadosHistorico.error ){
            msgAlert('Ocorreu um erro ao tentar buscar os dados...')
        }
        
        $('#containerGrafico').html(dadosHistorico.data.html)

    }catch(e){
        console.error(e)
    }
}

function percorreRowsLancamentos(marcador){
    
    try{

        var rows = document.querySelectorAll(`.linha_grupo_${marcador}`)
        
        var cont = 0
        var setouData = false

        for( var i=0; i<5; i++ ){
            
            var strDados = $(rows[i]).attr('data-dados')
            var arrDados = strDados.split('|')
            
            $(`#td_desc_${cont}`).text(arrDados[0])
            $(`#td_val_${cont}`).html(`<center>${arrDados[2]}</center>`)
        
            if( !setouData ){
                $('#h3-move-informe').html(`<i>Data: ${arrDados[1]}</i>`)
                pesquisarHistoricoInforme( arrDados[1] )
                setouData = true
            }

            cont += 1

            if( cont % 4 == 0 ) {
                cont    = 0
                
                setTimeout( function(){
                    somaTotal()
                },250)       
                
            }

        }
    
    }catch(e){
        
        var htmlDataLimite = $('#h3-move-informe').html()

        if( htmlDataLimite.indexOf('Limite') == -1 ){
            $('#h3-move-informe').html(`${htmlDataLimite}<i> - Data Limite...</i>`)
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

    var totalFormatado = formatCurrency(total)

    $('#td_total').html(`<center>${totalFormatado}</center>`)
}