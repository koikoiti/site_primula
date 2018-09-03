<?php
	#include das funcoes da tela
	include('functions/banco-relatorio-produtos-vendidos.php');
	
	$dataIni = date("Y-m-01");
	$dataFim = date("Y-m-d");
	$buscaProdutoMarca = "";
	$botao_limpar = '';
	
	#Instancia o objeto
	$banco = new bancorelatorioprodutosvendidos();
	
	if($_GET){
		$dataIni = $_GET['dataIni'];
		$dataFim = $_GET['dataFim'];
		$buscaProdutoMarca = $_GET['produtoMarca'];
		$botao_limpar = '<a href="'.UrlPadrao.'relatorio-produtos-vendidos" class="btn btn-danger"><i class="fa fa-times"></i></a>';
	}
		
	$Relatorio = $banco->MontaRelatorio($dataIni, $dataFim, $buscaProdutoMarca);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('relatorio-produtos-vendidos'));
	$Conteudo = str_replace("<%RELATORIO%>", $Relatorio, $Conteudo);
	$Conteudo = str_replace("<%BUSCADATAINI%>", $dataIni, $Conteudo);
	$Conteudo = str_replace("<%BUSCADATAFIM%>", $dataFim, $Conteudo);
	$Conteudo = str_replace("<%BOTAOLIMPARFILTRO%>", $botao_limpar, $Conteudo);
	$Conteudo = str_replace("<%BUSCAPRODUTOMARCA%>", $buscaProdutoMarca, $Conteudo);
?>