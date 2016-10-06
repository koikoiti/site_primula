<?php
    class bancovenda extends banco{
    	
    	#Busca venda por id
    	function BuscaVendaPorId($idvenda){
    		$Sql = "SELECT * FROM t_vendas WHERE idvenda = $idvenda";
    		$result = parent::Execute($Sql);
    		$rs = parent::ArrayData($result);
    		return $rs;
    	}
    	
    	#Busca cliente
    	function BuscaCliente($idcliente){
    		$Sql = "SELECT * FROM t_clientes WHERE idcliente = $idcliente";
    		$result = parent::Execute($Sql);
    		$rs = parent::ArrayData($result);
    		return $rs;
    	}
        
        #Tipo frete
        function SelectTipoFrete($idtipofrete){
            $Sql = "SELECT * FROM fixo_tipo_frete ORDER BY tipo ASC";
            $select_frete = "<select required class='form-control' name='tipofrete'>";
			$select_frete .= "<option selected value=''>Tipo do Frete</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idtipofrete'] == $idtipofrete){
						$select_frete .= "<option selected value='".$rs['idtipofrete']."'>".$rs['tipo']."</option>";
					}else{
						$select_frete .= "<option value='".$rs['idtipofrete']."'>".$rs['tipo']."</option>";
					}
				}
				$select_frete .= "</select>";
				return utf8_encode($select_frete);
			}else{
				return false;
			}
        }
        
        #Lista Vendas
        function ListaVendas(){
            $Auxilio = parent::CarregaHtml('Vendas/itens/lista-venda-itens');
            $Sql = "SELECT V.*, C.* FROM t_vendas V 
                    INNER JOIN t_clientes C ON V.idcliente = C.idcliente
                    ";
            $result = parent::Execute($Sql);
            $linha = parent::Linha($result);
            if($linha){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    #$valorTotal = $this->valorTotalVenda($rs['idvenda'], $rs['valor_frete'], $rs['frete_porconta'], $rs['idtipoprofissional']);
                    $Linha = str_replace('<%ID%>', $rs['idvenda'], $Linha);
                    $Linha = str_replace('<%NUMERO%>', str_pad($rs['idvenda'], 5, "0", STR_PAD_LEFT), $Linha);
                    $Linha = str_replace('<%DATA%>', date("d/m/Y H:i", strtotime($rs['data'])), $Linha);
                    $Linha = str_replace('<%CLIENTE%>', $rs['nome'], $Linha);
                    $Linha = str_replace('<%NF%>', $rs['nf'], $Linha);
                    $Linha = str_replace('<%VALOR%>', $rs['valor_venda'], $Linha);
                    $Linha = str_replace('<%VENDIDOPOR%>', parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
                    if($rs['orcamento'] == 1){
                        $auxVO = 'Orçamento';
                        $editar = '<a href="<%URLPADRAO%>venda/editar/'.$rs['idvenda'].'">Editar</a>
                        			<a href="<%URLPADRAO%>venda/excluir/'.$rs['idvenda'].'">Excluir</a>';
                    }else{
                        $auxVO = 'Venda';
                        $editar = '<a target="_blank" href="<%URLPADRAO%>finalizar/'.$rs['idvenda'].'">Reimprimir</a>
                        			<a href="<%URLPADRAO%>venda/cancelar/'.$rs['idvenda'].'">Cancelar</a>';
                    }
                    $Linha = str_replace('<%VENDAORCAMENTO%>', $auxVO, $Linha);
                    $Linha = str_replace('<%EDITAR%>', $editar, $Linha);
                    $Vendas .= $Linha;
                }
            }else{
                $Vendas = '<tr class="odd gradeX">
                                <td colspan="8">Não foi encontrada nenhuma venda.</td>
                             <tr>';
            }
            return utf8_encode($Vendas);
        }
        
        #Calcula valor total da venda
        function valorTotalVenda($idvenda, $frete, $porConta, $idtipoprofissional){
            $SqlValor = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = $idtipoprofissional";
            $resultValor = parent::Execute($SqlValor);
            $rsValor = parent::ArrayData($resultValor);
            $Sql = "SELECT * FROM t_vendas_produtos V 
                    INNER JOIN t_produtos P ON P.idproduto = V.idproduto 
                    WHERE V.idvenda = $idvenda";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $produtos += ($rs[$rsValor['valor']] - $rs['desconto']) * $rs['quantidade'];
            }
            if($porConta == 1){
                $produtos -= $frete;
            }
            return "R$ " . number_format($produtos, 2, ',', '.');
        }
        
        #Insere Venda/Orçamento
        function InsereOrcamento($idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, $orcamento, $arrTipoPagamento, $arrPagamento, $total, $troco_credito){
        	if($orcamento == 1){
        		#Orçamento (Não altera estoque e não vai pro fluxo)
        		
        		if($fretePorConta){
        			$fretePorConta = 1;
        		}else{
        			$fretePorConta = 0;
        		}
        		
        		#troco as credito
        		if($troco_credito){
        			$troco_credito = 1;
        		}else{
        			$troco_credito = 0;
        		}
        		
        		$Sql = "INSERT INTO t_vendas (idcliente, data, idtipofrete, valor_frete, frete_porconta, orcamento, idusuario, valor_venda, troco_credito) VALUES ('$idcliente', '".date("Y-m-d H:i:s")."', '$tipoFrete', '$valorFrete', '$fretePorConta', '$orcamento', '".$_SESSION['idusuario']."', '$total', '$troco_credito')";
        		parent::Execute($Sql);
        		$lastID = mysql_insert_id();
        		
        		#Insere produtos
        		foreach($arrProdutos as $key => $value){
        			if($arrBrinde[$key]){
        				$brinde = 1;
        			}else{
        				$brinde = 0;
        			}
        			$SqlProdutos = "INSERT INTO t_vendas_produtos (idvenda, produto_kit, quantidade, desconto_valor, brinde) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$arrDesconto[$key]}', '$brinde')";
        			parent::Execute($SqlProdutos);
        		}
        		if($arrTipoPagamento){
        			$parcela = 1;
        			foreach($arrTipoPagamento as $key => $value){
        				$pagamento = $arrPagamento[$key];
        				$pagamento = str_replace('.', '', $pagamento);
        				$pagamento = str_replace(',', '.', $pagamento);
        				$SqlPagamento = "INSERT INTO t_vendas_pagamentos (idvenda, idformapagamento, parcela, valor) VALUES ('$lastID', '$value', '$parcela', '$pagamento')";
        				$parcela++;
        				parent::Execute($SqlPagamento);
        			}
        		}
        	}else{
        		#Venda (Altera estoque, add fluxo)@TODO
        		
        		if($fretePorConta){
        			$fretePorConta = 1;
        		}else{
        			$fretePorConta = 0;
        		}
        		
        		#troco as credito
        		if($troco_credito){
        			$troco_credito = 1;
        		}else{
        			$troco_credito = 0;
        		}
        		
        		$Sql = "INSERT INTO t_vendas (idcliente, data, idtipofrete, valor_frete, frete_porconta, orcamento, idusuario, valor_venda, troco_credito) VALUES ('$idcliente', '".date("Y-m-d H:i:s")."', '$tipoFrete', '$valorFrete', '$fretePorConta', '$orcamento', '".$_SESSION['idusuario']."', '$total', '$troco_credito')";
        		parent::Execute($Sql);
        		$lastID = mysql_insert_id();
        		
        		#Insere produtos
        		foreach($arrProdutos as $key => $value){
        			if($arrBrinde[$key]){
        				$brinde = 1;
        			}else{
        				$brinde = 0;
        			}
        			$SqlProdutos = "INSERT INTO t_vendas_produtos (idvenda, produto_kit, quantidade, desconto_valor, brinde) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$arrDesconto[$key]}', '$brinde')";
        			parent::Execute($SqlProdutos);
        		}
        		if($arrTipoPagamento){
        			$parcela = 1;
        			foreach($arrTipoPagamento as $key => $value){
        				$pagamento = $arrPagamento[$key];
        				$pagamento = str_replace('.', '', $pagamento);
        				$pagamento = str_replace(',', '.', $pagamento);
        				$SqlPagamento = "INSERT INTO t_vendas_pagamentos (idvenda, idformapagamento, parcela, valor) VALUES ('$lastID', '$value', '$parcela', '$pagamento')";
        				$parcela++;
        				parent::Execute($SqlPagamento);
        			}
        		}
        	}
                        
            return $lastID;
        }
    }
?>