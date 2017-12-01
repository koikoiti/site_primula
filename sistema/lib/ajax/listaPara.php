<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$funcPara = $_POST['funcPara'];
	
	$Sql = "SELECT C.nome, C.idcliente, P.tipo AS profissional FROM t_usuarios_carteira_clientes U
			INNER JOIN t_clientes C ON U.idcliente = C.idcliente 
			INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional 
			WHERE U.idusuario = $funcPara";
	$result = $banco->Execute($Sql);
	while($rs = $banco->ArrayData($result)){
		$retorno .= '<div class="col-sm-12"><label>'.$rs['nome'].' ('.$rs['profissional'].')</div>';
	}
	
	echo utf8_encode($retorno);
?>