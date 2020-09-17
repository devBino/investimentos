@extends('template.app')
@section('telas')
    
    <div class="row box-title">
        <div class="col-sm-12">
            <h6 class="text text-secondary">Erro...</h6>
        </div>
    </div>

    <br><br><br>

    <center>
        <div class="alert-danger" style="width: 350px; padding:10px; border-radius:10px;">
            <div class="row">
                <div class="col-sm-9">
                    <p>Acesso Negado!!</p>
                </div>
                <div class="col-sm-3 d-flex justify-content-end">
                    <i class="fas fa-lock" style="font-size: 30pt;"></i>
                </div>
            </div>
        </div>
    </center>

@stop