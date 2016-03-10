<?php
	#include das funcoes da tela
	include('functions/banco-aviso.php');

	#Instancia o objeto
	$banco = new bancoaviso();
    
    $Avisos = $banco->ListaAvisos();
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-avisos'));
    $Conteudo = str_replace("<%AVISOS%>", $Avisos, $Conteudo);
?>