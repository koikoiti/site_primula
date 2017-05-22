<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idcliente = $_POST['idcliente'];
    $historico = utf8_decode($_POST['historico']);
    $rever = $_POST['rever'];
    echo $rever;
    $Sql = "INSERT INTO t_clientes_historico (idcliente, data, historico, usuario) VALUES 
    		($idcliente, '".date("Y-m-d H:i:s")."', '$historico', '".$_SESSION['nomeexibicao']."')";
    $banco->Execute($Sql);
        
    if($rever != ""){
    	$Sql_Verificar = "UPDATE t_clientes SET verificado = 9, data_verificar = '$rever' WHERE idcliente = $idcliente";
    }else{
    	$Sql_Verificar = "UPDATE t_clientes SET verificado = 1, data_verificar = '0000-00-00' WHERE idcliente = $idcliente";
    }
    
    $banco->Execute($Sql_Verificar);
    
    echo 1;
?>