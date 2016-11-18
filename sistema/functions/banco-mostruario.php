<?php
	class bancomostruario extends banco{
		
		function ListaMostruario(){
			$Sql = "SELECT * FROM t_mostruario ORDER BY data DESC";
			$result = parent::Execute($Sql);
			$Auxilio = parent::CarregaHtml('itens/lista-mostruario-itens');
			while($rs = parent::ArrayData($result)){
				$Linha = $Auxilio;
				$auxPK = explode("_", $rs['produto_kit']);
				if($auxPK[0] == 'prod'){
					$idproduto = $auxPK[1];
					$SqlPK = "SELECT nome FROM t_produtos WHERE idproduto = $idproduto";
				}elseif($auxPK[0] == 'kit'){
					$idkit = $auxPK[1];
					$SqlPK = "SELECT nome FROM t_kit WHERE idkit = $idkit";
				}
				$resultPK = parent::Execute($SqlPK);
				$rsPK = parent::ArrayData($resultPK);
				$Linha = str_replace('<%PRODUTO%>', $rsPK['nome'], $Linha);
				$Linha = str_replace('<%QUANTIDADE%>', $rs['quantidade'], $Linha);
				$Linha = str_replace('<%USUARIO%>', $rs['usuario'], $Linha);
				$Linha = str_replace('<%DATA%>', date("d/m/Y H:i", strtotime($rs['data'])), $Linha);
				$Linha = str_replace('<%ID%>', $rs['idmostruario'], $Linha);
				$retorno .= $Linha;
			}
			return utf8_encode($retorno);
		}
		
		function VoltarAoEstoque($idmostruario){
			$Sql = "SELECT * FROM t_mostruario WHERE idmostruario = $idmostruario";
			$result = parent::Execute($Sql);
			$rs = parent::ArrayData($result);
			
			$auxPK = explode("_", $rs['produto_kit']);
			if($auxPK[0] == 'prod'){
				$idproduto = $auxPK[1];
				$SqlProduto = "UPDATE t_produtos SET estoque = estoque + ".$rs['quantidade'] . " WHERE idproduto = $idproduto";
				parent::Execute($SqlProduto);
			}elseif($auxPK[0] == 'kit'){
				$idkit = $auxPK[1];
				$SqlKit = "UPDATE t_kit SET estoque = estoque + ".$rs['quantidade']. " WHERE idkit = $idkit";
				parent::Execute($SqlKit);
			}
			$SqlDelete = "DELETE FROM t_mostruario WHERE idmostruario = $idmostruario";
			parent::Execute($SqlDelete);
			parent::RedirecionaPara('lista-mostruario');
		}
	}
?>