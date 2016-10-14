<?php
	#number_format($rs['valor_unitario'], 2, ',', '.')
    class bancofinalizar extends banco{
        
        function MontaSaida($idvenda){
            #Inicia mPDF
            require_once('app/mpdf60/mpdf.php');
            #Define a default page size/format by array - page will be 190mm wide x 236mm height
            $mpdf = new mPDF('utf-8', array(80, 300), '', '', 5, 5, 8, 8, 0, 0);
                        
            #HTML Auxilio
            $Auxilio = utf8_encode(parent::CarregaHtml('Vendas/saida'));
            
            #Número da venda
            $numero = str_pad($idvenda, 5, "0", STR_PAD_LEFT);
            
            #Sql da venda - geral
            $Sql = "SELECT * FROM t_vendas WHERE idvenda = $idvenda";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            
            #Cliente
            $SqlCliente = "SELECT nome, endereco, numero, bairro, cidade, estado, idtipoprofissional FROM t_clientes WHERE idcliente = " . $rs['idcliente'];
            $resultCliente = parent::Execute($SqlCliente);
            $rsCliente = parent::ArrayData($resultCliente);
            $endereco = $rsCliente['endereco'] . ', ' . $rsCliente['numero'] . ' - ' . $rsCliente['bairro'] . ' - ' . $rsCliente['cidade'] . '/' . $rsCliente['estado'];
            if($rsCliente['idtipoprofissional'] == 1){
            	$valor_produto_cliente = 'valor_consumidor';
            }else{
            	$valor_produto_cliente = 'valor_profissional';
            }
            
            #Produtos
            $Produtos = $this->MontaProdutos($idvenda, $valor_produto_cliente);
            
            #Pagamentos
            $Pagamentos = $this->MontaPagamentos($idvenda);
            
            #Troco
            $Troco = "<tr>
            			<td></td>
            			<td>Troco</td>
            			<td>".number_format(($Pagamentos['total'] - $rs['valor_venda']), 2, ',', '.')."</td>
            		</tr>";
            
            #Por conta
            if($rs['frete_porconta'] == 1){
            	$por_conta = "(Por Conta)";
            }else{
            	$por_conta = '';
            }
            
            #Replaces
            $Auxilio = str_replace('<%PAGAMENTOS%>', $Pagamentos['html'], $Auxilio);
            $Auxilio = str_replace('<%TROCO%>', $Troco, $Auxilio);
            $Auxilio = str_replace('<%FRETE%>', number_format($rs['valor_frete'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%PORCONTA%>', $por_conta, $Auxilio);
            $Auxilio = str_replace('<%PRODUTOS%>', $Produtos['html'], $Auxilio);
            $Auxilio = str_replace('<%DESCONTO%>', number_format($Produtos['desconto'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%TOTALPRODUTOS%>', number_format($Produtos['subtotal'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%TOTALVENDA%>', number_format($rs['valor_venda'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%NOMECLIENTE%>', utf8_encode($rsCliente['nome']), $Auxilio);
            $Auxilio = str_replace('<%ENDERECOCLIENTE%>', utf8_encode($endereco), $Auxilio);
            $Auxilio = str_replace('<%NUMERO%>', $numero, $Auxilio);
            $Auxilio = str_replace('<%DATAIMPRESSAO%>', date("d/m/Y H:i:s"), $Auxilio);
            $Auxilio = str_replace('<%FUNCIONARIO%>', utf8_encode(parent::BuscaUsuarioPorId($rs['idusuario'])), $Auxilio);
            
            $mpdf->WriteHTML($Auxilio);
            
            $actualHeight = $mpdf->y + 8; // Current writing position + a bottom margin in mm
            $mpdf = new mPDF('utf-8', array(80, $actualHeight), '', '', 5, 5, 8, 8, 0, 0);
            
            $mpdf->WriteHTML($Auxilio);
            
            #$mpdf->SetFooter(' ');
            $mpdf->Output();
            exit;
        }
        
        #Monta Produtos
        function MontaProdutos($idvenda, $valor_produto_cliente){
        	$Sql = "SELECT * FROM t_vendas_produtos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		$auxProduto = explode('_', $rs['produto_kit']);
        		$SqlProduto = "SELECT nome, $valor_produto_cliente FROM t_produtos WHERE idproduto = {$auxProduto[1]}";
        		$resultProduto = parent::Execute($SqlProduto);
        		$rsProduto = parent::ArrayData($resultProduto);
        		$Linha = '<tr>';
        		$Linha .= "<td>{$rsProduto['nome']}</td>";
        		$Linha .= "<td>{$rs['quantidade']}</td>";
        		if($rs['brinde'] == 1){
        			$Linha .= "<td>0,00 (B)</td>";
        			$Linha .= "<td>0,00 (B)</td>";
        		}else{
        			$Linha .= "<td>" . number_format($rsProduto[$valor_produto_cliente], 2, ',', '.') . "</td>";
        			$Linha .= "<td>" . number_format($rsProduto[$valor_produto_cliente] * $rs['quantidade'], 2, ',', '.') . "</td>";
        			$Produtos['subtotal'] += $rsProduto[$valor_produto_cliente] * $rs['quantidade'];
        			$Produtos['desconto'] += $rs['desconto_valor'] * $rs['quantidade'];
        		}
        		$Linha .= '</tr>';
        		$Produtos['html'] .= utf8_encode($Linha);
        	}
        	return $Produtos;
        }
        
        #Monta Produtos TXT
        function MontaProdutosTXT($idvenda, $valor_produto_cliente){
        	$Sql = "SELECT * FROM t_vendas_produtos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		$auxProduto = explode('_', $rs['produto_kit']);
        		$SqlProduto = "SELECT nome, $valor_produto_cliente FROM t_produtos WHERE idproduto = {$auxProduto[1]}";
        		$resultProduto = parent::Execute($SqlProduto);
        		$rsProduto = parent::ArrayData($resultProduto);
        		$Linha = '';
        		$Linha .= "{$rsProduto['nome']} \n";
        		$Linha .= "{$rs['quantidade']} x ";
        		if($rs['brinde'] == 1){
        			$Linha .= "0,00 (B) = ";
        			$Linha .= "0,00 (B)";
        		}else{
        			$Linha .= "" . number_format($rsProduto[$valor_produto_cliente], 2, ',', '.') . " = ";
        			$Linha .= "" . number_format($rsProduto[$valor_produto_cliente] * $rs['quantidade'], 2, ',', '.') . "";
        			$Produtos['subtotal'] += $rsProduto[$valor_produto_cliente] * $rs['quantidade'];
        			$Produtos['desconto'] += $rs['desconto_valor'] * $rs['quantidade'];
        		}
        		$Linha .= "\n\n";
        		$Produtos['html'] .= $Linha;
        	}
        	return $Produtos;
        }
        
        #Monta Pagamentos
        function MontaPagamentos($idvenda){
        	$Sql = "SELECT P.*, F.* FROM t_vendas_pagamentos P 
        			INNER JOIN fixo_forma_pagamento F ON P.idformapagamento = F.idformapagamento 
        			WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		$Linha = '<tr>';
        		$Linha .= "<td>{$rs['forma_pagamento']}</td>";
        		$Linha .= "<td>Parcela: {$rs['parcela']}</td>";
        		$Linha .= "<td>" . number_format($rs['valor'], 2, ',', '.') . "</td>";
        		$Linha .= '</tr>';
        		$Pagamentos['html'] .= utf8_encode($Linha);
        		$total += $rs['valor'];
        	}
        	$Pagamentos['html'] .= "<tr><td colspan='3'><hr/></td></tr>
        					<tr>
        						<td>
	        					</td>
        						<td>
        							Total Pago 
	        					</td>
        						<td>
        							" . number_format($total, 2, ',', '.') . "
	        					</td>
        					</tr>";
        	$Pagamentos['total'] = $total;
        	return $Pagamentos;
        }
        
        #Monta Pagamentos TXT
        function MontaPagamentosTXT($idvenda){
        	$Sql = "SELECT P.*, F.* FROM t_vendas_pagamentos P
        	INNER JOIN fixo_forma_pagamento F ON P.idformapagamento = F.idformapagamento
        	WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		$Linha = '';
        		$Linha .= "{$rs['forma_pagamento']} \t";
        		$Linha .= "Parcela: {$rs['parcela']} \t";
        		$Linha .= number_format($rs['valor'], 2, ',', '.');
        		$Linha .= "\n";
        		$Pagamentos['html'] .= ($Linha);
        		$total += $rs['valor'];
        	}
        	$Pagamentos['html'] .= "Total Pago: " . number_format($total, 2, ',', '.');
        	$Pagamentos['total'] = $total;
        	return $Pagamentos;
        }
        
        #Monta saída txt
        function MontaSaidaTxt($idvenda){
        	$fileTXT = fopen($_SERVER['DOCUMENT_ROOT'] . "/sistema/arq/vendas/".$idvenda.".txt", "w");
        	$Auxilio = file_get_contents(UrlPadrao . "html/Vendas/saidaTXT.txt");
        	
        	#Número da venda
        	$numero = str_pad($idvenda, 5, "0", STR_PAD_LEFT);
        	
        	#Sql da venda - geral
        	$Sql = "SELECT * FROM t_vendas WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	$rs = parent::ArrayData($result);
        	
        	#Cliente
        	$SqlCliente = "SELECT nome, endereco, numero, bairro, cidade, estado, idtipoprofissional FROM t_clientes WHERE idcliente = " . $rs['idcliente'];
        	$resultCliente = parent::Execute($SqlCliente);
        	$rsCliente = parent::ArrayData($resultCliente);
        	$endereco = $rsCliente['endereco'] . ', ' . $rsCliente['numero'] . "\n" . $rsCliente['bairro'] . "\n" . $rsCliente['cidade'] . '/' . $rsCliente['estado'];
        	if($rsCliente['idtipoprofissional'] == 1){
        		$valor_produto_cliente = 'valor_consumidor';
        	}else{
        		$valor_produto_cliente = 'valor_profissional';
        	}
        	
        	#Produtos
        	$Produtos = $this->MontaProdutosTXT($idvenda, $valor_produto_cliente);
        	
        	#Pagamentos
        	$Pagamentos = $this->MontaPagamentosTXT($idvenda);
        	
        	#Troco
        	$Troco = "Troco: ".number_format(($Pagamentos['total'] - $rs['valor_venda']), 2, ',', '.');
        	
        	#Por conta
        	if($rs['frete_porconta'] == 1){
        		$por_conta = "(Por Conta)";
        	}else{
        		$por_conta = '';
        	}
        	
        	#Replaces
        	$Auxilio = str_replace('<%PAGAMENTOS%>', $Pagamentos['html'], $Auxilio);
        	$Auxilio = str_replace('<%TROCO%>', $Troco, $Auxilio);
        	$Auxilio = str_replace('<%FRETE%>', number_format($rs['valor_frete'], 2, ',', '.'), $Auxilio);
        	$Auxilio = str_replace('<%PORCONTA%>', $por_conta, $Auxilio);
        	$Auxilio = str_replace('<%PRODUTOS%>', $Produtos['html'], $Auxilio);
        	$Auxilio = str_replace('<%DESCONTO%>', number_format($Produtos['desconto'], 2, ',', '.'), $Auxilio);
        	$Auxilio = str_replace('<%TOTALPRODUTOS%>', number_format($Produtos['subtotal'], 2, ',', '.'), $Auxilio);
        	$Auxilio = str_replace('<%TOTALVENDA%>', number_format($rs['valor_venda'], 2, ',', '.'), $Auxilio);
        	$Auxilio = str_replace('<%NOMECLIENTE%>', ($rsCliente['nome']), $Auxilio);
        	$Auxilio = str_replace('<%ENDERECOCLIENTE%>', ($endereco), $Auxilio);
        	$Auxilio = str_replace('<%NUMERO%>', $numero, $Auxilio);
        	$Auxilio = str_replace('<%DATAIMPRESSAO%>', date("d/m/Y H:i:s"), $Auxilio);
        	$Auxilio = str_replace('<%FUNCIONARIO%>', (parent::BuscaUsuarioPorId($rs['idusuario'])), $Auxilio);
        	fwrite($fileTXT, $Auxilio);
        	parent::RedirecionaPara('arq/vendas/'.$idvenda.".txt");
        }
    }
?>