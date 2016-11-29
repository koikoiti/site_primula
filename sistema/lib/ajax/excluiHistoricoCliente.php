<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idhistorico = $_POST['idhistoricocliente'];
    
    $Sql = "DELETE FROM t_clientes_historico WHERE idhistoricocliente = $idhistorico";
    $banco->Execute($Sql);
    echo 1;  
?>