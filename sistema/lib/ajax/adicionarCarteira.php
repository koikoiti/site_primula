<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$idcliente = $_POST['idcliente'];
	
	$SqlVerifica = "SELECT * FROM t_usuarios_carteira_clientes WHERE idcliente = $idcliente";
	$resultVerifica = $banco->Execute($SqlVerifica);
	$linhaVerifica = $banco->Linha($resultVerifica);
	
	if($linhaVerifica){
		echo 666;
	}else{
		$Sql = "INSERT INTO t_usuarios_carteira_clientes (idusuario, idcliente) VALUES (".$_SESSION['idusuario'].", $idcliente)";
		$banco->Execute($Sql);
		echo 1;
	}
?>