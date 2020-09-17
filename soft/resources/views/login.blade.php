<div class="container">
    
    <div class="row justify-content-center">

      <div class="col-sm-12">

        <div class="card o-hidden border-0 shadow-lg my-5">

          <div class="card-body p-0">
    
            <div class="row">
                <div class="col-lg-6">
                  <div class="p-5">
                    <div class="text-center">
                      <h1 class="h4 text-gray-900 mb-4">Credencias de Acesso</h1>
                    </div>

                    <form id="fr-login" action="/home-login" method="post" class="borda p-5">
                
                        <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                        
                        <div class="row">
                            <div class="col-sm-3 form-group d-flex justify-content-end">
                                <label>Usuário</label>
                            </div>
                            <div class="col-sm-6 form-group d-flex justify-content-start">
                                <input type="text" name="usuario" class="form-control form-control-sm" required autocomplete="off" autofocus="true">
                            </div>
                            <div class="col-sm-3 d-flex justify-content-start">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-3 form-group d-flex justify-content-end">
                                <label>Senha</label>
                            </div>
                            <div class="col-sm-6 form-group d-flex justify-content-start">
                                <input type="password" name="senha" class="form-control form-control-sm" required autocomplete="off">
                            </div>
                            <div class="col-sm-3 d-flex justify-content-start">
                                <button class="btn btn-secondary btn-sm bt-form">Login</button>
                            </div>
                        </div>

                    </form>

                  </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-5">
                        <div class="card card-info">
                            <div class="card card-header">
                                <p><b>Seu Gerenciador de Investimentos Inteligente</b></p>
                            </div>
                            <div class="card card-body">
                                <ul>
                                    <li>Cadastre tipos de investimentos, papeis, aportes, resgates e proventos;</li>
                                    <li>Atualiza Cotações de Ações consumindo API;</li>
                                    <li>Controle seus Aportes;</li>
                                    <li>Cadastre Preço Alvo para aportes;</li>
                                    <li>Controle seu Preço Médio;</li>
                                    <li>Acompanhe a evolução dos proventos Mensais;</li>
                                    <li>DashBoard Estratégico, simplifica composição da carteira</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

  @if( isset($msg) && !empty($msg) )
    <div id="aviso">
        <br>

        <div class="row">
            <div class="col-sm-12">
                <div class="alert-danger">
                    <p align="center">Login Inválido!!</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function(){
            $('#aviso').hide()
        },3000)
    </script>
@endif