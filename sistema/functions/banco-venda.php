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
        function ListaVendas($busca_nome, $busca_cnpj, $busca_cpf, $busca_venda){
            $Auxilio = parent::CarregaHtml('Vendas/itens/lista-venda-itens');
            $Sql = "SELECT V.*, C.* FROM t_vendas V 
                    INNER JOIN t_clientes C ON V.idcliente = C.idcliente 
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
                    $Linha = str_replace('<%VALOR%>', 'R$ '.number_format($rs['valor_venda'], 2, ',', '.'), $Linha);
                    $Linha = str_replace('<%VENDIDOPOR%>', parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
                    if($rs['orcamento'] == 1){
                        $auxVO = 'Orçamento';
                        $editar = '<a href="<%URLPADRAO%>venda/editar/'.$rs['idvenda'].'">Editar</a>
                        			<a href="<%URLPADRAO%>venda/excluir/'.$rs['idvenda'].'">Excluir</a>';
                    }else{
                        $auxVO = 'Venda';
                        $editar = '<a target="_blank" href="<%URLPADRAO%>finalizar/'.$rs['idvenda'].'">Reimprimir</a>';
                        if($_SESSION['idsetor'] == 1){
                        	$editar .= '<a href="<%URLPADRAO%>venda/cancelar/'.$rs['idvenda'].'">Cancelar</a>';
                        }
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
        function InsereOrcamento($idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, $orcamento, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs){
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
        		
        		$Sql = "INSERT INTO t_vendas (idcliente, data, idtipofrete, valor_frete, frete_porconta, orcamento, idusuario, valor_venda, troco_credito, obs) VALUES ('$idcliente', '".date("Y-m-d H:i:s")."', '$tipoFrete', '$valorFrete', '$fretePorConta', '$orcamento', '".$_SESSION['idusuario']."', '$total', '$troco_credito', '$obs')";
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
        		
        		$Sql = "INSERT INTO t_vendas (idcliente, data, idtipofrete, valor_frete, frete_porconta, orcamento, idusuario, valor_venda, troco_credito, obs) VALUES ('$idcliente', '".date("Y-m-d H:i:s")."', '$tipoFrete', '$valorFrete', '$fretePorConta', '$orcamento', '".$_SESSION['idusuario']."', '$total', '$troco_credito', '$obs')";
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
        
        function UpdateOrcamento($idvenda, $idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, $orcamento, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs){
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
        				idcliente = '$idcliente', idtipofrete = '$tipoFrete', valor_frete = '$valorFrete', frete_porconta = '$fretePorConta', orcamento = $orcamento, valor_venda = '$total', troco_credito = '$troco_credito', obs = '$obs' 
        				WHERE idvenda = $idvenda";
        		#echo $Sql;die;
        		parent::Execute($Sql);
        		$lastID = $idvenda;
        	
        		#Insere produtos
        		foreach($arrProdutos as $key => $value){
        			if($arrBrinde[$key]){
        				$brinde = 1;
        			}else{
        				$brinde = 0;
        			}
        			$SqlDeleteProdutos = "DELETE FROM t_vendas_produtos WHERE idvenda = $idvenda";
        			parent::Execute($SqlDeleteProdutos);
        			$SqlProdutos = "INSERT INTO t_vendas_produtos (idvenda, produto_kit, quantidade, desconto_valor, brinde) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$arrDesconto[$key]}', '$brinde')";
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
        	
        		$Sql = "UPDATE t_vendas SET 
        				idcliente = '$idcliente', idtipofrete = '$tipoFrete', valor_frete = '$valorFrete', frete_porconta = '$fretePorConta', orcamento = $orcamento, valor_venda = '$total', troco_credito = '$troco_credito', obs = '$obs' 
        				WHERE idvenda = $idvenda";
        		parent::Execute($Sql);
        		$lastID = $idvenda;
        	
        		#Insere produtos
        		foreach($arrProdutos as $key => $value){
        			if($arrBrinde[$key]){
        				$brinde = 1;
        			}else{
        				$brinde = 0;
        			}
        			$SqlDeleteProdutos = "DELETE FROM t_vendas_produtos WHERE idvenda = $idvenda";
        			parent::Execute($SqlDeleteProdutos);
        			$SqlProdutos = "INSERT INTO t_vendas_produtos (idvenda, produto_kit, quantidade, desconto_valor, brinde) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$arrDesconto[$key]}', '$brinde')";
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
        				$SqlPagamento = "INSERT INTO t_vendas_pagamentos (idvenda, idformapagamento, parcela, valor) VALUES ('$lastID', '$value', '$parcela', '$pagamento')";
        				$parcela++;
        				parent::Execute($SqlPagamento);
        			}
        		}
        	}
        	
        	return $lastID;
        }
        
        function ExcluirOrcamento($idvenda){
        	$Sql = "DELETE FROM t_vendas WHERE idvenda = $idvenda";
        	parent::Execute($Sql);
        	parent::RedirecionaPara('lista-venda');
        }
        
        function CancelarVenda($idvenda){
        	$Sql = "DELETE FROM t_vendas WHERE idvenda = $idvenda";
        	parent::Execute($Sql);
        	#@TODO voltar para o estoque
        	parent::RedirecionaPara('lista-venda');
        }
        
        function MontaProdutosEditar($idvenda, $idtipoprofissional){
        	if($idtipoprofissional == 1){
        		$tipoValor = 'valor_consumidor';
        	}else{
        		$tipoValor = 'valor_profissional';
        	}
        	$Sql = "SELECT * FROM t_vendas_produtos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		$auxProd = explode("_", $rs['produto_kit']);
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
        				<div class="col-sm-10 no-padding"><div class="col-sm-11 no-padding"><div class="col-md-12" id="div_produto'.$rs['produto_kit'].'">Produto: <input readonly id="produtonovo'.$rs['produto_kit'].'" type="text" class="form-control produto ui-autocomplete-input" autocomplete="off" value="'.$rsProduto['nome'].'"></div><div class="col-md-2"><br><label>Preço: R$ <span id="preco'.$rs['produto_kit'].'">'.number_format($rsProduto[$tipoValor], 2, ',', '.').'</span></label><input type="hidden" name="hid_valor_real[]" id="hid_valor_real'.$rs['produto_kit'].'" value="'.$rsProduto['valor_profissional'].'"></div><div class="col-md-2">Quantidade: <input onblur="calculaSubtotal();" type="text" class="form-control quantidade" value="'.$rs['quantidade'].'" name="quantidade[]"></div><div class="col-md-2">Desconto R$ (Unitário): <input name="desconto_valor[]" type="text" class="form-control money desconto-valor" onblur="calculaSubtotal();" value="'.$rs['desconto_valor'].'" autocomplete="off"></div><div class="col-md-2"><br><label><input onchange="calculaSubtotal();" name="brinde['.$rs['produto_kit'].']" type="checkbox" '.$cbbrinde.'> Brinde</label></div></div><div class="col-sm-1 no-padding"><div class="col-sm-1"><br><button onclick="menos(\''.$rs['produto_kit'].'\')" type="button" class="btn btn-danger">-</button></div></div></div></div>';
        	}
        	return utf8_encode($retorno);
        }
        
        function MontaPagamentosEditar($idvenda){
        	$Sql = "SELECT * FROM t_vendas_pagamentos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	$linhas = parent::Linha($result);
        	if($linhas){
	        	while($rs = parent::ArrayData($result)){
	        		$retorno .= '<div id="novoPagamentoEdit'.$rs['idvendapagamento'].'" class="col-sm-12" style="margin-top: 5px;">
	        					<div class="col-sm-4">
	        					<select class="form-control" name="tipoPagamento[]">
	        				<option value="1">Dinheiro</option><option value="2">Cheque</option>
	        				<option value="3">Cartão de Débito</option><option value="4">Cartão de Crédito</option>
	        				<option value="5">Boleto</option><option value="6">Depósito Bancário</option>
	        				<option value="7">Crédito a Distância</option></select></div><div class="col-sm-5">
	        				<input type="text" class="form-control money" onblur="calculaTroco();" name="pagamento[]" autocomplete="off" value="'.$rs['valor'].'">
	        				</div>
	        				<button onclick="menosPagamento(\'Edit'.$rs['idvendapagamento'].'\')" type="button" class="btn btn-danger">-</button></div>';
	        	}
        	}else{
        		$retorno = '';
        	}
        	return $retorno;
        }
    }
?>