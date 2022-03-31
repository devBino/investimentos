$(function(){
    
    $('#descricao').change(function(){
        setTimeout(function(){
            $('#valor').focus()
        },300)
    })

    $('.pesquisa-dia').click(async function(){

        $('#tbody-informes').html('');

        var data = $(this).attr('data-dia')

        var informes = await promiseAjax('/informe-data','POST',{
            '_token':$('#tkn').val(),
            'data':data
        });
        
        if( informes.data != undefined && informes.data.informes != undefined ){

            var arrDadosData = data.split('-');
            $('#sp-data').html(`${arrDadosData[2]}/${arrDadosData[1]}/${arrDadosData[0]}`);

            var htmlInformes = '';

            informes.data.informes.forEach((informe)=>{
                
                let htmlRow = '<tr>';

                htmlRow = `${htmlRow}<td>`;
                htmlRow = `${htmlRow}${informe.cdInforme}`;
                htmlRow = `${htmlRow}</td>`;

                htmlRow = `${htmlRow}<td>`;
                htmlRow = `${htmlRow}${informe.descricao}`;
                htmlRow = `${htmlRow}</td>`;

                htmlRow = `${htmlRow}<td>`;
                htmlRow = `${htmlRow}${ parseFloat(informe.valor).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}) }`;
                htmlRow = `${htmlRow}</td>`;

                htmlRow = `${htmlRow}</tr>`;

                htmlInformes = `${htmlInformes}${htmlRow}`;

            });

            $('#tbody-informes').html(htmlInformes);

            $('#modal').show();
        }else{
            alert("Não foram encontrados informes para a data selecionada...");
        }

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

    $('#btFechaModal').click(function(){
        $('#modal').hide()
    })

    configsInicio()
    
})