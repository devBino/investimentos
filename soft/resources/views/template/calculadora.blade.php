<div id="modalCalculadora" class="modal-calculadora">
    <div class="panel-modal-calculadora">
        <div class="row modal-header pb-0 pt-0 ">
            <div class="col">
                <h5 class="text text-info">Calculadora</h5>
            </div>
        </div>
        <div class="row modal-body">
            
            <div class="col-sm-12">
                
                <div class="row mb-2">
                    <div class="col-sm-12 d-flex justify-content-center">
                        <input type="text" id="resultado" class="form-control form-control-sm" readonly>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="1"><b>1</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="2"><b>2</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="3"><b>3</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-info btn-sm sinal" data-sinal="+"><b>+</b></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="4"><b>4</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="5"><b>5</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="6"><b>6</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-info btn-sm sinal" data-sinal="-"><b>-</b></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="7"><b>7</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="8"><b>8</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="9"><b>9</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-info btn-sm sinal" data-sinal="*"><b>*</b></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="0"><b>0</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-secondary btn-sm digitos" data-digito="."><b>.</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-info btn-sm sinal" data-sinal="pot"><b>Pôt</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button class="btn btn-info btn-sm sinal" data-sinal="/"><b>/</b></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button id="btFechaModalCalculadora" class="btn btn-danger btn-sm"><b>Off</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button id="btIgual" class="btn btn-info btn-sm"><b>=</b></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button id="btCopiar" class="btn btn-info btn-sm"><i class="fas fa-copy"></i></button>
                    </div>
                    <div class="col-sm-3 m-0 mt-1 mb-1">
                        <button id="btLimparCalculadora" class="btn btn-success btn-sm" data-digito="C"><b>C</b></button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="{{ asset('/js/calculadora.js') }}"></script>