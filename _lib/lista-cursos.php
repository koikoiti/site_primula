<?php
	#include das funcoes da tela lista-cursos
	include('_functions/banco-cursos.php');

	#Instancia o objeto
	$banco = new bancocursos();
    
	$Cursos = $banco->MontaCursos();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-cursos'));
	$Conteudo = str_replace('<%CURSOS%>', $Cursos, $Conteudo);
?>