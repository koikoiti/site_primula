<?php
	#include das funcoes da tela inico
	include('_functions/banco-inicio.php');

	#Instancia o objeto
	$banco = new bancoinicio();
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('sobre-nos'));
?>