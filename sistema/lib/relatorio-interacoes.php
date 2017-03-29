<?php
	#include das funcoes da tela
	include('functions/banco-relatorio-interacoes.php');
	
	$dataIni = date("Y-m-01");
	$dataFim = date("Y-m-d");
	$botao_limpar = '';

	#Instancia o objeto
	$banco = new bancorelatoriointeracoes();
	
	if($_GET){
		$dataIni = $_GET['dataIni'];
		$dataFim = $_GET['dataFim'];
		$idresponsavel = $_GET['busca_responsavel'];
		$botao_limpar = '<a href="'.UrlPadrao.'relatorio-interacoes" class="btn btn-danger"><i class="fa fa-times"></i></a>';
	}
	
	$Relatorio = $banco->MontaRelatorioInteracoes($dataIni, $dataFim, $idresponsavel);
	
	$select_usuarios = $banco->MontaUsuarios($idresponsavel);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('relatorio-interacoes'));
	$Conteudo = str_replace("<%RELATORIO%>", $Relatorio, $Conteudo);
	$Conteudo = str_replace("<%SELECTUSUARIOS%>", $select_usuarios, $Conteudo);
	$Conteudo = str_replace("<%BUSCADATAINI%>", $dataIni, $Conteudo);
	$Conteudo = str_replace("<%BUSCADATAFIM%>", $dataFim, $Conteudo);
	$Conteudo = str_replace("<%BOTAOLIMPARFILTRO%>", $botao_limpar, $Conteudo);
?>