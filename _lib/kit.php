<?php
	#include das funcoes da tela lista-kit
	include('_functions/banco-kits.php');

	#Instancia o objeto
	$banco = new bancokit();
    
	$Categorias = $banco->ListaCategorias();
	$idkit = $this->PaginaAux[0];
	
	$rsKit = $banco->BuscaKitPorId($idkit); 
	$fotos = $banco->MontaFotosKit($idkit);
	$produtos = $banco->BuscaProdutosKit($idkit);
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('kit'));
	$Conteudo = str_replace('<%NOMEKIT%>', utf8_encode($rsKit['nome']), $Conteudo);
	$Conteudo = str_replace('<%CODIGO%>', utf8_encode($rsKit['codigo']), $Conteudo);
	$Conteudo = str_replace('<%DESCRICAO%>', utf8_encode($rsKit['descricao']), $Conteudo);
	$Conteudo = str_replace('<%PRECO%>', number_format($rsKit['valor_consumidor'], 2, ',', '.'), $Conteudo);
	$Conteudo = str_replace('<%FOTOS%>', $fotos, $Conteudo);
	$Conteudo = str_replace('<%PRODUTOS%>', $produtos, $Conteudo);
?>