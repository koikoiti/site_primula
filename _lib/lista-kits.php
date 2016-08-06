<?php
	$pagina = 1;
	#include das funcoes da tela lista-kits
	include('_functions/banco-kits.php');

	#Instancia o objeto
	$banco = new bancokit();
    
	if($_GET){
		$pagina = $_GET['pagina'];
		$order = $_GET['order'];
	}
	
	$Kits = $banco->ListaKits($pagina, $order);
	#$Categorias = $banco->ListaCategorias();
	$paginacao = $banco->MontaPaginacao($pagina, $order);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-kits'));
	$Conteudo = str_replace('<%KITS%>', $Kits, $Conteudo);
	#$Conteudo = str_replace('<%CATEGORIAS%>', $Categorias, $Conteudo);
	$Conteudo = str_replace('<%PAGINACAO%>', $paginacao, $Conteudo);
	$Conteudo = str_replace('<%PAGINA%>', $pagina, $Conteudo);
?>