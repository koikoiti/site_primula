<?php
	$pagina = 1;
	
	#include das funcoes da tela lista-produto
	include('_functions/banco-produto.php');

	#Instancia o objeto
	$banco = new bancoproduto();
    
	if($this->PaginaAux[0] == 'categoria'){
		$idcategoria = $this->PaginaAux[1];
		$categoria = ' > '.$banco->BuscaCategoriaPorId($idcategoria);
	}
	
	if($_GET){
		$pagina = $_GET['pagina'];
		$order = $_GET['order'];
		$idcategoria = $_GET['idcategoria'];
	}
	
	$Produtos = $banco->ListaProdutos($pagina, $idcategoria, $order);
	$Categorias = $banco->ListaCategorias();
	$paginacao = $banco->MontaPaginacao($pagina, $idcategoria, $order);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-produtos'));
	$Conteudo = str_replace('<%PRODUTOS%>', $Produtos, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIAS%>', $Categorias, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIA%>', $categoria, $Conteudo);
	$Conteudo = str_replace('<%IDCATEGORIA%>', $idcategoria, $Conteudo);
	$Conteudo = str_replace('<%PAGINACAO%>', $paginacao, $Conteudo);
	$Conteudo = str_replace('<%PAGINA%>', $pagina, $Conteudo);
	
	/*
	 * Depois dos produtos - Load more:
	 * <!-- div class="load-more-holder">
			<a href="#new-products" class="load-more">
				load more  products
			</a>
		</div-->
	 * */
	
	/*
	 * 
	<li class="disabled"><span>&laquo;</span></li>
    <li class="active"><span>1</span></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li><a href="#">&raquo;</a></li>
	 * 
	 * 
	 * 
	 * */
?>