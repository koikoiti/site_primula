<?php
    $cbInativos = 'false';
    $titulo = "Lista Kit";
        
	#include das funcoes da tela inico
	include('functions/banco-kit.php');

	#Instancia o objeto
	$banco = new bancokit();
    
	if($_GET){
	    if($_GET['cbInativos'] == 'true'){
	        $cbInativos = 'checked';
	    }
	}
	
    $Kits = $banco->ListaKits($cbInativos);
    
    $Conteudo = utf8_encode($banco->CarregaHtml('Produtos/lista-kit'));
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
    $Conteudo = str_replace("<%KITS%>", $Kits, $Conteudo);
    $Conteudo = str_replace('<%PAGINACAO%>', $paginacao, $Conteudo);
    $Conteudo = str_replace('<%CBINATIVOS%>', $cbInativos, $Conteudo);
?>