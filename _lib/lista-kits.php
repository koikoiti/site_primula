<?php
	#include das funcoes da tela lista-kits
	include('_functions/banco-kits.php');

	#Instancia o objeto
	$banco = new bancokit();
    
	$Kits = $banco->ListaKits();
	$Categorias = $banco->ListaCategorias();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-kits'));
	$Conteudo = str_replace('<%KITS%>', $Kits, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIAS%>', $Categorias, $Conteudo);
?>