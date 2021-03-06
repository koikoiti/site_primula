<?php
	#include das funcoes da tela 
	include('functions/banco-venda.php');
	
	$busca_dataIni = date("Y-m-d", strtotime("-7 day"));
	$busca_dataFim = date("Y-m-d");
	
	#Instancia o objeto
	$banco = new bancovenda();
    
    $pagina = 1;
    
    if($_GET){
    	$busca_nome = utf8_decode($_GET['busca_nome']);
    	$busca_cnpj = $_GET['busca_cnpj'];
    	$busca_cpf = $_GET['busca_cpf'];
    	$busca_dataIni = $_GET['busca_dataIni'];
    	$busca_dataFim = $_GET['busca_dataFim'];
    	$busca_venda = ltrim($_GET['busca_venda'], 0);
    	$busca_responsavel = $_GET['busca_responsavel'];
    	$busca_pagamento = $_GET['busca_pagamento'];
    	$busca_procedencia= $_GET['busca_procedencia'];
    	if($_GET['page']){
    		$pagina = $_GET['page'];
    	}else{
    		$pagina = 1;
    	}
    }
    
    $Vendas = $banco->ListaVendas($busca_nome, $busca_cnpj, $busca_cpf, $busca_venda, $busca_dataIni, $busca_dataFim, $busca_responsavel, $busca_pagamento, $busca_procedencia);
    $select_usuarios = $banco->MontaUsuarios($busca_responsavel);
    $select_pagamentos = $banco->MontaPagamentos($busca_pagamento);
    $select_procedencia = $banco->MontaProcedencia($busca_procedencia);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Vendas/lista-venda'));
    $Conteudo = str_replace("<%VENDAS%>", $Vendas, $Conteudo);
    $Conteudo = str_replace("<%BUSCAVENDA%>", $busca_venda, $Conteudo);
    $Conteudo = str_replace("<%BUSCACLIENTE%>", $busca_nome, $Conteudo);
    $Conteudo = str_replace("<%BUSCACNPJ%>", $busca_cnpj, $Conteudo);
    $Conteudo = str_replace("<%BUSCACPF%>", $busca_cpf, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAINI%>", $busca_dataIni, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAFIM%>", $busca_dataFim, $Conteudo);
    $Conteudo = str_replace("<%SELECTUSUARIOS%>", $select_usuarios, $Conteudo);
    $Conteudo = str_replace("<%SELECTPAGAMENTOS%>", $select_pagamentos, $Conteudo);
    $Conteudo = str_replace("<%SELECTPROCEDENCIA%>", $select_procedencia, $Conteudo);
?>