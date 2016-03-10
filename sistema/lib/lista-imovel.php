<?php
	#include das funcoes da tela inico
	include('functions/banco-imovel.php');

	#Instancia o objeto
	$banco = new bancoimovel();
    
    $pagina = 1;
    
    if($_GET){
        $referencia = $_GET['referencia'];
        $idcategoria = $_GET['categoria'];
        $endereco = $_GET['endereco'];
        $bairro = $_GET['bairro'];
        $cidade = $_GET['cidade'];
        if($_GET['page']){
            $pagina = $_GET['page'];
        }else{
            $pagina = 1;
        }
    }
    
    $Imoveis = $banco->ListaImoveis($referencia, $idcategoria, $endereco, $bairro, $cidade, $pagina);
    
    $select_categorias = $banco->SelectBuscaCategorias($idcategoria);
    
    $paginacao = $banco->MontaPaginacao($referencia, $idcategoria, $endereco, $bairro, $cidade, $pagina);
        
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Imovel/lista-imovel'));
    $Conteudo = str_replace("<%IMOVEIS%>", $Imoveis, $Conteudo);
    $Conteudo = str_replace("<%BUSCATIPO%>", $select_categorias, $Conteudo);
    $Conteudo = str_replace("<%REFERENCIA%>", $referencia, $Conteudo);
    $Conteudo = str_replace("<%ENDERECO%>", $endereco, $Conteudo);
    $Conteudo = str_replace("<%BAIRRO%>", $bairro, $Conteudo);
    $Conteudo = str_replace("<%CIDADE%>", $cidade, $Conteudo);
    $Conteudo = str_replace('<%PAGINACAO%>', $paginacao, $Conteudo);
?>