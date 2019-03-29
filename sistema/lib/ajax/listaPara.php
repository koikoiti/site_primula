<?php
    function diffe($date1, $date2) {
        $start = new DateTime($date1);
        $end = new DateTime($date2);
        $months = round(($end->format('U') - $start->format('U')) / (60*60*24*30));
        return $months;
    }

	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	$funcPara = $_POST['funcPara'];
	
	$Sql = "SELECT C.nome, C.idcliente, P.tipo AS profissional, MAX(V.data) AS ultima_venda FROM t_usuarios_carteira_clientes U
			INNER JOIN t_clientes C ON U.idcliente = C.idcliente 
			INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional 
            LEFT JOIN t_vendas V ON V.idcliente = C.idcliente
			WHERE U.idusuario = $funcPara
			GROUP BY C.idcliente
			ORDER BY ultima_venda DESC";
	$result = $banco->Execute($Sql);
	while($rs = $banco->ArrayData($result)){
	    if($rs['ultima_venda']){
	        $ultima_venda = date("d/m/Y", strtotime($rs['ultima_venda']));
	        $meses_ultima_venda = diffe($rs['ultima_venda'], date("Y-m-d H:i:s"));
	        if($meses_ultima_venda >= 0 && $meses_ultima_venda <= 3){
	            $div_color = 'alert-success';
	        }elseif($meses_ultima_venda > 3 && $meses_ultima_venda <= 6){
	            $div_color = 'alert-warning';
	        }elseif($meses_ultima_venda > 6){
	            $div_color = 'alert-danger';
	        }
	    }else{
	        $ultima_venda = "--/--/----";
	        $meses_ultima_venda = null;
	        $div_color = '';
	    }
	    
	   $meses_ultima_venda = diffe($rs['ultima_venda'], date("Y-m-d"));
	    
		$retorno .= '<div class="col-sm-10 '.$div_color.'"><label>'.$rs['nome'].' ('.$rs['profissional'].')</div><div class="col-sm-2 '.$div_color.'"><label>'.$ultima_venda.'</label></div>';
	}
	
	echo utf8_encode($retorno);
?>