<?php
	class bancoscylla extends banco{
		
		function arrumaEstoque(){
			$Sql = "SELECT * FROM t_vendas_produtos";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				#Verifica se � kit ou produto
				$auxPK = explode('_', $rs['produto_kit']);
				if($auxPK[0] == 'prod'){
					$idproduto = $auxPK[1];
					$SqlProduto = "UPDATE t_produtos SET estoque = estoque - ".$rs['quantidade'] . " WHERE idproduto = $idproduto";
					parent::Execute($SqlProduto);
				}elseif($auxPK[0] == 'kit'){
					$idkit = $auxPK[1];
					$SqlKit = "UPDATE t_kit SET estoque = estoque - ".$rs['quantidade']. " WHERE idkit = $idkit";
					parent::Execute($SqlKit);
				}
			}
		}
		
		function ArrumaVendasKit(){
			$Sql = "SELECT produto_kit, quantidade FROM t_vendas_produtos WHERE produto_kit LIKE 'kit_%' ORDER BY produto_kit";
			$result = parent::Execute($Sql);
			$cont = 1;
			while($rs = parent::ArrayData($result)){
				$auxPK = explode('_', $rs['produto_kit']);
				$idkit = $auxPK[1];
				$SqlKit = "SELECT idproduto, quantidade FROM t_kit_produtos WHERE idkit = $idkit";
				$resultKit = parent::Execute($SqlKit);
				while($rsKit = parent::ArrayData($resultKit)){
					$SqlProduto = "UPDATE t_produtos SET estoque = estoque - ".$rsKit['quantidade'] * $rs['quantidade'] . " WHERE idproduto = " . $rsKit['idproduto'];
					#echo $SqlProduto . "<br>";
					parent::Execute($SqlProduto);
				}
				$cont++;
			}
			#echo $cont;
		}
	}
?>