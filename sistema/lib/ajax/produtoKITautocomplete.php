<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $term = utf8_decode($_GET[ "term" ]);
    $idcliente = $_GET['idcliente'];
    
    $SqlTipo = "SELECT * FROM t_clientes WHERE idcliente = $idcliente";
    $resultTipo = $banco->Execute($SqlTipo);
    $rsTipo = $banco->ArrayData($resultTipo);
    
    if($rsTipo['idtipocliente'] == 1){
        $tipo = 'valor_consumidor';
    }else{
        $tipo = 'valor_profissional';
    }
    
    $Sql = 'SELECT * FROM t_produtos WHERE (nome LIKE "%'.$term.'%" OR cod_barras LIKE "%'.$term.'%" OR marca LIKE "%'.$term.'%")';
    $result = $banco->Execute($Sql);
    
    while($rs = $banco->ArrayData($result)){
        $SqlImagem = "SELECT caminho FROM t_imagens_produto WHERE ordem = 1 AND idproduto = {$rs['idproduto']}";
        $resultImagem = $banco->Execute($SqlImagem);
        $rsImagem = $banco->ArrayData($resultImagem);
        $array[] = array('label' => utf8_encode($rs['nome'] . ' - ' . $rs['marca'] . ' - Estoque: ' . $rs['estoque'] . ' UN'),
                         'value' => 'Produto: '.utf8_encode($rs['nome']),
                         'idproduto' => 'prod_'.$rs['idproduto'],
                         'caminho' => UrlFoto.$rsImagem['caminho'],
                         'valor_consumidor' => $rs['valor_consumidor'],
                         'valor_profissional' => $rs['valor_profissional'],
                    );
    }
    
    echo json_encode($array);
 ?>