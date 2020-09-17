var calculadoraOpcoes = {
    sinal:'',
    numero1:'',
    numero2:'',
    resultado:0
}

$(function(){

    $('#calculadora').click(function(){
        limparCalculadora()
        $('#modalCalculadora').show()
    })

    $('#btFechaModalCalculadora').click(function(){
        limparCalculadora()
        $('#modalCalculadora').hide()
    })

    $('.digitos').click(function(){
        try{
            var numeroAtual = strNuloOuVazio(calculadoraOpcoes.sinal) ? calculadoraOpcoes.numero1 : calculadoraOpcoes.numero2
            var digito      = $(this).attr('data-digito')

            if( digito == '.' && numeroAtual.indexOf('.') != -1 ){
                return false
            }

            numeroAtual = `${numeroAtual}${digito}`

            if( strNuloOuVazio(calculadoraOpcoes.sinal) ){
                calculadoraOpcoes.numero1 = numeroAtual
            }else{
                calculadoraOpcoes.numero2 = numeroAtual
            }

            $('#resultado').val(parseFloat(numeroAtual))

        }catch(e){
            console.error(e)
        }
    })

    $('.sinal').click(function(){
        try{
            
            var resultado = $('#resultado').val()

            if( strNuloOuVazio(calculadoraOpcoes.numero1) && ( strNuloOuVazio(resultado) || parseFloat(resultado) == 0.00 ) ){
                return false
            }
            
            if( strNuloOuVazio(calculadoraOpcoes.numero1) ){
                calculadoraOpcoes.numero1 = resultado
                calculadoraOpcoes.sinal = $(this).attr('data-sinal')
            }else if( strNuloOuVazio(calculadoraOpcoes.numero2) ){
                calculadoraOpcoes.sinal = $(this).attr('data-sinal')
            }else if( !strNuloOuVazio(calculadoraOpcoes.numero2) ){
                calculadoraOpcoes.sinal = $(this).attr('data-sinal')
            }

            calculo()

        }catch(e){
            console.error(e)
        }
    })

    $('#btIgual').click(function(){
        try{
            calculo()
        }catch(e){
            console.error(e)
        }
    })

    $('#btCopiar').click(function(){
        copiarTexto('resultado')
    })

    $('#btLimparCalculadora').click(function(){
        try{
            
            limparCalculadora()

        }catch(e){
            console.error(e)
        }
    })

})

function limparCalculadora(){
 
    calculadoraOpcoes.sinal     = ''
    calculadoraOpcoes.numero1   = ''
    calculadoraOpcoes.numero2   = ''
    calculadoraOpcoes.resultado = 0

    setTimeout(function(){
        $('#resultado').val(calculadoraOpcoes.resultado)
    },50)
    
}

function calculo(){
    try{

        if( strNuloOuVazio(`${calculadoraOpcoes.sinal}`) || strNuloOuVazio(`${calculadoraOpcoes.numero1}`) || strNuloOuVazio(`${calculadoraOpcoes.numero2}`) ){
            return false                
        }

        switch( calculadoraOpcoes.sinal ){
            case '+':
                calculadoraOpcoes.resultado = parseFloat(calculadoraOpcoes.numero1) + parseFloat(calculadoraOpcoes.numero2)
                break;
            case '-':
                calculadoraOpcoes.resultado = parseFloat(calculadoraOpcoes.numero1) - parseFloat(calculadoraOpcoes.numero2)
                break;
            case '*':
                calculadoraOpcoes.resultado = parseFloat(calculadoraOpcoes.numero1) * parseFloat(calculadoraOpcoes.numero2)
                break;
            case '/':
                calculadoraOpcoes.resultado = parseFloat(calculadoraOpcoes.numero1) / parseFloat(calculadoraOpcoes.numero2)
                break;
            case 'pot':
                calculadoraOpcoes.resultado = Math.pow( parseFloat(calculadoraOpcoes.numero1),parseFloat(calculadoraOpcoes.numero2) )
                break;
            default:
                break;
        }

        $('#resultado').val(calculadoraOpcoes.resultado.toFixed(3))

        calculadoraOpcoes.sinal     = ''
        calculadoraOpcoes.numero1   = ''
        calculadoraOpcoes.numero2   = ''

    }catch(e){
        console.error(e)
    }
}