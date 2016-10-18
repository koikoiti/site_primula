<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idcliente = $_POST['idcliente'];
        
	$SqlDeleta = "DELETE FROM t_clientes_consulta WHERE idcliente = $idcliente";
	if($banco->Execute($SqlDeleta)){
		echo 1;
    }else{
		echo 9;
    }
?>