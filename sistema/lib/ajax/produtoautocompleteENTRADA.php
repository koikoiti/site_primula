<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $term = $_GET[ "term" ];
    
    $Sql = 'SELECT * FROM t_produtos WHERE (nome LIKE "%'.$term.'%" OR cod_barras LIKE "%'.$term.'%" OR marca LIKE "%'.$term.'%") AND ativo = 1 ORDER BY nome';
    $result = $banco->Execute($Sql);
    
    while($rs = $banco->ArrayData($result)){
        $SqlImagem = "SELECT caminho FROM t_imagens_produto WHERE ordem = 1 AND idproduto = {$rs['idproduto']}";
        $resultImagem = $banco->Execute($SqlImagem);
        $rsImagem = $banco->ArrayData($resultImagem);
        $array[] = array('label' => 'Produto: '.utf8_encode($rs['nome'] . ' - ' . $rs['marca'] . ' - Estoque: ' . $rs['estoque'] . ' UN'),
                         'value' => 'Produto: '.utf8_encode($rs['nome']),
                         'idproduto' => 'prod_'.$rs['idproduto'],
                         'caminho' => UrlFoto.$rsImagem['caminho'],
                    );
    }
    
    $SqlKit = 'SELECT * FROM t_kit WHERE (nome LIKE "%'.$term.'%" OR codigo LIKE "%'.$term.'%") AND ativo = 1 ORDER BY nome';
    $resultKit = $banco->Execute($SqlKit);
    
    while($rsKit = $banco->ArrayData($resultKit)){
        $SqlImagemKit = "SELECT caminho FROM t_imagens_kit WHERE ordem = 1 AND idkit = {$rsKit['idkit']}";
        $resultImagemKit = $banco->Execute($SqlImagemKit);
        $rsImagemKit = $banco->ArrayData($resultImagemKit);
        $array[] = array('label' => 'Kit: '.utf8_encode($rsKit['nome'] . ' - Estoque: ' . $rsKit['estoque'] . ' UN'),
                         'value' => 'Kit: '.utf8_encode($rsKit['nome']),
                         'idproduto' => 'kit_'.$rsKit['idkit'],
                         'caminho' => UrlFoto.$rsImagemKit['caminho'],
                    );
    }
    
    echo json_encode($array);
 ?>