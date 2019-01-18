<?php
    $cbInativos = 'false';
	#include das funcoes da tela inico
	include('functions/banco-produto.php');

	#Instancia o objeto
	$banco = new bancoproduto();
    
    $pagina = 1;
    
    if($_GET){
        $produto = $_GET['produto'];
        $idcategoria = $_GET['categoria'];
        $marca = $_GET['marca'];
        $status = $_GET['status'];
        if($_GET['cbInativos'] == 'false'){
            $cbInativos = '';
        }else{
            $cbInativos = 'checked';
        }        
        if($_GET['page']){
            $pagina = $_GET['page'];
        }else{
            $pagina = 1;
        }
    }
    
    $Produtos = $banco->ListaProdutos($produto, $idcategoria, $marca, $modelo, $pagina, $status, $cbInativos);
    
    $select_categorias = $banco->MontaSelectBuscaCategorias($idcategoria);
    $select_status = $banco->MontaSelectStatus($status);
    
    $paginacao = $banco->MontaPaginacao($produto, $idcategoria, $marca, $modelo, $pagina, $status, $cbInativos);
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Produtos/lista-produto'));
    $Conteudo = str_replace("<%PRODUTOS%>", $Produtos, $Conteudo);
    $Conteudo = str_replace("<%BUSCACATEGORIA%>", $select_categorias, $Conteudo);
    $Conteudo = str_replace("<%BUSCASTATUS%>", $select_status, $Conteudo);
    $Conteudo = str_replace("<%PRODUTO%>", $produto, $Conteudo);
    $Conteudo = str_replace("<%MARCA%>", $marca, $Conteudo);
    $Conteudo = str_replace("<%MODELO%>", $modelo, $Conteudo);
    $Conteudo = str_replace('<%PAGINACAO%>', $paginacao, $Conteudo);
    $Conteudo = str_replace('<%CBINATIVOS%>', $cbInativos, $Conteudo);
?>