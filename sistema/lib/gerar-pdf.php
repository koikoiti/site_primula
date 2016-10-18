<?php
	#include das funcoes da tela 
	include('functions/banco-finalizar.php');

	#Instancia o objeto
	$banco = new bancofinalizar();
    
	$idvenda = $this->PaginaAux[0];
	
	$banco->MontaSaida($idvenda);
	
	#$banco->MontaSaidaTxt($idvenda);
?>