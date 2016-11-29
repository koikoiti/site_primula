<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$idaviso = $_POST['idaviso'];
	
	$Sql = "UPDATE t_avisos SET idusuario_finalizar = " . $_SESSION['idusuario'] . ", data_finalizar = '"  . date("Y-m-d H:i:s") . "' WHERE idaviso = $idaviso";
	$banco->Execute($Sql);
	echo 1;	
?>