<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$idtipoprofissional = $_POST['idtipoprofissional'];
	
	$SqlVerifica = "SELECT ativo FROM t_valor_profissional WHERE idtipoprofissional = $idtipoprofissional";
	$resultVerifica = $banco->Execute($SqlVerifica);
	$rsVerifica = $banco->ArrayData($resultVerifica);
	
	if($rsVerifica['ativo'] == 1){
		$Sql = "UPDATE t_valor_profissional SET ativo = 0 WHERE idtipoprofissional = $idtipoprofissional";
	}else{
		$Sql = "UPDATE t_valor_profissional SET ativo = 1 WHERE idtipoprofissional = $idtipoprofissional";
	}
	
	$banco->Execute($Sql);
	echo 1;
?>