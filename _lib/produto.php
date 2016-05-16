<?php
	#include das funcoes da tela lista-produto
	include('_functions/banco-produto.php');

	#Instancia o objeto
	$banco = new bancoproduto();
    
	$Categorias = $banco->ListaCategorias();
	$idproduto = $this->PaginaAux[0];
	
	$rsProduto = $banco->BuscaProdutoPorId($idproduto); 
	$fotos = $banco->MontaFotosProdutoUnico($idproduto);
	$semelhantes = $banco->MontaSemelhantes($rsProduto['idcategoria']);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('produto'));
	$Conteudo = str_replace('<%NOMEPRODUTO%>', utf8_encode($rsProduto['nome']), $Conteudo);
	$Conteudo = str_replace('<%MARCA%>', utf8_encode($rsProduto['marca']), $Conteudo);
	$Conteudo = str_replace('<%CODIGO%>', utf8_encode($rsProduto['cod_barras']), $Conteudo);
	$Conteudo = str_replace('<%DESCRICAO%>', utf8_encode($rsProduto['descricao']), $Conteudo);
	$Conteudo = str_replace('<%NOMECATEGORIA%>', utf8_encode($rsProduto['categoria']), $Conteudo);
	$Conteudo = str_replace('<%PRECO%>', number_format($rsProduto['valor_consumidor'], 2, ',', '.'), $Conteudo);
	$Conteudo = str_replace('<%FOTOS%>', $fotos, $Conteudo);
	$Conteudo = str_replace('<%SEMELHANTES%>', $semelhantes, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIAS%>', $Categorias, $Conteudo);
?>