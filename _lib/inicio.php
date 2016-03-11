<?php
	#include das funcoes da tela inico
	include('_functions/banco-inicio.php');

	#Instancia o objeto
	$banco = new bancoinicio();
    
	$destaques = $banco->MontaDestaques();
	$ultimos = $banco->MontaUltimos();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('inicio'));
	$Conteudo = str_replace('<%DESTAQUES%>', $destaques, $Conteudo);
	$Conteudo = str_replace('<%ULTIMOS%>', $ultimos, $Conteudo);
?>