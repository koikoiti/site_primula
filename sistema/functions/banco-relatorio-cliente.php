<?php
	class bancorelatoriocliente extends banco{
		
		#Monta relatório com marca
		function MontaRelatorioComMarca($dataIni, $dataFim, $idresponsavel, $marca){
			$Auxilio = parent::CarregaHtml('itens/relatorio-cliente-itens');
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
			$Sql = "SELECT SUM(DISTINCT valor_frete) AS valor_frete, V.idcliente, C.nome, C.idtipoprofissional, V.idvenda, V.data, V.idusuario
					FROM t_vendas V
					INNER JOIN t_clientes C ON V.idcliente = C.idcliente
					INNER JOIN t_vendas_produtos X ON V.idvenda = X.idvenda
					WHERE 1 $where AND V.orcamento = 0 GROUP BY idcliente ORDER BY nome";
			
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
				$venda_sem_frete = $this->BuscaVendaSemFrete($marca, $rs['idcliente'], $dataIni, $dataFim, $idresponsavel);
				$valor_venda = $venda_sem_frete + $rs['valor_frete'];
				$Linha = str_replace("<%VENDASEMFRETE%>", "R$ " . number_format($venda_sem_frete, 2, ',', '.'), $Linha);
				$Linha = str_replace("<%VALORFRETE%>", "R$ " . number_format($rs['valor_frete'], 2, ',', '.'), $Linha);
				$Linha = str_replace("<%VALORTOTAL%>", "R$ " . number_format($valor_venda, 2, ',', '.'), $Linha);
				$Linha = str_replace("<%RESPONSAVEL%>", parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
			
				#Soma totais
				$quantidade_vendas++;
				$total_vendas += $valor_venda;
				$total_fretes += $rs['valor_frete'];
				$total_vendas_sem_frete += $venda_sem_frete;
				$Relatorio .= $Linha;
			}
			$LinhaTotal = parent::CarregaHtml('itens/relatorio-cliente-ultima');
			$LinhaTotal = str_replace("<%QUANTIDADEVENDAS%>", $quantidade_vendas, $LinhaTotal);
			$LinhaTotal = str_replace("<%VALORTOTAL%>", "R$ ".number_format($total_vendas, 2, ',', '.'), $LinhaTotal);
			$LinhaTotal = str_replace("<%TOTALVENDASSEMFRETE%>", "R$ ".number_format($total_vendas_sem_frete, 2, ',', '.'), $LinhaTotal);
			$LinhaTotal = str_replace("<%TOTALFRETE%>", "R$ ".number_format($total_fretes, 2, ',', '.'), $LinhaTotal);
			$Relatorio .= $LinhaTotal;
			
			return $Relatorio;
		}
		
		function BuscaVendaSemFrete($marca, $idcliente, $dataIni, $dataFim, $idresponsavel){
			$where = '';
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
			
			$where .= " AND V.idusuario = $idresponsavel";
			
			$dataIni = $dataIni . " 00:00:00";
			$dataFim = $dataFim . " 23:59:59";
			$Sql = "SELECT DISTINCT V.idvenda, C.idtipoprofissional FROM t_vendas V 
					INNER JOIN t_clientes C ON C.idcliente = V.idcliente 
					INNER JOIN t_vendas_produtos X ON V.idvenda = X.idvenda 
					WHERE 1 $where AND V.idcliente = $idcliente AND V.data >= '$dataIni' AND V.data <= '$dataFim'";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				#Tipo do valor (consumidor/profissional)
				$SqlValor = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = {$rs['idtipoprofissional']}";
				$resultValor = parent::Execute($SqlValor);
				$rsValor = parent::ArrayData($resultValor);
				#Busca os valores dos produtos com a marca
				$SqlProdutosdaVenda = "SELECT produto_kit, quantidade FROM t_vendas_produtos WHERE idvenda = {$rs['idvenda']}";
				$resultProdutosdaVenda = parent::Execute($SqlProdutosdaVenda);
				
				while($rsProdutosdaVenda = parent::ArrayData($resultProdutosdaVenda)){
					$auxPK = explode("_", $rsProdutosdaVenda['produto_kit']);
					if($auxPK[0] == 'prod'){
						$SqlValorProduto = "SELECT {$rsValor['valor']} FROM t_produtos WHERE idproduto = {$auxPK[1]} AND marca LIKE '%$marca%'";
						$resultValorProduto = parent::Execute($SqlValorProduto);
						$linhaValorProduto = parent::Linha($resultValorProduto);
						if($linhaValorProduto){
							$rsValorProduto = parent::ArrayData($resultValorProduto);
							$valor_venda_unit += ($rsValorProduto[$rsValor['valor']] * $rsProdutosdaVenda['quantidade']);
						}
					}else{
						$SqlValorKit = "SELECT {$rsValor['valor']} FROM t_kit WHERE idkit = {$auxPK[1]} AND marca LIKE '%$marca%'";
						$resultValorKit = parent::Execute($SqlValorKit);
						$linhaValorKit = parent::Linha($resultValorKit);
						if($linhaValorKit){
							$rsValorKit = parent::ArrayData($resultValorKit);
							$valor_venda_unit += ($rsValorKit[$rsValor['valor']] * $rsProdutosdaVenda['quantidade']);
						}
					}
				}
			}
			return $valor_venda_unit;
		}
#===============================================================================================================================================
		
		#Monta relatório
		function MontaRelatorio($dataIni, $dataFim, $idresponsavel){
			$Auxilio = parent::CarregaHtml('itens/relatorio-cliente-itens');
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
				
			$Sql = "SELECT SUM(valor_venda) AS valor_venda, SUM(valor_frete) AS valor_frete, C.nome, V.idvenda, V.data, V.idusuario FROM t_vendas V
					INNER JOIN t_clientes C ON V.idcliente = C.idcliente
					WHERE 1 $where AND V.orcamento = 0 GROUP BY nome";
			
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
				$venda_sem_frete = $rs['valor_venda'] - $rs['valor_frete'];
				$Linha = str_replace("<%VENDASEMFRETE%>", "R$ " . number_format($venda_sem_frete, 2, ',', '.'), $Linha);
				$Linha = str_replace("<%VALORFRETE%>", "R$ " . number_format($rs['valor_frete'], 2, ',', '.'), $Linha);
				$Linha = str_replace("<%VALORTOTAL%>", "R$ " . number_format($rs['valor_venda'], 2, ',', '.'), $Linha);
				$Linha = str_replace("<%RESPONSAVEL%>", parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
		
				#Soma totais
				$quantidade_vendas++;
				$total_vendas += $valor_venda;
				$total_fretes += $rs['valor_frete'];
				$total_vendas_sem_frete += $venda_sem_frete;
				$Relatorio .= $Linha;
			}
			$LinhaTotal = parent::CarregaHtml('itens/relatorio-cliente-ultima');
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
			$select_usuarios = "<select id='busca_responsavel' style='float: left; width: 25%;' class='form-control' name='busca_responsavel'>";
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