<?php
	#include das funcoes da tela 
	include('functions/banco-ultima-venda.php');
	
	$botao_limpar = '';
	$mes = 1;
	
	#Instancia o objeto
	$banco = new bancoultimavenda();
    
    if($_GET){
    	$mes = $_GET['mes'];
    	$botao_limpar = '<a href="'.UrlPadrao.'lista-ultima-venda" class="btn btn-danger"><i class="fa fa-times"></i></a>';
    }
    
    $lista_ultimas_vendas = $banco->ListaUltimasVendas($mes);
    $select_periodo = $banco->MontaSelectPeriodo($mes);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-ultima-venda'));
	$Conteudo = str_replace("<%ULTIMASVENDAS%>", $lista_ultimas_vendas, $Conteudo);
	$Conteudo = str_replace("<%SELECTPERIODO%>", $select_periodo, $Conteudo);
    $Conteudo = str_replace("<%BOTAOLIMPARFILTRO%>", $botao_limpar, $Conteudo);
?>