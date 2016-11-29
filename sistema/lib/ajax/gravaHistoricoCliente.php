<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idcliente = $_POST['idcliente'];
    $historico = utf8_decode($_POST['historico']);
    
    $Sql = "INSERT INTO t_clientes_historico (idcliente, data, historico, usuario) VALUES 
    		($idcliente, '".date("Y-m-d H:i:s")."', '$historico', '".$_SESSION['nomeexibicao']."')";
    $banco->Execute($Sql);
    echo 1;
    
?>