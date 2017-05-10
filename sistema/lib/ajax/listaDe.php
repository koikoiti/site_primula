<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$funcDe = $_POST['funcDe'];
	
	$Sql = "SELECT C.nome, C.idcliente FROM t_usuarios_carteira_clientes U 
			INNER JOIN t_clientes C ON U.idcliente = C.idcliente 
			WHERE U.idusuario = $funcDe";
	$result = $banco->Execute($Sql);
	while($rs = $banco->ArrayData($result)){
		$retorno .= '<div class="col-sm-12"><label><input name="arrClientes[]" value="'.$rs['idcliente'].'" type="checkbox"> '.$rs['nome'].'</label></div>';
	}
	
	echo utf8_encode($retorno);	
?>