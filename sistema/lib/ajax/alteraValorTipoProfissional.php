<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$idtipoprofissional = $_POST['idtipoprofissional'];
	
	$SqlVerifica = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = $idtipoprofissional";
	$resultVerifica = $banco->Execute($SqlVerifica);
	$rsVerifica = $banco->ArrayData($resultVerifica);
	
	if($rsVerifica['valor'] != 'valor_consumidor'){
		$Sql = "UPDATE t_valor_profissional SET valor = 'valor_consumidor' WHERE idtipoprofissional = $idtipoprofissional";
	}else{
		$Sql = "UPDATE t_valor_profissional SET valor = 'valor_profissional' WHERE idtipoprofissional = $idtipoprofissional";
	}
	
	$banco->Execute($Sql);
	echo 1;
?>