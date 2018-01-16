<?php
	#include das funcoes da tela 
	include('functions/banco-relatorio.php');
	
	$dataIni = date("Y-m-01");
	$dataFim = date("Y-m-d");
	$botao_limpar = '';
	
	#Instancia o objeto
	$banco = new bancorelatorio();
    
    if($_GET){
    	$dataIni = $_GET['dataIni'];
    	$dataFim = $_GET['dataFim'];
    	$idresponsavel = $_GET['busca_responsavel'];
    	$marca = $_GET['busca_marca'];
    	$idtipopagamento = $_GET['busca_pgto'];
    	$botao_limpar = '<a href="'.UrlPadrao.'relatorios" class="btn btn-danger"><i class="fa fa-times"></i></a>';
    }
    
    $Relatorio = $banco->MontaRelatorio($dataIni, $dataFim, $idresponsavel, $marca, $idtipopagamento);
    $select_usuarios = $banco->MontaUsuarios($idresponsavel);
    $select_tipoPagamento = $banco->MontaSelectTipoPagamento($idtipopagamento);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('relatorios'));
    $Conteudo = str_replace("<%RELATORIO%>", $Relatorio, $Conteudo);
    $Conteudo = str_replace("<%SELECTUSUARIOS%>", $select_usuarios, $Conteudo);
    $Conteudo = str_replace("<%SELECTTIPOPAGAMENTO%>", $select_tipoPagamento, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAINI%>", $dataIni, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAFIM%>", $dataFim, $Conteudo);
    $Conteudo = str_replace("<%BUSCAMARCA%>", $marca, $Conteudo);
    $Conteudo = str_replace("<%BOTAOLIMPARFILTRO%>", $botao_limpar, $Conteudo);
?>