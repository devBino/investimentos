@extends('template.sistema')
@section('programa')

<script>
    setTimeout(function(){
        window.print(document)
        window.close()
    },500)
</script>

<div class="row box-title">
    <div class="col-sm-12">
        <h6 class="text text-secondary"><HTML_TITULO></h6>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="borda">
            <HTML_TABELA>
        </div>
    </div>
</div>

@stop