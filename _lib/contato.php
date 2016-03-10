<?php
	#include das funcoes da tela inico
	include('_functions/banco-contato.php');

	#Instancia o objeto
	$banco = new bancocontato();
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('contato'));
?>