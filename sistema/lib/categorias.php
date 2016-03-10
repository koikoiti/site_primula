<?php
    $titulo = "Categorias Produto";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    
	#include das funcoes da tela inico
	include('functions/banco-categorias.php');

	#Instancia o objeto
	$banco = new bancocategorias();
    
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        $arrCateg = $_POST['categ'];
        $arrCategOld = $_POST['categOld'];
        
        $banco->InsereCateg($arrCateg);
        $banco->UpdateCateg($arrCategOld);
        $banco->RedirecionaPara('categorias');
    }
    
    $categorias = $banco->BuscaCategorias();
    
    $Conteudo = utf8_encode($banco->CarregaHtml('Produtos/categorias'));
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
    $Conteudo = str_replace("<%CATEGORIAS%>", $categorias, $Conteudo);
 
    #Botões
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
?>