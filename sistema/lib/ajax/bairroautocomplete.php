<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $term = $_GET[ "term" ];
    
    $Sql = 'SELECT bairro FROM t_bairros WHERE (bairro LIKE "%'.$term.'%") GROUP BY bairro ORDER BY bairro ASC';
    $result = $banco->Execute($Sql);
    
    while($rs = $banco->ArrayData($result)){
        $array[] = array('label' => utf8_encode($rs['bairro']),
                         'value' => utf8_encode($rs['bairro']),
                    );
    }
    
    echo json_encode($array);
 ?>