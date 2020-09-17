$(function(){
    $('#fecha-alert').click(function(){
        $('#div-alert').hide()
    })

    $('.limpar_form').click(function(){
        if(confirm('Deseja limpar o formulário??')){
            window.location.href = ''
        }
    })

    $('.visualiza_registros').click(function(){
        try{
            var dataRow = $(this).attr('data-linha')
            $(`.${dataRow}`).fadeToggle()
        }catch(e){
            console.error(e)
        }
    })
})

function configsInicio(){
    try{
        
        setTimeout(function(){
            estilizaSelects()
        },500)
        
    }catch(e){
        console.error(e)
    }
}

function send_ajax(url=null, type=null, data=null, callback=null){
    try{
        if( url != null && type != null && data != null && callback != null ){
            $.ajax({
                url: url,
                type: type,
                data: data,
                success:function(dados){
                    console.log(dados)
                    callback(dados)
                },
                error(e){
                    console.error(e)
                }
            })
        }
    }catch(e){}
}

function selectTodos(id, eventoChange = true){
    try{
        var options = $(`#${id} option`)
        var dados = []

        for(const opt of options){
            var valor = $(opt).val()
            
            if( valor != undefined && valor != null && valor != '' ){
                dados.push(valor)
            }
        }

        if( !eventoChange ){
            $(`#${id}`).val(dados)
        }else{
            $(`#${id}`).val(dados).trigger('change')
        }

    }catch(e){
        console.error(e)
    }
}

function verVazios(classe){
    try{
        var vazio = false
        var campos = $(`.${classe}`)
        var id

        for( const campo of campos ){
            if(!vazio){
                var v = $(campo).val()
                
                if( !(v != undefined && v != null && v != "") ){
                    id = $(campo).attr('id')
                    vazio = true
                }
            }
            
        }

        var objReturn = {vazio:vazio, id:id}

        return objReturn

    }catch(e){}
}

function estilizaSelects(){
    try{
        var campos = $('select')
        
        for( const campo of campos ){
            $(campo).addClass('class-select')
            $(campo).select2()
        }

    }catch(e){
        console.log(e)
    }
}

function copiarTexto(idElemento){
    try{
        
        var txtCopy = document.getElementById(idElemento)
        txtCopy.select()
        document.execCommand("Copy")
        
    }catch(e){
        console.error(e)
    }
}

function maiusculas(str){
    return str.toUpperCase();
}

function trataDataBanco( strDt = '31-12-1969', fmtBrasil = false ){
    try{
        var dt      = strDt.substr(0,10)
        var spDt    = dt.split('-')
        
        if( fmtBrasil ){
            dt = spDt = `${spDt[2]}-${spDt[1]}-${spDt[0]}`
        }else{
            dt = spDt = `${spDt[0]}-${spDt[1]}-${spDt[2]}`
        }
        
        dt = (dt == '31-12-1969') ? '' : dt

        return dt
    }catch(e){
        console.error(e)
        return ''
    }
}

function printNovaJanela(html,nomeJanela){
    var novaJanela = window.open(nomeJanela,'_blank','width=1100,height=700,toolbar=no,scrollbars=1,resizable=no,top=30,left=80')
    novaJanela.document.write(html)
}

function limpaForm( idForm, idFocus ){
    try{
        var inputs = $(`#${idForm} input`)

        for( const input of inputs ){
            if( $(input).attr('type') != 'hidden' ){
                $(input).val('').trigger('change')
            }
        }

        var selects = $(`#${idForm} select`)

        for( const select of selects ){
            $(select).val('').trigger('change')
        }

        $(`#${idFocus}`).focus()

    }catch(e){
        console.error(e)
    }
}

function strNuloOuVazio( strVal ){

    var vazio = true

    if( strVal != '' && strVal != undefined && strVal != null ){
        vazio = false
    }

    return vazio

}

function msgAlert(msg = 'Erro ao tentar concluir ação...', tipoClass = 'div-danger', tempo = 5000){
    try{
        
        $('#div-alert').show()

        $('#div-alert').attr('class', tipoClass)
        $('#p-alert').html(msg)

        setTimeout(function(){
            $('#div-alert').hide()
        },tempo)
    }catch(e){
        console.error(e)
    }
}

function recebePrompt(msgChamada = 'Por favor, informe um valor: '){
    txtInput = ''

    try{
        while( strNuloOuVazio(txtInput) ){
            txtInput = prompt(msgChamada)
        }
    }catch(e){
        console.error(e)
    }

    return txtInput
}