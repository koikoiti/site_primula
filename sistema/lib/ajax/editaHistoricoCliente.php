<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idhistorico = $_POST['idhistoricocliente'];
    
    $Sql = "SELECT historico, idcliente FROM t_clientes_historico WHERE idhistoricocliente = $idhistorico";
    $result = $banco->Execute($Sql);
    $rs = $banco->ArrayData($result);
    
    $SqlData = "SELECT data_verificar FROM t_clientes WHERE idcliente = " . $rs['idcliente'];
    $resultData = $banco->Execute($SqlData);
    $rsData = $banco->ArrayData($resultData);
    
    $retorno['historico'] = utf8_encode($rs['historico']);
    $retorno['data_verificar'] = $rsData['data_verificar'];
    
    echo json_encode($retorno);
?>