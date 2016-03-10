<?php
    $titulo = "Fluxo Financeiro";
        
	#include das funcoes da tela
	include('functions/banco-fluxo.php');

	#Instancia o objeto
	$banco = new bancofluxo();
    
    $Conteudo = utf8_encode($banco->CarregaHtml('Gerenciamento/fluxo-financeiro'));
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
?>