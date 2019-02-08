<?php
	class bancoscylla extends banco{
		
	    function arrumaValoresVendasProdutos(){
	        $Sql = "SELECT * FROM t_vendas_produtos P
                    INNER JOIN t_vendas V ON P.idvenda = V.idvenda";
	        $result = parent::Execute($Sql);
	        while($rs = parent::ArrayData($result)){
	            $auxPK = explode("_", $rs['produto_kit']);
	            if($auxPK[0] == 'prod'){
	                $SqlValorProduto = "SELECT * FROM t_produtos WHERE idproduto = {$auxPK[1]}";
	                $resultValorProduto = parent::Execute($SqlValorProduto);
	                $rsValorProduto = parent::ArrayData($resultValorProduto);
	            }else{
	                $SqlValorKit = "SELECT * FROM t_kit WHERE idkit = {$auxPK[1]}";
	                $resultValorKit = parent::Execute($SqlValorKit);
	                $rsValorKit = parent::Linha($resultValorKit);
	            }
	            switch($rs['idtipovenda']){
	                       #LOJA - 
	                case 1:
	                    
	                    break;
	                case 2:
	                    break;
	                case 3:
	                    break;
	            }
	        }
	    }
	    
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
		
		function ArrumaValorApp(){
		    $Sql = "SELECT idproduto, valor_app, campo_status, valor_profissional, valor_consumidor FROM t_produtos WHERE marca = 'Bioage'";
		    $result = parent::Execute($Sql);
		    while($rs = parent::ArrayData($result)){
		        if($rs['campo_status'] == "Profissional"){
		            $newValorApp = $rs['valor_profissional'];
		        }elseif($rs['campo_status'] == "Home Care"){
		            $newValorApp = ceil($rs['valor_consumidor'] + $rs['valor_consumidor'] * 0.05);
		        }
		        
		        $SqlUpdate = "UPDATE t_produtos SET valor_app = '$newValorApp' WHERE idproduto = {$rs['idproduto']}";
		        $resultUpdate = parent::Execute($SqlUpdate);
		    }
		    echo "Done;";
		}
	}
?>