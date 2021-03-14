<?php

use Illuminate\Support\Facades\Route;

//HOME
Route::get("/","Home@home");
Route::post("/home-login","Home@login");

//SISTEMA
Route::get('/sistema','Home@sistema')->middleware(['IsOk']);

//CAIXA
Route::get('/caixa','Caixa@index')->middleware(['IsOk']);
Route::post('/caixa-salvar','Caixa@salvar')->middleware(['IsOk']);
Route::get('/caixa-deletar/{id}','Caixa@deletar')->middleware(['IsOk']);
Route::post('/caixa-pesquisar','Caixa@pesquisar')->middleware(['IsOk']);
Route::get('/caixa-pesquisar','Caixa@index')->middleware(['IsOk']);

//PAPEL
Route::get('/papel','Papel@index')->middleware(['IsOk']);
Route::post('/papel-salvar','Papel@salvar')->middleware(['IsOk']);
Route::get('/papel-deletar/{id}','Papel@deletar')->middleware(['IsOk']);
Route::post('/papel-atualiza-cotacao','Papel@atualizaCotacoes')->middleware(['IsOk']);
Route::get('/papel-atualiza-cotacao-manual/{papel}/{cotacao}','Papel@atualizaCotacaoManual')->middleware(['IsOk']);
Route::post('/papel-pesquisar','Papel@pesquisar')->middleware(['IsOk']);
Route::get('/papel-pesquisar','Papel@index')->middleware(['IsOk']);

//ALVOS
Route::get('/alvo','Alvo@index')->middleware(['IsOk']);
Route::post('/alvo-salvar','Alvo@salvar')->middleware(['IsOk']);
Route::get('/alvo-deletar/{chave}','Alvo@deletar')->middleware(['IsOk']);
Route::get('/alvo-ordenar','Alvo@pesquisaOrdenar')->middleware(['IsOk']);
Route::post('/alvo-ordenar','Alvo@pesquisaOrdenar')->middleware(['IsOk']);

//APORTES
Route::get('/aporte','Aporte@index')->middleware(['IsOk']);
Route::post('/aporte-salvar','Aporte@salvar')->middleware(['IsOk']);
Route::get('/aporte-deletar/{id}','Aporte@deletar')->middleware(['IsOk']);
Route::post('/aporte-pesquisar','Aporte@pesquisar')->middleware(['IsOk']);
Route::get('/aporte-pesquisar','Aporte@index')->middleware(['IsOk']);

//RESGATES
Route::get('/resgate','Resgate@index')->middleware(['IsOk']);
Route::get('resgate-pesquisa','Resgate@index')->middleware(['IsOk']);
Route::post('/resgate-pesquisa','Resgate@pesquisar')->middleware(['IsOk']);
Route::get('/resgate-aporte/{aporte}','Resgate@resgatarAporte')->middleware(['IsOk']);
Route::get('/resgate-aporte/{aporte}/{cotacao}','Resgate@resgatarAporte')->middleware(['IsOk']);
Route::post('/resgate-aportes','Resgate@resgatarAportes')->middleware(['IsOk']);

//PROVENTOS
Route::get('/provento','Provento@index')->middleware(['IsOk']);
Route::post('/provento-salvar','Provento@salvar')->middleware(['IsOk']);
Route::get('/provento-deletar/{id}','Provento@deletar')->middleware(['IsOk']);
Route::post('/provento-pesquisar','Provento@pesquisar')->middleware(['IsOk']);

//PROVENTOS MENSAIS
Route::get('/provento-mensal','Provento@proventosMensais')->middleware(['IsOk']);
Route::post('/provento-mensal','Provento@proventosMensais')->middleware(['IsOk']);
Route::get('/provento-papel','Provento@proventosPapeis')->middleware(['IsOk']);
Route::post('/provento-papel','Provento@proventosPapeis')->middleware(['IsOk']);

//DASHBOARD
Route::get('/dashboard','Dashboard@index')->middleware(['IsOk']);
Route::post('/dashboard-permissao-valores','Dashboard@permissaoValores')->middleware(['IsOk']);


//INFORME
Route::get('/informe','Informe@index')->middleware(['IsOk']);
Route::post('/informe-salvar','Informe@salvar')->middleware(['IsOk']);
Route::get('/informe-deletar/{id}','Informe@deletar')->middleware(['IsOk']);
Route::post('/informe-busca-dados','Informe@pesquisarHistoricoInforme')->middleware(['IsOk']);


//COTAÇÕES
Route::get('/cotacao','Cotacao@index')->middleware(['IsOk']);