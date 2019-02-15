<?php
	class bancoscylla extends banco{
		
	    function arrumaValoresVendasProdutos(){
	        $Sql = "SELECT P.*, V.*, C.idtipoprofissional FROM t_vendas_produtos P
                    INNER JOIN t_vendas V ON P.idvenda = V.idvenda
                    INNER JOIN t_clientes C ON V.idcliente = C.idcliente
                    WHERE P.valor_venda = 0 OR P.valor_relatorio = 0";
	        $result = parent::Execute($Sql);
	        while($rs = parent::ArrayData($result)){
	            #idtipoprofissional 13 = Verificar
	            if($rs['idtipoprofissional'] != 13){
    	            switch($rs['idtipovenda']){
    	                case 1:
    	                    #Loja
    	                    $SqlTipo = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = " . $rs['idtipoprofissional'];
    	                    $resultTipo = parent::Execute($SqlTipo);
    	                    $rsValor = parent::ArrayData($resultTipo);
    	                    $valor = $rsValor['valor'];
    	                    $valor_relatorio = $rsValor['valor'];
    	                    break;
    	                case 2:
    	                    #Franquia
    	                    if($rs['idtipoprofissional'] == 1){
    	                        $valor = "valor_app";
    	                        $valor_relatorio = "valor_app";
    	                    }else{
    	                        $valor = "valor_profissional";
    	                        $valor_relatorio = "valor_profissional";
    	                    }
    	                    break;
    	                case 3:
    	                    #Derma
    	                    $valor = "valor_app";
    	                    $valor_relatorio = "valor_profissional";
    	                    break;
    	            }
    	            
    	            $auxPK = explode("_", $rs['produto_kit']);
    	            if($auxPK[0] == 'prod'){
    	                $SqlValorProduto = "SELECT * FROM t_produtos WHERE idproduto = {$auxPK[1]}";
    	                $resultValorProduto = parent::Execute($SqlValorProduto);
    	                $rsValorProduto = parent::ArrayData($resultValorProduto);
    	                $valor_venda_reais = $rsValorProduto[$valor];
    	                $valor_relatorio_reais = $rsValorProduto[$valor_relatorio];
    	            }else{
    	                $SqlTipo = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = " . $rs['idtipoprofissional'];
    	                $resultTipo = parent::Execute($SqlTipo);
    	                $rsValor = parent::ArrayData($resultTipo);
    	                $valor = $rsValor['valor'];
    	                $valor_relatorio = $rsValor['valor'];
    	                
    	                $SqlValorKit = "SELECT * FROM t_kit WHERE idkit = {$auxPK[1]}";
    	                $resultValorKit = parent::Execute($SqlValorKit);
    	                $rsValorKit = parent::ArrayData($resultValorKit);
    	                
    	                $valor_venda_reais = $rsValorKit[$valor];
    	                $valor_relatorio_reais = $rsValorKit[$valor_relatorio];
    	            }
    	            
    	            #UPDATE
    	            $SqlUpdate = "UPDATE t_vendas_produtos SET valor_venda = $valor_venda_reais, valor_relatorio = $valor_relatorio_reais WHERE idvendaproduto = " . $rs['idvendaproduto'];
    	            parent::Execute($SqlUpdate);
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