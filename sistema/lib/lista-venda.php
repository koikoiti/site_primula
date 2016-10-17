<?php
	#include das funcoes da tela 
	include('functions/banco-venda.php');

	#Instancia o objeto
	$banco = new bancovenda();
    
    $pagina = 1;
    
    if($_GET){
    	$busca_nome = utf8_decode($_GET['busca_nome']);
    	$busca_cnpj = $_GET['busca_cnpj'];
    	$busca_cpf = $_GET['busca_cpf'];
    	$busca_venda = ltrim($_GET['busca_venda'], 0);
    	if($_GET['page']){
    		$pagina = $_GET['page'];
    	}else{
    		$pagina = 1;
    	}
    }
    
    $Vendas = $banco->ListaVendas($busca_nome, $busca_cnpj, $busca_cpf, $busca_venda);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Vendas/lista-venda'));
    $Conteudo = str_replace("<%VENDAS%>", $Vendas, $Conteudo);
    $Conteudo = str_replace("<%BUSCAVENDA%>", $busca_venda, $Conteudo);
    $Conteudo = str_replace("<%BUSCACLIENTE%>", $busca_nome, $Conteudo);
    $Conteudo = str_replace("<%BUSCACNPJ%>", $busca_cnpj, $Conteudo);
    $Conteudo = str_replace("<%BUSCACPF%>", $busca_cpf, $Conteudo);
?>