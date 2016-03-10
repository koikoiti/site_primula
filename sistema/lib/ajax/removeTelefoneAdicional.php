<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idtelefoneadicional = $_POST['idtelefoneadicional'];
    
    
	$SqlDeleta = "DELETE FROM t_clientes_telefonesadicionais WHERE idtelefoneadicional = $idtelefoneadicional";
	if($banco->Execute($SqlDeleta)){
	   echo 1;
    }else{
        echo 9;
    }
?>