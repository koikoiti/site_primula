<?php
	#include das funcoes da tela inico
	include('functions/banco-usuario.php');

	#Instancia o objeto
	$banco = new bancousuario();
    
    $Usuarios = $banco->ListaUsuarios();
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Usuario/lista-usuario'));
    $Conteudo = str_replace("<%USUARIOS%>", $Usuarios, $Conteudo);
?>