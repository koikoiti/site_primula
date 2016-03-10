<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $id = $_POST['idproduto'];
    
    $Sql = "DELETE FROM t_destaques WHERE idproduto = $id";
    $banco->Execute($Sql);
    echo 1;
?>