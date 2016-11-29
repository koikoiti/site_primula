<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idhistoricocliente = $_POST['idhistoricocliente'];
    $historico = utf8_decode($_POST['historico']);
    
    $Sql = "UPDATE t_clientes_historico SET data = '".date("Y-m-d H:i:s")."', historico = '$historico' WHERE idhistoricocliente = $idhistoricocliente";
    $banco->Execute($Sql);
    echo 1;
?>