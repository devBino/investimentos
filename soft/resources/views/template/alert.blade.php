@if( session('status') )

    @php
        $arrStatus = explode("|", session('status'));
    @endphp


    <div id="div-alert" class="div-{{$arrStatus[1]}}">
        <div class="row">
            <div class="col-sm-10">
                <p id="p-alert">{{ $arrStatus[0] }}</p>
            </div>
            <div class="col-sm-2 d-flex justify-content-end">
                <span id="fecha-alert"><i class="fas fa-times-circle"></i></span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function(){
            $('#div-alert').hide()
        },5000)
    </script>

@else

    <div id="div-alert" class="div-success">
        <div class="row">
            <div class="col-sm-10">
                <p id="p-alert">Olá, seja bem vindo...</p>
            </div>
            <div class="col-sm-2 d-flex justify-content-end">
                <span id="fecha-alert"><i class="fas fa-times-circle"></i></span>
            </div>
        </div>
    </div>
    <script>
        $('#div-alert').hide()
    </script>

@endif