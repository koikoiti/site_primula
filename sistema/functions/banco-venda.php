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
        
        #Tipo venda
        function SelectTipoVenda($idtipovenda, $flagEditar){
            $Sql = "SELECT * FROM fixo_tipo_venda ORDER BY tipo ASC";
            $select_venda = "<select $flagEditar required class='form-control' name='tipovenda' id='tipovenda'>";
            $select_venda .= "<option selected value=''>- Venda -</option>";
            $result = parent::Execute($Sql);
            if($result){
                while($rs = parent::ArrayData($result)){
                    if($rs['idtipovenda'] == $idtipovenda){
                        $select_venda .= "<option selected value='".$rs['idtipovenda']."'>".$rs['tipo']."</option>";
                    }else{
                        $select_venda .= "<option value='".$rs['idtipovenda']."'>".$rs['tipo']."</option>";
                    }
                }
                $select_venda .= "</select>";
                return utf8_encode($select_venda);
            }else{
                return false;
            }
        }
        
        #Lista Vendas
        function ListaVendas($busca_nome, $busca_cnpj, $busca_cpf, $busca_venda, $busca_dataIni, $busca_dataFim, $busca_responsavel, $busca_pagamento, $busca_procedencia){
            $quantidade_vendas = 0;
            $total_vendas = 0;
            $quantidade_orcamentos = 0;
            $total_orcamentos = 0;
            $Auxilio = parent::CarregaHtml('Vendas/itens/lista-venda-itens');
            $Sql = "SELECT DISTINCT V.*, C.* FROM t_vendas V 
                    INNER JOIN t_clientes C ON V.idcliente = C.idcliente 
                    LEFT JOIN t_vendas_pagamentos P ON P.idvenda = V.idvenda 
            		WHERE 1 
                    ";
            if($busca_nome != ''){
            	$Sql .= " AND (C.nome LIKE '%$busca_nome%' OR C.nome_socio LIKE '%$busca_nome%')";
            }
            if($busca_cpf != ''){
            	$Sql .= " AND (C.cpf LIKE '%$busca_cpf%' OR C.cpf_socio LIKE '%$busca_cpf%')";
            }
            if($busca_cnpj != ''){
            	$Sql .= " AND C.cnpj LIKE '%$busca_cnpj%'";
            }
            if($busca_venda != ''){
            	$Sql .= " AND V.idvenda LIKE '%$busca_venda%'";
            }
            if($busca_dataFim != '' || $busca_dataIni != ''){
            	$Sql .= " AND V.data BETWEEN '$busca_dataIni 00:00:00' AND '$busca_dataFim 23:59:59'";
            }
            if($busca_responsavel){
                $Sql .= " AND V.idusuario = " . $busca_responsavel;
            }
            if($busca_pagamento){
                $Sql .= " AND P.idformapagamento = $busca_pagamento";
            }
            if($busca_procedencia){
                $Sql .= " AND V.idtipovenda = $busca_procedencia";
            }
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
                    $Linha = str_replace('<%IDCLIENTE%>', $rs['idcliente'], $Linha);
                    $Linha = str_replace('<%NF%>', $rs['nf'], $Linha);
                    $Linha = str_replace('<%VALOR%>', 'R$ '.number_format($rs['valor_venda'], 2, ',', '.'), $Linha);
                    $Linha = str_replace('<%VENDIDOPOR%>', parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
                    if($rs['orcamento'] == 1){
                        $quantidade_orcamentos++;
                        $total_orcamentos += $rs['valor_venda'];
                        $auxVO = 'Orçamento';
                        $editar = '<a href="<%URLPADRAO%>venda/editar/'.$rs['idvenda'].'">Editar</a>
                        			<a href="<%URLPADRAO%>venda/excluir/'.$rs['idvenda'].'">Excluir</a>';
                    }else{
                        $quantidade_vendas++;
                        $total_vendas += $rs['valor_venda'];
                        $auxVO = 'Venda';
                        $editar = '<a target="_blank" href="<%URLPADRAO%>finalizar/'.$rs['idvenda'].'">Reimprimir</a>
                        			';
                        if($_SESSION['idsetor'] == 1){
                        	$editar .= '<a href="<%URLPADRAO%>venda/cancelar/'.$rs['idvenda'].'">Cancelar Venda</a>';
                        }
                    }
                    $Pagamentos = "";
                    $SqlPagamentos = "SELECT * FROM t_vendas_pagamentos P 
                                      INNER JOIN fixo_forma_pagamento X ON P.idformapagamento = X.idformapagamento 
                                      WHERE P.idvenda = " . $rs['idvenda'];
                    $resultPagamentos = parent::Execute($SqlPagamentos);
                    $linhaPagamentos = parent::Linha($resultPagamentos);
                    if($linhaPagamentos){
                        while($rsPagamentos = parent::ArrayData($resultPagamentos)){
                            $Pagamentos .= "<small>" . date("d/m/Y", strtotime($rsPagamentos['data'])) . " - {$rsPagamentos['forma_pagamento']} - R$ " . number_format($rsPagamentos['valor'], 2, ",", ".") . "</small><br>";
                        }
                    }
                    $SqlTipoVenda = "SELECT tipo FROM fixo_tipo_venda WHERE idtipovenda = " . $rs['idtipovenda'];
                    $resultTipoVenda = parent::Execute($SqlTipoVenda);
                    $rsTipoVenda = parent::ArrayData($resultTipoVenda);
                    $Linha = str_replace('<%TIPO%>', $rsTipoVenda['tipo'], $Linha);
                    $Linha = str_replace('<%PAGAMENTOS%>', $Pagamentos, $Linha);
                    $Linha = str_replace('<%VENDAORCAMENTO%>', $auxVO, $Linha);
                    $Linha = str_replace('<%EDITAR%>', $editar, $Linha);
                    $Vendas .= $Linha;
                }
            }else{
                $Vendas = '<tr class="odd gradeX">
                                <td colspan="8">Não foi encontrada nenhuma venda.</td>
                             <tr>';
            }
            if($_SESSION['idsetor'] == 1){
                $LinhaTotal = parent::CarregaHtml('Vendas/itens/lista-venda-ultima');
                $LinhaTotal = str_replace("<%QUANTIDADEVENDAS%>", $quantidade_vendas, $LinhaTotal);
                $LinhaTotal = str_replace("<%TOTALVENDAS%>", "R$ ".number_format($total_vendas, 2, ',', '.'), $LinhaTotal);
                $LinhaTotal = str_replace("<%QUANTIDADEORCAMENTOS%>", $quantidade_orcamentos, $LinhaTotal);
                $LinhaTotal = str_replace("<%TOTALORCAMENTOS%>", "R$ ".number_format($total_orcamentos, 2, ',', '.'), $LinhaTotal);
                $Vendas .= $LinhaTotal;
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
        function InsereOrcamento($idcliente, $idtipovenda, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, $orcamento, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs, $arrDataPagamento, $tarifa, $desconto_subtotal){
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
        		
        		$Sql = "INSERT INTO t_vendas (idcliente, idtipovenda, data, idtipofrete, valor_frete, frete_porconta, orcamento, idusuario, valor_venda, troco_credito, obs, tarifa, desconto_subtotal) 
        				VALUES ('$idcliente', '$idtipovenda', '".date("Y-m-d H:i:s")."', '$tipoFrete', '$valorFrete', '$fretePorConta', '$orcamento', '".$_SESSION['idusuario']."', '$total', '$troco_credito', '$obs', '$tarifa', '$desconto_subtotal')";
        		parent::Execute($Sql);
        		$lastID = mysql_insert_id();
        		
        		#Insere produtos
        		foreach($arrProdutos as $key => $value){
        			if($arrBrinde[$key]){
        				$brinde = 1;
        			}else{
        				$brinde = 0;
        			}
        			$descontoProduto = $arrDesconto[$key];
        			$descontoProduto = str_replace('.', '', $descontoProduto);
        			$descontoProduto = str_replace(',', '.', $descontoProduto);
        			$SqlProdutos = "INSERT INTO t_vendas_produtos (idvenda, produto_kit, quantidade, desconto_valor, brinde) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$descontoProduto}', '$brinde')";
        			parent::Execute($SqlProdutos);
        		}
        		if($arrTipoPagamento){
        			$parcela = 1;
        			foreach($arrTipoPagamento as $key => $value){
        				$pagamento = $arrPagamento[$key];
        				$pagamento = str_replace('.', '', $pagamento);
        				$pagamento = str_replace(',', '.', $pagamento);
        				$SqlPagamento = "INSERT INTO t_vendas_pagamentos (idvenda, idformapagamento, parcela, valor, data) VALUES ('$lastID', '$value', '$parcela', '$pagamento', '".$arrDataPagamento[$key]."')";
        				$parcela++;
        				parent::Execute($SqlPagamento);
        			}
        		}
        	}else{
        		#Venda (Altera estoque (OK), add fluxo)@TODO
        		
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
        		
        		$Sql = "INSERT INTO t_vendas (idcliente, idtipovenda, data, idtipofrete, valor_frete, frete_porconta, orcamento, idusuario, valor_venda, troco_credito, obs, tarifa, desconto_subtotal) 
        				VALUES ('$idcliente', '$idtipovenda', '".date("Y-m-d H:i:s")."', '$tipoFrete', '$valorFrete', '$fretePorConta', '$orcamento', '".$_SESSION['idusuario']."', '$total', '$troco_credito', '$obs', '$tarifa', '$desconto_subtotal')";
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
        		
        		#Arruma Estoque
        		$this->arrumaEstoque($lastID);
        		
        		if($arrTipoPagamento){
        			$parcela = 1;
        			foreach($arrTipoPagamento as $key => $value){
        				$pagamento = $arrPagamento[$key];
        				$pagamento = str_replace('.', '', $pagamento);
        				$pagamento = str_replace(',', '.', $pagamento);
        				$SqlPagamento = "INSERT INTO t_vendas_pagamentos (idvenda, idformapagamento, parcela, valor, data) VALUES ('$lastID', '$value', '$parcela', '$pagamento', '".$arrDataPagamento[$key]."')";
        				$parcela++;
        				parent::Execute($SqlPagamento);
        			}
        		}
        	}
                        
            return $lastID;
        }
        
        function UpdateOrcamento($idvenda, $idtipovenda, $idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, $orcamento, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs, $arrDataPagamento, $tarifa, $desconto_subtotal){
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
        	
        		$Sql = "UPDATE t_vendas SET 
        				idtipovenda = '$idtipovenda', idcliente = '$idcliente', idtipofrete = '$tipoFrete', valor_frete = '$valorFrete', frete_porconta = '$fretePorConta', orcamento = $orcamento, valor_venda = '$total', troco_credito = '$troco_credito', obs = '$obs', tarifa = '$tarifa', desconto_subtotal = '$desconto_subtotal' 
        				WHERE idvenda = $idvenda";
        		#echo $Sql;die;
        		parent::Execute($Sql);
        		$lastID = $idvenda;
        		
        		$SqlDeleteProdutos = "DELETE FROM t_vendas_produtos WHERE idvenda = $idvenda";
        		parent::Execute($SqlDeleteProdutos);
        		#Insere produtos
        		foreach($arrProdutos as $key => $value){
        			
        			if($arrBrinde[$key]){
        				$brinde = 1;
        			}else{
        				$brinde = 0;
        			}
        			$descontoProduto = $arrDesconto[$key];
        			$descontoProduto = str_replace('.', '', $descontoProduto);
        			$descontoProduto = str_replace(',', '.', $descontoProduto);
        			$SqlProdutos = "INSERT INTO t_vendas_produtos (idvenda, produto_kit, quantidade, desconto_valor, brinde) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$descontoProduto}', '$brinde')";
        			parent::Execute($SqlProdutos);
        		}
        		if($arrTipoPagamento){
        			$SqlDeletePagamentos = "DELETE FROM t_vendas_pagamentos WHERE idvenda = $idvenda";
        			parent::Execute($SqlDeletePagamentos);
        			$parcela = 1;
        			foreach($arrTipoPagamento as $key => $value){
        				$pagamento = $arrPagamento[$key];
        				$pagamento = str_replace('.', '', $pagamento);
        				$pagamento = str_replace(',', '.', $pagamento);
        				$SqlPagamento = "INSERT INTO t_vendas_pagamentos (idvenda, idformapagamento, parcela, valor, data) VALUES ('$lastID', '$value', '$parcela', '$pagamento', '".$arrDataPagamento[$key]."')";
        				$parcela++;
        				parent::Execute($SqlPagamento);
        			}
        		}
        	}else{
        		#Venda (Altera estoque (OK), add fluxo)@TODO
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
        	
        		$Sql = "UPDATE t_vendas SET 
        				idtipovenda = '$idtipovenda', idcliente = '$idcliente', idtipofrete = '$tipoFrete', valor_frete = '$valorFrete', frete_porconta = '$fretePorConta', orcamento = $orcamento, valor_venda = '$total', troco_credito = '$troco_credito', obs = '$obs', tarifa = '$tarifa', desconto_subtotal = '$desconto_subtotal' 
        				WHERE idvenda = $idvenda";
        		parent::Execute($Sql);
        		$lastID = $idvenda;
        		
        		$SqlDeleteProdutos = "DELETE FROM t_vendas_produtos WHERE idvenda = $idvenda";
        		parent::Execute($SqlDeleteProdutos);
        		
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
        		
        		#Arruma Estoque
        		$this->ArrumaEstoque($idvenda);
        		
        		if($arrTipoPagamento){
        			$SqlDeletePagamentos = "DELETE FROM t_vendas_pagamentos WHERE idvenda = $idvenda";
        			parent::Execute($SqlDeletePagamentos);
        			$parcela = 1;
        			foreach($arrTipoPagamento as $key => $value){
        				$pagamento = $arrPagamento[$key];
        				$pagamento = str_replace('.', '', $pagamento);
        				$pagamento = str_replace(',', '.', $pagamento);
        				$SqlPagamento = "INSERT INTO t_vendas_pagamentos (idvenda, idformapagamento, parcela, valor, data) VALUES ('$lastID', '$value', '$parcela', '$pagamento', '".$arrDataPagamento[$key]."')";
        				$parcela++;
        				parent::Execute($SqlPagamento);
        			}
        		}
        	}
        	
        	return $lastID;
        }
        
        #Arruma Estoque
        function arrumaEstoque($idvenda){
        	$Sql = "SELECT * FROM t_vendas_produtos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		#Verifica se é kit ou produto
        		$auxPK = explode('_', $rs['produto_kit']);
        		if($auxPK[0] == 'prod'){
        			$idproduto = $auxPK[1];
        			$quantidade = $rs['quantidade'];
        			$SqlProduto = "UPDATE t_produtos SET estoque = estoque - $quantidade WHERE idproduto = $idproduto";
        			parent::Execute($SqlProduto);
        		}elseif($auxPK[0] == 'kit'){
        			$idkit = $auxPK[1];
        			$SqlKit = "SELECT idproduto, quantidade FROM t_kit_produtos WHERE idkit = $idkit";
        			$resultKit = parent::Execute($SqlKit);
        			while($rsKit = parent::ArrayData($resultKit)){
        				$SqlProduto = "UPDATE t_produtos SET estoque = estoque - ".$rsKit['quantidade'] * $rs['quantidade'] . " WHERE idproduto = " . $rsKit['idproduto'];
        				parent::Execute($SqlProduto);
        			}
        		}
        	}
        }
        
        function ExcluirOrcamento($idvenda){
        	$Sql = "DELETE FROM t_vendas WHERE idvenda = $idvenda";
        	parent::Execute($Sql);
        	parent::RedirecionaPara('lista-venda');
        }
        
        function CancelarVenda($idvenda){
        	$SqlItens = "SELECT * FROM t_vendas_produtos WHERE idvenda = $idvenda";
        	$resultItens = parent::Execute($SqlItens);
        	while($rsItens = parent::ArrayData($resultItens)){
        		#Verifica se é kit ou produto
        		$auxPK = explode('_', $rsItens['produto_kit']);
        		if($auxPK[0] == 'prod'){
        			$idproduto = $auxPK[1];
        			$quantidade = $rsItens['quantidade'];
        			$SqlProduto = "UPDATE t_produtos SET estoque = estoque + $quantidade WHERE idproduto = $idproduto";
        			parent::Execute($SqlProduto);
        		}elseif($auxPK[0] == 'kit'){
        			$idkit = $auxPK[1];
        			$SqlKit = "SELECT idproduto, quantidade FROM t_kit_produtos WHERE idkit = $idkit";
        			$resultKit = parent::Execute($SqlKit);
        			while($rsKit = parent::ArrayData($resultKit)){
        				$SqlProduto = "UPDATE t_produtos SET estoque = estoque + ".$rsKit['quantidade'] * $rsItens['quantidade'] . " WHERE idproduto = " . $rsKit['idproduto'];
        				parent::Execute($SqlProduto);
        			}
        		}
        	}
        	$Sql = "UPDATE t_vendas SET orcamento = 1 WHERE idvenda = $idvenda";
        	parent::Execute($Sql);
        	parent::RedirecionaPara('lista-venda');
        }
        
        function MontaProdutosEditar($idvenda, $idtipoprofissional, $tipovenda){
            $ret = array();
            
            $Sql = "SELECT * FROM t_vendas_produtos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	$cont = 0;
        	while($rs = parent::ArrayData($result)){
        		$auxProd = explode("_", $rs['produto_kit']);
        		if($auxProd[0] == 'prod'){
        		    #1-Loja, 2-Franquia(site), 3-Derma(App)
        		    switch($tipovenda){
        		        case 1:
        		            if($idtipoprofissional == 1){
        		                $tipoValor = 'valor_consumidor';
        		            }else{
        		                $tipoValor = 'valor_profissional';
        		            }
        		            break;
        		        case 2:
        		            if($idtipoprofissional == 1){
        		                $tipoValor = "valor_app";
        		            }else{
        		                $tipoValor = "valor_profissional";
        		            }
        		            break;
        		        case 3:
        		            $tipoValor = "valor_app";
        		            break;
        		    }
	        		$SqlProduto = "SELECT * FROM t_produtos P INNER JOIN t_imagens_produto I ON I.idproduto = P.idproduto WHERE P.idproduto = " . $auxProd[1] . " AND I.ordem = 1";
	        		$resultProduto = parent::Execute($SqlProduto);
	        		$rsProduto = parent::ArrayData($resultProduto);
	        		#$retorno .= "<div class='col-sm-10 no-padding'><div class='col-sm-11 no-padding'><div class='col-md-12' id='div_produto".$rs['produto_kit']."'>Produto: <input id='produtonovo".$rs['produto_kit']."' type='text' class='form-control produto' /></div><div class='col-md-2'><br/><label>Preço: R$ <span id='preco".$rs['produto_kit']."'></span></label><input type='hidden' name='hid_valor_real[]' id='hid_valor_real".$rsProduto['valor_profissional']."'/></div><div class='col-md-2'>Quantidade: <input onblur='calculaSubtotal();' type='text' class='form-control quantidade' value='".$rs['quantidade']."' name='quantidade[]' /></div><div class='col-md-2'>Desconto R$ (Unitário): <input name='desconto_valor[]' type='text' class='form-control money desconto-valor' onblur='calculaSubtotal();' value='".$rs['desconto_valor']."'/></div><!--div class='col-md-2'>Desconto % (Unitário): <input name='desconto_porcentagem[]' type='text' class='form-control porcentagem' onblur='calculaSubtotal();' value='0'/></div--><div class='col-md-2'><br/><label><input onchange='calculaSubtotal();' name='brinde[".$rs['produto_kit']."]' type='checkbox'/> Brinde</label></div></div><div class='col-sm-1 no-padding'><div class='col-sm-1'><br/><button onclick='menos(".$rs['produto_kit'].")' type='button' class='btn btn-danger'>-</button></div></div></div>";
	        		if($rs['brinde'] == 1){
	        			$cbbrinde = 'checked';
	        		}else{
	        			$cbbrinde = '';
	        		}
	        		$retorno .= '<div id="novo'.$rs['produto_kit'].'" class="novo-produto">
	        						<div id="" class="col-md-2 text-center"><img id="img1" src="'.UrlFoto.$rsProduto['caminho'].'" style="width: 100px; height: 100px;"></div>
	        						<div class="col-sm-10 no-padding"><div class="col-sm-11 no-padding"><div class="col-md-12" id="div_produto'.$rs['produto_kit'].'">Produto: <input readonly id="produtonovo'.$rs['produto_kit'].'" type="text" class="form-control produto ui-autocomplete-input" autocomplete="off" value="'.$rsProduto['nome'].'"></div><div class="col-md-2"><br><label>Preço: R$ <span id="preco'.$rs['produto_kit'].'">'.number_format($rsProduto[$tipoValor], 2, ',', '.').'</span></label><input type="hidden" name="hid_valor_real[]" id="hid_valor_real'.$rs['produto_kit'].'" value="'.$rsProduto[$tipoValor].'"></div><div class="col-md-2">Quantidade: <input onblur="calculaSubtotal();" type="text" class="form-control quantidade" value="'.$rs['quantidade'].'" name="quantidade[]"></div><div class="col-md-2">Desconto R$ (Unitário): <input name="desconto_valor[]" type="text" class="form-control money desconto-valor" onblur="calculaSubtotal();" value="'.number_format($rs['desconto_valor'], 2, ',', '.').'" autocomplete="off"></div><div class="col-md-2"><br><label><input onchange="calculaSubtotal();" name="brinde['.$cont.']" type="checkbox" '.$cbbrinde.'> Brinde</label></div></div><div class="col-sm-1 no-padding"><div class="col-sm-1"><br><button onclick="menos(\''.$rs['produto_kit'].'\')" type="button" class="btn btn-danger">-</button></div></div></div><input type="hidden" name="produtos[]" id="hid_produtoeditar'.$rs['produto_kit'].'" value="'.$rs['produto_kit'].'"></div>';
        		}else{
        		    if($idtipoprofissional == 1){
        		        $tipoValor = 'valor_consumidor';
        		    }else{
        		        $tipoValor = 'valor_profissional';
        		    }
        			$SqlKit = "SELECT * FROM t_kit K INNER JOIN t_imagens_kit I ON I.idkit = K.idkit WHERE K.idkit = " . $auxProd[1] . " AND I.ordem = 1";
        			
        			$resultKit = parent::Execute($SqlKit);
        			$rsKit = parent::ArrayData($resultKit);
        			if($rs['brinde'] == 1){
        				$cbbrinde = 'checked';
        			}else{
        				$cbbrinde = '';
        			}
        			$retorno .= '<div id="novo'.$rs['produto_kit'].'" class="novo-produto">
	        						<div id="" class="col-md-2 text-center"><img id="img1" src="'.UrlFoto.$rsKit['caminho'].'" style="width: 100px; height: 100px;"></div>
	        						<div class="col-sm-10 no-padding"><div class="col-sm-11 no-padding"><div class="col-md-12" id="div_produto'.$rs['produto_kit'].'">Produto: <input readonly id="produtonovo'.$rs['produto_kit'].'" type="text" class="form-control produto ui-autocomplete-input" autocomplete="off" value="'.$rsKit['nome'].'"></div><div class="col-md-2"><br><label>Preço: R$ <span id="preco'.$rs['produto_kit'].'">'.number_format($rsKit[$tipoValor], 2, ',', '.').'</span></label><input type="hidden" name="hid_valor_real[]" id="hid_valor_real'.$rs['produto_kit'].'" value="'.$rsKit[$tipoValor].'"></div><div class="col-md-2">Quantidade: <input onblur="calculaSubtotal();" type="text" class="form-control quantidade" value="'.$rs['quantidade'].'" name="quantidade[]"></div><div class="col-md-2">Desconto R$ (Unitário): <input name="desconto_valor[]" type="text" class="form-control money desconto-valor" onblur="calculaSubtotal();" value="'.number_format($rs['desconto_valor'], 2, ',', '.').'" autocomplete="off"></div><div class="col-md-2"><br><label><input onchange="calculaSubtotal();" name="brinde['.$cont.']" type="checkbox" '.$cbbrinde.'> Brinde</label></div></div><div class="col-sm-1 no-padding"><div class="col-sm-1"><br><button onclick="menos(\''.$rs['produto_kit'].'\')" type="button" class="btn btn-danger">-</button></div></div></div><input type="hidden" name="produtos[]" id="hid_produtoeditar'.$rs['produto_kit'].'" value="'.$rs['produto_kit'].'"></div>';
        		}
        		$cont++;
        	}
        	$ret['HTML'] = utf8_encode($retorno);
        	$ret['cont'] = $cont;
        	return $ret;
        }
        
        function MontaPagamentosEditar($idvenda){
        	$Sql = "SELECT * FROM t_vendas_pagamentos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	$linhas = parent::Linha($result);
        	if($linhas){
	        	while($rs = parent::ArrayData($result)){
	        		switch($rs['idformapagamento']){
	        			case 1:
	        				$selDinheiro = 'selected';
	        				break;
	        			case 2:
	        				$selCheque = 'selected';
	        				break;
	        			case 3:
	        				$selDebito = 'selected';
	        				break;
	        			case 4:
	        				$selCredito = 'selected';
	        				break;
	        			case 5:
	        				$selBoleto = 'selected';
	        				break;
	        			case 6:
	        				$selDeposito = 'selected';
	        				break;
	        			case 7:
	        				$selDistancia = 'selected';
	        				break;
	        			case 8:
	        				$selDebRede = 'selected';
	        				break;
	        			case 9:
	        				$selCredRede = 'selected';
	        				break;
	        		}
	        		$retorno .= '<div id="novoPagamentoEdit'.$rs['idvendapagamento'].'" class="col-sm-12" style="margin-top: 5px;">
	        					<div class="col-sm-4">
	        					<select class="form-control" name="tipoPagamento[]">
	        				<option value="1" '.$selDinheiro.'>Dinheiro</option><option value="2" '.$selCheque.'>Cheque</option>
	        				<option value="3" '.$selDebito.'>Cartão de Débito</option><option value="4" '.$selCredito.'>Cartão de Crédito</option>
	        				<option value="5" '.$selBoleto.'>Boleto</option><option value="6" '.$selDeposito.'>Depósito Bancário</option>
	        				<option value="7" '.$selDistancia.'>Crédito a Distância</option>
	        				<option value="8" '.$selDebRede.'>Cartão de Débito (Rede)</option>
	        				<option value="9" '.$selCredRede.'>Cartão de Crédito (Rede)</option>
        						</select></div>
	        				<div class="col-sm-3">
	        				<input type="text" class="form-control money" onblur="calculaTroco();" name="pagamento[]" autocomplete="off" value="'.$rs['valor'].'">
	        				</div>
	        				<div class="col-sm-3">
	        				<input type="date" class="form-control" name="dataPagamento[]" autocomplete="off" value="'.$rs['data'].'">
	        				</div>
	        				<button onclick="menosPagamento(\'Edit'.$rs['idvendapagamento'].'\')" type="button" class="btn btn-danger">-</button></div>';
	        	}
        	}else{
        		$retorno = '';
        	}
        	return utf8_encode($retorno);
        }
        
        #Monta usuários select
        function MontaUsuarios($idresponsavel){
            if($_SESSION['idsetor'] == 1){
                $Sql = "SELECT * FROM t_usuarios WHERE 1 AND login <> 'admin' ORDER BY ativo DESC, nome_exibicao ASC";
                $select_usuarios = "<select id='busca_responsavel' style='float: left; width: 20%;' class='form-control' name='busca_responsavel'>";
                $select_usuarios .= "<option selected value=''>Responsável</option>";
                $result = parent::Execute($Sql);
                if($result){
                    while($rs = parent::ArrayData($result)){
                        if($rs['ativo'] == 0){
                            $inativo = " (Inativo)";
                        }else{
                            $inativo = '';
                        }
                        if($rs['idusuario'] == $idresponsavel){
                            $select_usuarios .= "<option selected value='".$rs['idusuario']."'>".$rs['nome_exibicao']." $inativo</option>";
                        }else{
                            $select_usuarios .= "<option value='".$rs['idusuario']."'>".$rs['nome_exibicao']."$inativo</option>";
                        }
                    }
                    $select_usuarios .= "</select>";
                    return utf8_encode($select_usuarios);
                }else{
                    return false;
                }
            }else{
                if($idresponsavel){
                    $selected = "selected";
                }
                $select_usuarios = "<select id='busca_responsavel' style='float: left; width: 20%;' class='form-control' name='busca_responsavel'>";
                $select_usuarios .= "<option value=''>Responsável</option>";
                $select_usuarios .= "<option $selected value='{$_SESSION['idusuario']}'>{$_SESSION['nomeexibicao']}</option>";
                $select_usuarios .= "</select>";
                return utf8_encode($select_usuarios);
            }
        }
        
        #Monta pagamentos select
        function MontaPagamentos($idformapagamento){
            $Sql = "SELECT * FROM fixo_forma_pagamento ORDER BY forma_pagamento ASC";
            $result = parent::Execute($Sql);
            $select_pagamentos = "<select id='busca_pagamento' style='float: left; width: 20%;' class='form-control' name='busca_pagamento'>";
            $select_pagamentos .= "<option selected value=''>Pagamento</option>";
            $result = parent::Execute($Sql);
            if($result){
                while($rs = parent::ArrayData($result)){
                    if($rs['idformapagamento'] == $idformapagamento){
                        $select_pagamentos .= "<option selected value='".$rs['idformapagamento']."'>".$rs['forma_pagamento']."</option>";
                    }else{
                        $select_pagamentos .= "<option value='".$rs['idformapagamento']."'>".$rs['forma_pagamento']."</option>";
                    }
                }
                $select_pagamentos .= "</select>";
                return utf8_encode($select_pagamentos);
            }else{
                return false;
            }
        }
        
        #Monta procedencia select
        function MontaProcedencia($idtipovenda){
            $Sql = "SELECT * FROM fixo_tipo_venda ORDER BY tipo ASC";
            $result = parent::Execute($Sql);
            $select_procedencia = "<select id='busca_procedencia' style='float: left; width: 20%;' class='form-control' name='busca_procedencia'>";
            $select_procedencia .= "<option selected value=''>Procedência</option>";
            $result = parent::Execute($Sql);
            if($result){
                while($rs = parent::ArrayData($result)){
                    if($rs['idtipovenda'] == $idtipovenda){
                        $select_procedencia .= "<option selected value='".$rs['idtipovenda']."'>".$rs['tipo']."</option>";
                    }else{
                        $select_procedencia .= "<option value='".$rs['idtipovenda']."'>".$rs['tipo']."</option>";
                    }
                }
                $select_procedencia .= "</select>";
                return utf8_encode($select_procedencia);
            }else{
                return false;
            }
        }
    }
?>