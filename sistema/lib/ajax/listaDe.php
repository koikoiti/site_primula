<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$funcDe = $_POST['funcDe'];
	
	if($funcDe == 'nao'){
		$Sql = "SELECT C.nome, C.idcliente, P.tipo AS profissional FROM t_usuarios_carteira_clientes X
				RIGHT JOIN t_clientes C ON X.idcliente = C.idcliente 
				INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional
				WHERE X.idcliente IS NULL
				ORDER BY C.nome ASC";
		$result = $banco->Execute($Sql);
		while($rs = $banco->ArrayData($result)){
			$retorno .= '<div class="col-sm-12"><label><input name="arrClientes[]" value="'.$rs['idcliente'].'" type="checkbox"> '.$rs['nome'].' ('.$rs['profissional'].')</label></div>';
		}
	}else{
		$Sql = "SELECT C.nome, C.idcliente, P.tipo AS profissional FROM t_usuarios_carteira_clientes U 
				INNER JOIN t_clientes C ON U.idcliente = C.idcliente 
				INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional 
				WHERE U.idusuario = $funcDe 
				ORDER BY C.nome ASC";
		$result = $banco->Execute($Sql);
		while($rs = $banco->ArrayData($result)){
			$retorno .= '<div class="col-sm-12"><label><input name="arrClientes[]" value="'.$rs['idcliente'].'" type="checkbox"> '.$rs['nome'].' ('.$rs['profissional'].')</label></div>';
		}
	}
	
	echo utf8_encode($retorno);	
?>