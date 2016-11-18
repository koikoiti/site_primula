<?php
	#include das funcoes da tela
	include('functions/banco-scylla.php');
	
	#Instancia o objeto
	$banco = new bancoscylla();
	
	switch($this->PaginaAux[0]){
		case 'arrumarEstoque':
			#$banco->ArrumaEstoque();
			break;
		default:
			#$banco->RedirecionaPara('inicio');
			break;
	}
?>