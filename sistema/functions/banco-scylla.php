<?php
	class bancoscylla extends banco{
		
		function arrumaEstoque(){
			$Sql = "SELECT * FROM t_vendas_produtos";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				#Verifica se é kit ou produto
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
	}
?>