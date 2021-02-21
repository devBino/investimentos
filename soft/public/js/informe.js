var indiceVelocidadeApresentacao    = 4
var arrVelocidades                  = []
var statusApresentacao  = true
var tempoMaximoPausado  = 1000
var tempoPausado        = 0

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
        }else if( move == '-1' ){
            marcadorMenosMenos()
        }else if( move == '0' ){
            statusApresentacao = true
            apresentar( $('#marcadorFixo').val() )
        }else if( move == '3' ){
            statusApresentacao = false
        }
    })

    $('#velocidade').change(function(){
        indiceVelocidadeApresentacao = $(this).val()
    })

    
    configsInicio()
    setVelocidades()

    indiceVelocidadeApresentacao    = $('#velocidade').val()
    statusApresentacao              = true

})

function setVelocidades(){

    var multiplicador = 0
    var min = $('#velocidade').attr('min')
    var max = $('#velocidade').attr('max')

    var velocidadeMaxima = parseInt(max) * 250

    //seta todas as posições do arrVelocidades como zeradas
    for( var i=parseInt(min); i<parseInt(max); i++ ){
        arrVelocidades.push(0)
    }

    //percorre a estrutura novamente, setando as velocidades mais rapidas na decrescente
    for( var i=parseInt(max); i>=parseInt(min); i-- ){
        if( i == 0 ){
            arrVelocidades[i] = velocidadeMaxima
        }else{
            arrVelocidades[i] = multiplicador * 250
        }
        
        multiplicador += 1
    }

}

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

async function apresentar( contadorControle ){

    try{
        
        if( !statusApresentacao ){
            msgAlert('A apresentação foi interrompida...')
            return false
        }

        var marcadorControle = contadorControle

        if( marcadorControle > 0 ){
            
            //prepara variaveis
            var rowGrupo    = document.querySelectorAll(`.linha_grupo_${contadorControle}`)
            var strDados    = $(rowGrupo[0]).attr('data-dados')
            var arrDados    = strDados.split('|')
            var data        = arrDados[1]

            //atualiza tabela de dados de informe da data iterada
            var setouData   = false
            var cont        = 0

            for( var i=0; i<5; i++ ){
            
                var strDados = $(rowGrupo[i]).attr('data-dados')
                var arrDados = strDados.split('|')
                
                $(`#td_desc_${cont}`).text(arrDados[0])
                $(`#td_val_${cont}`).html(`<center>${arrDados[2]}</center>`)
            
                if( !setouData ){
                    $('#h3-move-informe').html(`<i>Data: ${arrDados[1]}</i>`)                    
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

            //busca os dados e aplica html
            var params = {
                _token:$('#tkn').val(),
                data:data
            }
    
            var dadosHistorico = await promiseAjax('/informe-busca-dados','post',params)
            
            if( dadosHistorico.data === undefined || dadosHistorico.error ){
                msgAlert('Ocorreu um erro durante a apresentação...')
                return false
            }
            
            $('#containerGrafico').html(dadosHistorico.data.html)
            $('#h3-move-informe').html(`Data: ${data}`)
            
            //faz a chamada recursiva
            setTimeout( function(){
                marcadorControle -= 1
                apresentar( marcadorControle )
            }, arrVelocidades[ indiceVelocidadeApresentacao ] )

        }else{
            return false
        }

    }catch(e){
        console.error(e)
        return false
    }
    
}