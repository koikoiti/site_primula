<?php
	#include das funcoes da tela 
	include('functions/banco-venda.php');

	#Instancia o objeto
	$banco = new bancovenda();
    
    $pagina = 1;
        
    $Vendas = $banco->ListaVendas();
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Vendas/lista-venda'));
    $Conteudo = str_replace("<%VENDAS%>", $Vendas, $Conteudo);
?>