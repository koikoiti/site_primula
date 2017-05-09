<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$tipo = utf8_decode($_POST['tipo']);
	$valor = $_POST['valor'];
	
	$Sql1 = "INSERT INTO fixo_tipo_profissional (tipo) VALUES ('$tipo')";
	$banco->Execute($Sql1);
	$lastID = mysql_insert_id();
	
	$Sql = "INSERT INTO t_valor_profissional (idtipoprofissional, valor) VALUES ('$lastID', '$valor')";
	$banco->Execute($Sql);
	echo 1;
?>