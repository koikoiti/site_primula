<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idhistoricocliente = $_POST['idhistoricocliente'];
    $historico = utf8_decode($_POST['historico']);
    $rever = $_POST['rever'];
    
    $Sql = "UPDATE t_clientes_historico SET data = '".date("Y-m-d H:i:s")."', historico = '$historico' WHERE idhistoricocliente = $idhistoricocliente";
    $banco->Execute($Sql);
    
    $SqlCliente = "SELECT idcliente FROM t_clientes_historico WHERE idhistoricocliente = $idhistoricocliente";
    $resultCliente = $banco->Execute($SqlCliente);
    $rsCliente = $banco->ArrayData($resultCliente);
    
    if($rever != ""){
    	$Sql_Verificar = "UPDATE t_clientes SET verificado = 9, data_verificar = '$rever' WHERE idcliente = " . $rsCliente['idcliente'];
    }else{
    	$Sql_Verificar = "UPDATE t_clientes SET verificado = 1, data_verificar = '0000-00-00' WHERE idcliente = " . $rsCliente['idcliente'];
    }
    
    $banco->Execute($Sql_Verificar);
    
    echo 1;
?>