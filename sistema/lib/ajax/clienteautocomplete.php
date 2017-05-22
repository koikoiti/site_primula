<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $term = $_GET[ "term" ];
    
    $Sql = 'SELECT idcliente, nome, idtipoprofissional, cnpj, cpf FROM t_clientes WHERE (nome LIKE "%'.$term.'%" OR nome_contato LIKE "%'.$term.'%" OR cpf LIKE "%'.$term.'%" OR cnpj LIKE "%'.$term.'%") AND ativo = 1 ORDER BY nome ASC';
    $result = $banco->Execute($Sql);
    
    while($rs = $banco->ArrayData($result)){
    	if($rs['cnpj'] == ''){
    		if($rs['cpf'] == ''){
    			$info = '';
    		}else{
    			$info = " - CPF: {$rs['cpf']}";
    		}
    	}else{
    		$info = " - CNPJ: {$rs['cnpj']}";
    	}
        $array[] = array('label' => utf8_encode($rs['nome'] . $info),
                         'value' => utf8_encode($rs['nome']),
			        	 'info' => utf8_encode($info),
                         'idcliente' => $rs['idcliente'],
        				 'idtipoprofissional' => $rs['idtipoprofissional'],
                    );
    }
    
    echo json_encode($array);
 ?>