<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idproduto_kit = $_POST['idproduto'];
    $quantidade = $_POST['quantidade'];
    
    $auxProd = explode("_", $idproduto_kit);
    
    if($auxProd[0] == 'prod'){
    	#Remove produto
    	$Sql = "UPDATE t_produtos SET estoque = estoque - $quantidade WHERE idproduto = " . $auxProd[1];
    }elseif($auxProd[0] == 'kit'){
    	#Remove kit'
    	$Sql = "UPDATE t_kit SET estoque = estoque - $quantidade WHERE idkit = " . $auxProd[1];
    }
    
    if($banco->Execute($Sql)){
    	echo 1;
    }else{
    	echo 9;
    }
?>