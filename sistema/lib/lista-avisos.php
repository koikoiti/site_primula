<?php
	#include das funcoes da tela
	include('functions/banco-aviso.php');
	
	$view_finalizados = 0;
	$busca_dataIni = "";
	$busca_dataFim = date("Y-m-d", strtotime("+7 day"));
	$botao_limpar = '';
	
	#Instancia o objeto
	$banco = new bancoaviso();
	
	if($_GET){
	    $busca_dataIni = $_GET['busca_dataIni'];
	    $busca_dataFim = $_GET['busca_dataFim'];
		$view_finalizados = $_GET['view_finalizados'];
		$botao_limpar = '<button style="float: left;" onclick="javascript:location.href=\'<%URLPADRAO%>lista-avisos\'" type="button" class="btn btn-danger btn-flat"><i class="fa fa-times"></i></button>';
	}
	
	if($view_finalizados == 1){
		$finalizadosCB = 'checked';
	}else{
		$finalizadosCB = '';
	}
    
    $Avisos = $banco->ListaAvisos($view_finalizados, $busca_dataIni, $busca_dataFim);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-avisos'));
    $Conteudo = str_replace("<%AVISOS%>", $Avisos, $Conteudo);
    $Conteudo = str_replace("<%FINALIZADOSCB%>", $finalizadosCB, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAINI%>", $busca_dataIni, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAFIM%>", $busca_dataFim, $Conteudo);
    $Conteudo = str_replace("<%BOTAOLIMPAR%>", $botao_limpar, $Conteudo);
?>