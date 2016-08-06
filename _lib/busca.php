<?php
	#include das funcoes da tela lista-cursos
	include('_functions/banco-busca.php');

	#Instancia o objeto
	$banco = new bancobusca();
    
	$busca = urldecode($this->PaginaAux[0]);
	
	$Busca_Produto = $banco->MontaBuscaProduto($busca);
	$Busca_Kit = $banco->MontaBuscaKit($busca);
	$Busca_Curso = $banco->MontaBuscaCurso($busca);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('busca'));
	$Conteudo = str_replace('<%BUSCA%>', $busca, $Conteudo);
	$Conteudo = str_replace('<%BUSCAPRODUTO%>', $Busca_Produto, $Conteudo);
	$Conteudo = str_replace('<%BUSCAKIT%>', $Busca_Kit, $Conteudo);
	$Conteudo = str_replace('<%BUSCACURSO%>', $Busca_Curso, $Conteudo);
?>