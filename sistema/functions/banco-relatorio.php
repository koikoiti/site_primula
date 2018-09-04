<?php
	class bancorelatorio extends banco{
		
		#Select cidades clientes
		function MontaCidadesClientes($cidadeUF){
			$Sql = "SELECT DISTINCT UPPER(C.cidade) AS cidade , UPPER(C.estado) AS estado 
					FROM t_clientes C 
					INNER JOIN t_vendas V ON V.idcliente = C.idcliente 
					WHERE V.orcamento = 0 
					ORDER BY C.cidade ASC";
			$result = parent::Execute($Sql);
			if($result){
				$select_cidade = "<select id='busca_cidade' style='float: left; width: auto;' class='form-control' name='busca_cidade'>";
				$select_cidade .= "<option selected value=''>Cidade / UF</option>";
				while($rs = parent::ArrayData($result)){
					if(utf8_encode($rs['cidade'] . "*_*" . $rs['estado']) == $cidadeUF){
						$select_cidade .= "<option selected value='".$rs['cidade']."*_*".$rs['estado']."'>".$rs['cidade']." / ".$rs['estado']."</option>";
					}else{
						$select_cidade .= "<option value='".$rs['cidade']."*_*".$rs['estado']."'>".$rs['cidade']." / ".$rs['estado']."</option>";
					}
				}
				$select_cidade .= "</select>";
			}else{
				return false;
			}
			return utf8_encode($select_cidade);
		}			
		
		#Select tipo pagamento
		function MontaSelectTipoPagamento($idtipopagamento){
			$select_pgto = "<select id='busca_pgto' style='float: left; width: 10%;' class='form-control' name='busca_pgto'>";
			$select_pgto .= "<option selected value=''>Todos Pgtos</option>";
			$Sql = "SELECT * FROM fixo_forma_pagamento ORDER BY forma_pagamento ASC";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				if($rs['idformapagamento'] == $idtipopagamento){
					$select_pgto .= "<option selected value='".$rs['idformapagamento']."'>".$rs['forma_pagamento']."</option>";
				}else{
					$select_pgto .= "<option value='".$rs['idformapagamento']."'>".$rs['forma_pagamento']."</option>";
				}
			}
			$select_pgto .= "</select>";
			return utf8_encode($select_pgto);
		}
		
		#Monta o relatório
		function MontaRelatorio($dataIni, $dataFim, $idresponsavel, $marca, $idtipopagamento, $cidade){
			$Auxilio = parent::CarregaHtml('itens/relatorio-itens');
			$where = '';
			if($dataIni){
				#$newdataIni = implode("-", array_reverse(explode("/", $dataIni)));
				$where .= " AND V.data >= '$dataIni 00:00:00'";
			}
			if($dataFim){
				#$newdataFim = implode("-", array_reverse(explode("/", $dataFim)));
				$where .= " AND V.data <= '$dataFim 23:59:59'";
			}
			if($idresponsavel){
				$where .= " AND V.idusuario = $idresponsavel";
			}
			
			if($idtipopagamento){
				$where .= " AND Y.idformapagamento = $idtipopagamento";
			}
			
			if($cidade){
				$auxCidade = explode("*_*", $cidade);
				$bd_cidade = utf8_decode($auxCidade[0]);
				$bd_estado = utf8_decode($auxCidade[1]);
				$where .= " AND C.cidade LIKE '%$bd_cidade%' AND C.estado LIKE '%$bd_estado%'";
			}
			
			if($marca){
				$SqlMarcaProduto = "SELECT idproduto FROM t_produtos WHERE marca LIKE '%$marca%'";
				$resultMarcaProduto = parent::Execute($SqlMarcaProduto);
				$linhaMarcaProduto = parent::Linha($resultMarcaProduto);
				if($linhaMarcaProduto){
					$where .= " AND (";
					while($rsMarcaProduto = parent::ArrayData($resultMarcaProduto)){
						$where .= " X.produto_kit = 'prod_" . $rsMarcaProduto['idproduto'] . "' OR";
					}
				}
				$SqlMarcaKit = "SELECT idkit FROM t_kit WHERE marca LIKE '%$marca%'";
				$resultMarcaKit = parent::Execute($SqlMarcaKit);
				$linhaMarcaKit = parent::Linha($resultMarcaKit);
				if($linhaMarcaKit){
					while($rsMarcaKit = parent::ArrayData($resultMarcaKit)){
						$where .= " X.produto_kit = 'kit_" . $rsMarcaKit['idkit'] . "' OR";
					}
				}
				$where = rtrim($where, " OR");
				$where .= ")";
				$Sql = "SELECT DISTINCT C.nome, C.idtipoprofissional, V.idvenda, V.data, V.valor_frete, V.valor_venda, V.idusuario, V.desconto_subtotal 
						FROM t_vendas V
						INNER JOIN t_clientes C ON V.idcliente = C.idcliente
						LEFT JOIN t_vendas_produtos X ON V.idvenda = X.idvenda
						LEFT JOIN t_vendas_pagamentos Y ON Y.idvenda = V.idvenda
						WHERE 1 $where AND V.orcamento = 0 AND X.brinde = 0 AND V.valor_venda <> 0 GROUP BY idvenda";
			}else{
				$Sql = "SELECT DISTINCT C.nome, V.idvenda, V.data, V.valor_frete, V.valor_venda, V.idusuario FROM t_vendas V 
						INNER JOIN t_clientes C ON V.idcliente = C.idcliente
						LEFT JOIN t_vendas_pagamentos Y ON Y.idvenda = V.idvenda
						WHERE 1 $where AND V.orcamento = 0";
			}
			
			$quantidade_vendas = 0;
			$total_vendas_sem_frete = 0;
			$total_fretes = 0;
			$total_vendas = 0;
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				$Linha = $Auxilio;
				$Linha = str_replace("<%NUMERO%>", $rs['idvenda'], $Linha);
				$Linha = str_replace("<%DATA%>", date("d/m/Y", strtotime($rs['data'])), $Linha);
				$Linha = str_replace("<%CLIENTE%>", utf8_encode($rs['nome']), $Linha);
				if($marca){
					#Tipo do valor (consumidor/profissional)
					$SqlValor = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = {$rs['idtipoprofissional']}";
					$resultValor = parent::Execute($SqlValor);
					$rsValor = parent::ArrayData($resultValor);
					#Busca os valores dos produtos com a marca
					$SqlProdutosdaVenda = "SELECT produto_kit, quantidade, desconto_valor FROM t_vendas_produtos WHERE idvenda = {$rs['idvenda']} AND brinde = 0";
					$resultProdutosdaVenda = parent::Execute($SqlProdutosdaVenda);
					
					$valor_venda_unit = 0;
					while($rsProdutosdaVenda = parent::ArrayData($resultProdutosdaVenda)){
						$auxPK = explode("_", $rsProdutosdaVenda['produto_kit']);
						if($auxPK[0] == 'prod'){
							$SqlValorProduto = "SELECT {$rsValor['valor']} FROM t_produtos WHERE idproduto = {$auxPK[1]} AND marca LIKE '%$marca%'";
							$resultValorProduto = parent::Execute($SqlValorProduto);
							$linhaValorProduto = parent::Linha($resultValorProduto);
							if($linhaValorProduto){
								$rsValorProduto = parent::ArrayData($resultValorProduto);
								$valor_venda_unit += (($rsValorProduto[$rsValor['valor']] - $rsProdutosdaVenda['desconto_valor']) * $rsProdutosdaVenda['quantidade']);
							}
						}else{
							$SqlValorKit = "SELECT {$rsValor['valor']} FROM t_kit WHERE idkit = {$auxPK[1]} AND marca LIKE '%$marca%'";
							$resultValorKit = parent::Execute($SqlValorKit);
							$linhaValorKit = parent::Linha($resultValorKit);
							if($linhaValorKit){
								$rsValorKit = parent::ArrayData($resultValorKit);
								$valor_venda_unit += (($rsValorKit[$rsValor['valor']] - $rsProdutosdaVenda['desconto_valor']) * $rsProdutosdaVenda['quantidade']);
							}
						}
					}
					if(strtolower($marca) == "bioage"){
					   $valor_venda_unit = $valor_venda_unit - $rs['desconto_subtotal'];
					}
					$valor_venda = $valor_venda_unit + $rs['valor_frete'];
					
					$venda_sem_frete = $valor_venda_unit;
					$Linha = str_replace("<%VENDASEMFRETE%>", "R$ " . number_format($valor_venda_unit, 2, ',', '.'), $Linha);
					$Linha = str_replace("<%VALORFRETE%>", "R$ " . number_format($rs['valor_frete'], 2, ',', '.'), $Linha);
					$Linha = str_replace("<%VALORTOTAL%>", "R$ " . number_format($valor_venda, 2, ',', '.'), $Linha);
				}else{
					$valor_venda = $rs['valor_venda'];
					$venda_sem_frete = $rs['valor_venda'] - $rs['valor_frete'];
					$Linha = str_replace("<%VENDASEMFRETE%>", "R$ " . number_format($venda_sem_frete, 2, ',', '.'), $Linha);
					$Linha = str_replace("<%VALORFRETE%>", "R$ " . number_format($rs['valor_frete'], 2, ',', '.'), $Linha);
					$Linha = str_replace("<%VALORTOTAL%>", "R$ " . number_format($rs['valor_venda'], 2, ',', '.'), $Linha);
				}
				$Linha = str_replace("<%RESPONSAVEL%>", parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
				
				#Soma totais
				$quantidade_vendas++;
				$total_vendas += $valor_venda;
				$total_fretes += $rs['valor_frete'];
				$total_vendas_sem_frete += $venda_sem_frete;
				$Relatorio .= $Linha;
			}
			$LinhaTotal = parent::CarregaHtml('itens/relatorio-ultima');
			$LinhaTotal = str_replace("<%QUANTIDADEVENDAS%>", $quantidade_vendas, $LinhaTotal);
			$LinhaTotal = str_replace("<%VALORTOTAL%>", "R$ ".number_format($total_vendas, 2, ',', '.'), $LinhaTotal);
			$LinhaTotal = str_replace("<%TOTALVENDASSEMFRETE%>", "R$ ".number_format($total_vendas_sem_frete, 2, ',', '.'), $LinhaTotal);
			$LinhaTotal = str_replace("<%TOTALFRETE%>", "R$ ".number_format($total_fretes, 2, ',', '.'), $LinhaTotal);
			$Relatorio .= $LinhaTotal;
			
			return $Relatorio;
		}
		
		#Monta usuários select
		function MontaUsuarios($idresponsavel){
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
		}
	}
?>