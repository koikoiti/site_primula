<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idenderecoadicional = $_POST['idenderecoadicional'];
    
    
	$SqlDeleta = "DELETE FROM t_clientes_enderecosadicionais WHERE idenderecoadicional = $idenderecoadicional";
	if($banco->Execute($SqlDeleta)){
	   echo 1;
    }else{
        echo 9;
    }
?>