<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idslider = $_POST['id'];
    
    $SqlCaminho = "SELECT * FROM t_slider WHERE idslider = $idslider";
    $resultCaminho = $banco->Execute($SqlCaminho);
    $rsCaminho = $banco->ArrayData($resultCaminho);
    if (strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
        $caminhoRemover = $_SERVER['DOCUMENT_ROOT'] . "/" . $rsCaminho['caminho'];
    }else{
        $caminhoRemover = $_SERVER['DOCUMENT_ROOT'] . "/Primula/" . $rsCaminho['caminho'];
    }

    unlink($caminhoRemover);
    
    $Sql = "DELETE FROM t_slider WHERE idslider = $idslider";
    $banco->Execute($Sql);
    
    echo 1;
?>