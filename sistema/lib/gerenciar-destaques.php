<?php
	#include das funcoes da tela gerenciar-slider
	include('functions/banco-gerenciar.php');

	#Instancia o objeto
	$banco = new bancogerenciar();
    
    $destaques = $banco->MontaDestaques();
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('gerenciar-destaques'));
    $Conteudo = str_replace('<%DESTAQUES%>', $destaques, $Conteudo);
?>