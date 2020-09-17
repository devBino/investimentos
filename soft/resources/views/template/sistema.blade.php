<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @include('head')

    <title>BinoInvest</title>
</head>

<body id="page-top" class="bg-gradient-primary">
    <div id="wrapper">
        
        @include('template.calculadora')
        
        @yield('programa')        
    </div>
</body>

</html>