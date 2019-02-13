<?php
	include('../../functions/banco.php');
	include('../../functions/banco-kit.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$bancoKit = new bancokit();
	
	$banco->Conecta();
	session_start('login');
    
    $term = $_GET[ "term" ];
    $idtipoprofissional = $_GET['idtipoprofissional'];
    $tipovenda = $_GET['tipovenda'];
    
    #1-Loja, 2-Franquia(site), 3-Derma(App)
    switch($tipovenda){
        case 1:
            $SqlTipo = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = $idtipoprofissional";
            $resultTipo = $banco->Execute($SqlTipo);
            $rsValor = $banco->ArrayData($resultTipo);
            $valor = $rsValor['valor'];
            $valor_relatorio = $rsValor['valor'];
            break;
        case 2:
            if($idtipoprofissional == 1){
                $valor = "valor_app";
                $valor_relatorio = "valor_app";
            }else{
                $valor = "valor_profissional";
                $valor_relatorio = "valor_profissional";
            }
            break;
        case 3:
            $valor = "valor_app";
            $valor_relatorio = "valor_profissional";
            break;
    }
   
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
                         'valor' => number_format(floatval($rs[$valor]), 2, ',', '.'),
        				 'valor_real' => floatval($rs[$valor]),
                         'valor_relatorio' => floatval($rs[$valor_relatorio]),
                    );
    }
    
    $SqlTipo = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = $idtipoprofissional";
    $resultTipo = $banco->Execute($SqlTipo);
    $rsValor = $banco->ArrayData($resultTipo);
    $valor = $rsValor['valor'];
    
    $SqlKit = 'SELECT * FROM t_kit WHERE (nome LIKE "%'.$term.'%" OR codigo LIKE "%'.$term.'%") AND ativo = 1 ORDER BY nome';
    $resultKit = $banco->Execute($SqlKit);
    
    while($rsKit = $banco->ArrayData($resultKit)){
        $SqlImagemKit = "SELECT caminho FROM t_imagens_kit WHERE ordem = 1 AND idkit = {$rsKit['idkit']}";
        $resultImagemKit = $banco->Execute($SqlImagemKit);
        $rsImagemKit = $banco->ArrayData($resultImagemKit);
        $array[] = array('label' => 'Kit: '.utf8_encode($rsKit['nome'] . ' - Estoque: ' . $bancoKit->CalculaEstoqueKit($rsKit['idkit']) . ' UN'),
                         'value' => 'Kit: '.utf8_encode($rsKit['nome']),
                         'idproduto' => 'kit_'.$rsKit['idkit'],
                         'caminho' => UrlFoto.$rsImagemKit['caminho'],
                         'valor' => number_format(floatval($rsKit[$valor]), 2, ',', '.'),
        				 'valor_real' => floatval($rsKit[$valor]),
                         'valor_relatorio' => floatval($rsKit[$valor]),
                    );
    }
    
    echo json_encode($array);
 ?>