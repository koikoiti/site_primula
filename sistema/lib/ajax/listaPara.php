<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$funcPara = $_POST['funcPara'];
	
	$Sql = "SELECT C.nome, C.idcliente FROM t_usuarios_carteira_clientes U
			INNER JOIN t_clientes C ON U.idcliente = C.idcliente
			WHERE U.idusuario = $funcPara";
	$result = $banco->Execute($Sql);
	while($rs = $banco->ArrayData($result)){
		$retorno .= '<div class="col-sm-12"><label>'.$rs['nome'].'</div>';
	}
	
	echo utf8_encode($retorno);
?>