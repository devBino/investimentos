var url = 'http://ead.portalava.com.br/administrador/avaliacao/criar-sistema-avaliacao/cadastrar/'
var min = 1
var max = 232
var corpoTabela = ''
var htmlTabela = ''

function getHtml(numConfig=1){
    return new Promise((resolve,reject)=>{
        try{
            $.ajax({
                url: `${url}${numConfig}`,
                type: 'GET',
                success: function(res) {
                    resolve(res)
                },
                error:function(e){
                    reject(undefined)
                }
            })
        }catch(e){
            console.error(e)
            reject(undefined)
        }  
    })
}

async function startGame(numConfig=1){
    
    var contNumConfig = numConfig

    console.log(`Verificando sistema de avaliação ${numConfig}`)
    console.log('------------------------------------------------------------------')

    if( contNumConfig < max ){
        var dadosHtml = await getHtml(contNumConfig);
    
        var descricao       = getDescricao(dadosHtml)
        var substitutiva    = verDisabled(dadosHtml,'substitutiva')
        var avaliacaoUnica  = verDisabled(dadosHtml,'avaliacao_unica')
        var refazer         = verDisabled(dadosHtml,'refazer')

        contNumConfig += 1
        
        var rowHtmlTabela = montaRowCorpoTabela(numConfig,descricao,substitutiva,avaliacaoUnica,refazer)
        
        corpoTabela = `${corpoTabela}${rowHtmlTabela}`

        startGame(contNumConfig)
    }else{
        setHtmlTabela()
    }

}

function montaRowCorpoTabela(numConfig,descricao,substitutiva,avaliacaoUnica,refazer){
    
    if( descricao === '' || descricao === undefined ||descricao === null ){
        descricao = 'Sistema não configurado...'
        
        var strSubstitutiva     = 'Checkbox inexistente...'
        var strAvaliacaoUnica   = 'Checkbox inexistente...'
        var strRefazer          = 'Checkbox inexistente...'

        var classSubstitutiva     = 'ckInexistente'
        var classAvaliacaoUnica   = 'ckInexistente'
        var classRefazer          = 'ckInexistente'

    }else{

        var strSubstitutiva     = (!substitutiva) ? 'Liberado para Checar' : 'Bloqueado para Checar'
        var strAvaliacaoUnica   = (!avaliacaoUnica) ? 'Liberado para Checar' : 'Bloqueado para Checar'
        var strRefazer          = (!refazer) ? 'Liberado para Checar' : 'Bloqueado para Checar'

        var classSubstitutiva     = (!substitutiva) ? 'ckLiberado' : 'ckBloqueado'
        var classAvaliacaoUnica   = (!avaliacaoUnica) ? 'ckLiberado' : 'ckBloqueado'
        var classRefazer          = (!refazer) ? 'ckLiberado' : 'ckBloqueado'
    }

    

    var rowTabela = `
        <tr>
            <td>${numConfig}</td>
            <td>${descricao}</td>
            <td class="${classSubstitutiva}">${strSubstitutiva}</td>
            <td class="${classAvaliacaoUnica}">${strAvaliacaoUnica}</td>
            <td class="${classRefazer}">${strRefazer}</td>
        </tr>
    `

    return rowTabela
}

function verDisabled(dadosHtml,idCheck){
    var posicaoId   = dadosHtml.indexOf(`id="${idCheck}"`)
    var strTrecho   = dadosHtml.substr(posicaoId,180)
    
    if( strTrecho.indexOf('disabled') !== -1 ){
        return true
    }else{
        return false
    }
}

function getDescricao(dadosHtml){
    var posicaoCampo    = dadosHtml.indexOf('id="desc_avaliacao"')
    var strTrecho       = dadosHtml.substr(posicaoCampo,500)
    var posicaoValue    = strTrecho.indexOf('value="')
    var strValue        = strTrecho.substr(posicaoValue+7,400)
    var arrStrValue     = strValue.split('"')

    return arrStrValue[0]
}

function setHtmlTabela(){
    htmlTabela = `
        <table id="tblAvaliacoes">
            <thead>
                <tr>
                    <th>Avaliação</th>
                    <th>Descricao</th>
                    <th>Chec Substitutiva</th>
                    <th>Chec Avaliação Única</th>
                    <th>Chec Refazer Disciplina</th>
                </tr>
            </thead>
            <tbody>
                ${corpoTabela}
            </tbody>
        </table>
    `
    
    document.body.innerHTML=''
    document.body.innerHTML=`
        <h1>ABSTRAÇÃO DE PADRÃO DE BLOQUEIOS NOS CHECKBOX DA TELA DE CONFIG SISTEMA AVALIAÇÕES <button onclick="print(document);">IMPRIMIR</button></h1><hr>
        ${htmlTabela}
    `   

    setCssHtml()

}

function setCssHtml(){
    $('#tblAvaliacoes').css({
        marginTop:'20px',
        marginLeft:'30px',
        marginRight:'20px',
        width:'92%',
        border:'1px solid #dddddd',
        borderRadius:'5px',
        backgroundColor:'#f1f1f1'
    })
    
    $('#tblAvaliacoes th').css({
        border:'1px solid #dddddd',
        textAlign:'center'
    })

    $('#tblAvaliacoes td').css({
        border:'1px solid #dddddd',
        textAlign:'center'
    })

    $('body').css({
        backgroundColor:'#dddddd'
    })

    $('h1').css({
        fontSize:'14pt',
        textAlign:'center'
    })

    $('.ckLiberado').css({
        color:'green'
    })
    $('.ckBloqueado').css({
        color:'red'
    })
    $('.ckInexistente').css({
        color:'orange'
    })
}