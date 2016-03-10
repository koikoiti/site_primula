<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $term = $_GET[ "term" ];
    
    $Sql = 'SELECT * FROM t_clientes WHERE nome LIKE "%'.$term.'%"';
    $result = $banco->Execute($Sql);
    
    while($rs = $banco->ArrayData($result)){
        $array[] = array('label' => utf8_encode($rs['nome']),
                         'value' => utf8_encode($rs['nome']),
                         'idcliente' => $rs['idcliente'],
                    );
    }
    
    echo json_encode($array);
 ?>