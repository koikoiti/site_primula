<?php
	#include das funcoes da tela
	include('functions/banco-mostruario.php');

	#Instancia o objeto
	$banco = new bancomostruario();
    
    $Mostruario = $banco->ListaMostruario();
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-mostruario'));
    $Conteudo = str_replace("<%MOSTRUARIO%>", $Mostruario, $Conteudo);
?>