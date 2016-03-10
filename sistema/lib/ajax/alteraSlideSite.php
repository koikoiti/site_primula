<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idslider = $_POST['id'];
    $texto1 = $_POST['texto1'];
    $texto2 = $_POST['texto2'];
    $link = $_POST['link'];
    
    $Sql = "UPDATE t_slider SET texto1 = '$texto1', texto2 = '$texto2', link = '$link' WHERE idslider = $idslider";
    $banco->Execute($Sql);
    
    echo 1;
?>