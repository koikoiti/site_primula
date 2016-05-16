<?php
	#include das funcoes da tela lista-produto
	include('_functions/banco-produto.php');

	#Instancia o objeto
	$banco = new bancoproduto();
    
	$Produtos = $banco->ListaProdutos();
	$Categorias = $banco->ListaCategorias();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-produtos'));
	$Conteudo = str_replace('<%PRODUTOS%>', $Produtos, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIAS%>', $Categorias, $Conteudo);
	
	/*
	 * Depois dos produtos - Load more:
	 * <!-- div class="load-more-holder">
			<a href="#new-products" class="load-more">
				load more  products
			</a>
		</div-->
	 * */
?>