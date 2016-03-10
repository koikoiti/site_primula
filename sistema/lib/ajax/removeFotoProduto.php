<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $idimagem = $_POST['idimagem'];
    $idproduto = $_POST['idproduto'];
    
    $Sql = "SELECT * FROM t_imagens_produto WHERE idimagem = $idimagem";
	$result = $banco->Execute($Sql);
	$rs = $banco->ArrayData($result);
    
    if (strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
        $caminhoRemover = $_SERVER['DOCUMENT_ROOT'] . "/" . $rs['caminho'];
    }else{
        $caminhoRemover = $_SERVER['DOCUMENT_ROOT'] . "/primula/" . $rs['caminho'];
    }
    
	unlink($caminhoRemover);
    
	$SqlDeleta = "DELETE FROM t_imagens_produto WHERE idimagem = $idimagem";
	if($banco->Execute($SqlDeleta)){
		$SqlOrder = "SELECT * FROM t_imagens_produto WHERE idproduto = '$idproduto' ORDER BY ordem ASC";
		$resultOrder = $banco->Execute($SqlOrder);
		$cont = 1;
		while($rsOrder = $banco->ArrayData($resultOrder)){
			$SqlNewOrder = "UPDATE t_imagens_produto SET ordem = '$cont' WHERE idimagem = " . $rsOrder['idimagem'];
			$banco->Execute($SqlNewOrder);
			$cont++;
		}
		echo 1;
	}else{
		echo 9;
	}
?>