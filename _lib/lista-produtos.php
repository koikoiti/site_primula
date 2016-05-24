<?php
	#include das funcoes da tela lista-produto
	include('_functions/banco-produto.php');

	#Instancia o objeto
	$banco = new bancoproduto();
    
	if($this->PaginaAux[0] == 'categoria'){
		$idcategoria = $this->PaginaAux[1];
		$categoria = $banco->BuscaCategoriaPorId($idcategoria);
	}
	
	$Produtos = $banco->ListaProdutos($idcategoria);
	$Categorias = $banco->ListaCategorias();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-produtos'));
	$Conteudo = str_replace('<%PRODUTOS%>', $Produtos, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIAS%>', $Categorias, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIA%>', $categoria, $Conteudo);
	
	/*
	 * Depois dos produtos - Load more:
	 * <!-- div class="load-more-holder">
			<a href="#new-products" class="load-more">
				load more  products
			</a>
		</div-->
	 * */
?>