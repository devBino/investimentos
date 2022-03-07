@extends('template.sistema')

@section('programa')

<div class="row">
    <div class="col-sm-1 menu-lateral bg-dark">
        <nav>
            <!-- Sidebar -->
            <ul class="navbar-nav gradient-bg-azul sidebar sidebar-dark accordion" id="accordionSidebar">
                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                </a>
                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                <!-- Nav Item - Painel -->
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard">
                    <span>Dashboad</span></a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="caixa">
                    <span>Caixa</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="papel">
                    <span>Papel</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="alvo">
                    <span>Alvos</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="aporte">
                    <span>Aportes</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="resgate">
                    <span>Resgates</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="provento">
                    <span>Proventos</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="informe">
                    <span>Informe</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="rendimentos">
                    <span>IR</span></a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) 
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>-->

            </ul>

        </nav>

    </div>
    <div class="col-sm-11">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content"> 
                
                <nav id="nav-topo" class="navbar navbar-expand navbar-light bg-dark gradient-bg-azul topbar static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <li class="nav-item dropdown no-arrow">
                        <a  class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" 
                        aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text text-light small">Opções</span>
                        <!--<img class="img-profile rounded-circle" src="">-->
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a id="calculadora" class="dropdown-item" href="#">
                                <i class="fas fa-calculator fa-sm fa-fw mr-2"></i>
                                Calculadora
                            </a>
                        
                        <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                Sair
                            </a>
                        </div>
                    </li>

                    </ul>

                    </nav>

                <div class="container-fluid">

                    @include('template.alert')

                    @yield('telas')
                </div>
            </div>
        </div>
    </div>
</div>

@stop