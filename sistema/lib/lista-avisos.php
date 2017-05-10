<?php
	#include das funcoes da tela
	include('functions/banco-aviso.php');
	
	$view_finalizados = 0;
	
	#Instancia o objeto
	$banco = new bancoaviso();
	
	if($_GET){
		$view_finalizados = $_GET['view_finalizados'];
	}
	
	if($view_finalizados == 1){
		$finalizadosCB = 'checked';
	}else{
		$finalizadosCB = '';
	}
    
    $Avisos = $banco->ListaAvisos($view_finalizados);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-avisos'));
    $Conteudo = str_replace("<%AVISOS%>", $Avisos, $Conteudo);
    $Conteudo = str_replace("<%FINALIZADOSCB%>", $finalizadosCB, $Conteudo);
?>