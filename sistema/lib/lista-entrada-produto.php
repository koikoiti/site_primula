<?php
    $titulo = "Lista de Entrada de Produtos";
    
	#include das funcoes da tela inico
	include('functions/banco-entrada-produto.php');

	#Instancia o objeto
	$banco = new bancoentradaproduto();
    
    $EntradaProdutos = $banco->ListaEntradaProdutos();
    
    $Conteudo = utf8_encode($banco->CarregaHtml('Produtos/lista-entrada-produto'));
    $Conteudo = str_replace("<%ENTRADAPRODUTOS%>", $EntradaProdutos, $Conteudo);
    $Conteudo = str_replace('<%PAGINACAO%>', $paginacao, $Conteudo);
?>