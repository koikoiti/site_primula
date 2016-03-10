<?php
	#include das funcoes da tela 
	include('functions/banco-finalizar.php');

	#Instancia o objeto
	$banco = new bancofinalizar();
    
    $banco->MontaSaida();
?>