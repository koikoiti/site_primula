<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$idcliente = $_POST['idcliente'];
	
	$Sql = "DELETE FROM t_usuarios_carteira_clientes WHERE idcliente = $idcliente";
	$banco->Execute($Sql);
	echo 1;	
?>