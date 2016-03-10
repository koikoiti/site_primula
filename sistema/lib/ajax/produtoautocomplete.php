<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $term = $_GET[ "term" ];
    $idcliente = $_GET['idcliente'];
    
    $SqlTipo = "SELECT * FROM t_clientes WHERE idcliente = $idcliente";
    $resultTipo = $banco->Execute($SqlTipo);
    $rsTipo = $banco->ArrayData($resultTipo);
    
    if($rsTipo['idtipocliente'] == 1){
        $tipo = 'valor_consumidor';
    }else{
        $tipo = 'valor_profissional';
    }
    
    $Sql = 'SELECT * FROM t_produtos WHERE (nome LIKE "%'.$term.'%" OR cod_barras LIKE "%'.$term.'%" OR marca LIKE "%'.$term.'%") AND estoque > 0';
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
    
    $SqlKit = 'SELECT * FROM t_kit WHERE (nome LIKE "%'.$term.'%" OR codigo LIKE "%'.$term.'%") AND estoque > 0';
    $resultKit = $banco->Execute($SqlKit);
    
    while($rsKit = $banco->ArrayData($resultKit)){
        $SqlImagemKit = "SELECT caminho FROM t_imagens_kit WHERE ordem = 1 AND idkit = {$rsKit['idkit']}";
        $resultImagemKit = $banco->Execute($SqlImagemKit);
        $rsImagemKit = $banco->ArrayData($resultImagemKit);
        $array[] = array('label' => utf8_encode('Kit: ' . $rsKit['nome'] . ' - Estoque: ' . $rsKit['estoque'] . ' UN'),
                         'value' => 'Kit: '.utf8_encode($rsKit['nome']),
                         'idproduto' => 'kit_'.$rsKit['idkit'],
                         'caminho' => UrlFoto.$rsImagemKit['caminho'],
                         'valor_consumidor' => $rsKit['valor_consumidor'],
                         'valor_profissional' => $rsKit['valor_profissional'],
                    );
    }
    
    echo json_encode($array);
 ?>