<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idhistorico = $_POST['idhistoricocliente'];
    
    $Sql = "SELECT historico FROM t_clientes_historico WHERE idhistoricocliente = $idhistorico";
    $result = $banco->Execute($Sql);
    $rs = $banco->ArrayData($result);
    echo utf8_encode($rs['historico']);
?>