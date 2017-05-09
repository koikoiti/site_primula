<?php
	#include das funcoes da tela inico
	include('functions/banco-cliente.php');
	
	#Instancia o objeto
	$banco = new bancocliente();
	
	$tipo_profissional = $banco->BuscaTipoProfissional();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('clientes-tipo-profissional'));
	$Conteudo = str_replace('<%MSG%>', $msg, $Conteudo);
	$Conteudo = str_replace('<%TIPOPROFISSIONAL%>', $tipo_profissional, $Conteudo);
?>