<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idemailadicional = $_POST['idemailadicional'];
    
    
	$SqlDeleta = "DELETE FROM t_clientes_emailsadicionais WHERE idemailadicional = $idemailadicional";
	if($banco->Execute($SqlDeleta)){
	   echo 1;
    }else{
        echo 9;
    }
?>