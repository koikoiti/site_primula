<?php
	#include das funcoes da tela
	include('functions/banco-relatorio-cliente.php');
	
	$dataIni = date("Y-m-01");
	$dataFim = date("Y-m-d");
	$botao_limpar = '';
	
	#Instancia o objeto
	$banco = new bancorelatoriocliente();
	
	if($_GET){
		$dataIni = $_GET['dataIni'];
		$dataFim = $_GET['dataFim'];
		$idresponsavel = $_GET['busca_responsavel'];
		$marca = $_GET['busca_marca'];
		$botao_limpar = '<a href="'.UrlPadrao.'relatorios" class="btn btn-danger"><i class="fa fa-times"></i></a>';
	}
	
	$Relatorio = $banco->MontaRelatorio($dataIni, $dataFim, $idresponsavel, $marca);
	$select_usuarios = $banco->MontaUsuarios($idresponsavel);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('relatorio-cliente'));
	$Conteudo = str_replace("<%RELATORIO%>", $Relatorio, $Conteudo);
	$Conteudo = str_replace("<%SELECTUSUARIOS%>", $select_usuarios, $Conteudo);
	$Conteudo = str_replace("<%BUSCADATAINI%>", $dataIni, $Conteudo);
	$Conteudo = str_replace("<%BUSCADATAFIM%>", $dataFim, $Conteudo);
	$Conteudo = str_replace("<%BUSCAMARCA%>", $marca, $Conteudo);
	$Conteudo = str_replace("<%BOTAOLIMPARFILTRO%>", $botao_limpar, $Conteudo);
?>