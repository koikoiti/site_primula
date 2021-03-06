<?php
	class bancorelatoriointeracoes extends banco{
		
		#Monta relat�rio intera��es
		function MontaRelatorioInteracoes($dataIni, $dataFim, $idresponsavel){
			$where = '';
			if($dataIni){
				#$newdataIni = implode("-", array_reverse(explode("/", $dataIni)));
				$where .= " AND data >= '$dataIni 00:00:00'";
			}
			if($dataFim){
				#$newdataFim = implode("-", array_reverse(explode("/", $dataFim)));
				$where .= " AND data <= '$dataFim 23:59:59'";
			}
			if($idresponsavel){
				$responsavel = parent::BuscaUsuarioPorId($idresponsavel);
				$where .= " AND usuario = '$responsavel'";
				$whereNomeResp .= " AND usuario = '$responsavel'";
			}
			$Auxilio = utf8_encode(parent::CarregaHtml('itens/relatorio-interacoes-itens'));
			$Sql = "SELECT *, MAX(data) AS max_data FROM t_clientes_historico H
					INNER JOIN t_clientes C ON H.idcliente = C.idcliente 
					WHERE 1 $where 
					GROUP BY C.idcliente 
					ORDER BY C.nome ASC";
			$result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			$quantidade_clientes = 0;
			$total_interacoes = 0;
			$total_vendas = 0;
			$total_orcamentos = 0;
			if($num_rows){
				while($rs = parent::ArrayData($result)){
					$whereVendas = '';
					if($dataIni){
						#$newdataIni = implode("-", array_reverse(explode("/", $dataIni)));
						$whereVendas .= " AND data >= '$dataIni 00:00:00'";
						$whereInteracoes .= " AND data >= '$dataIni 00:00:00'";
					}
					if($dataFim){
						#$newdataFim = implode("-", array_reverse(explode("/", $dataFim)));
						$whereVendas .= " AND data <= '$dataFim 23:59:59'";
						$whereInteracoes .= " AND data <= '$dataFim 23:59:59'";
					}
					if($idresponsavel){
						$whereVendas .= " AND idusuario = '$idresponsavel'";
						$whereResp .= " AND idusuario = '$idresponsavel'";
					}
					$SqlVendas = "SELECT COUNT(*) AS vendas FROM t_vendas WHERE idcliente = " . $rs['idcliente'] . $whereVendas . " AND orcamento = 0" . $whereResp;
					$resultVendas = parent::Execute($SqlVendas);
					$rsVendas = parent::ArrayData($resultVendas);
					$SqlOrcamentos = "SELECT COUNT(*) AS orcamentos FROM t_vendas WHERE idcliente = " . $rs['idcliente'] . $whereVendas . " AND orcamento = 1" . $whereResp;
					$resultOrcamentos = parent::Execute($SqlOrcamentos);
					$rsOrcamentos = parent::ArrayData($resultOrcamentos);
					$Linha = $Auxilio;
					$Linha = str_replace("<%DATAULTIMAINTERACAO%>", date("d/m/Y H:i", strtotime($rs['max_data'])), $Linha);
					$Linha = str_replace("<%IDCLIENTE%>", $rs['idcliente'], $Linha);
					$Linha = str_replace("<%CLIENTE%>", utf8_encode($rs['nome']), $Linha);
					$Linha = str_replace("<%RESPONSAVEL%>", utf8_encode($rs['usuario']), $Linha);
					$SqlInteracoes = "SELECT COUNT(*) AS total FROM t_clientes_historico WHERE idcliente = " . $rs['idcliente'] . $whereInteracoes . $whereNomeResp;
					$resultInteracoes = parent::Execute($SqlInteracoes);
					$rsInteracoes = parent::ArrayData($resultInteracoes);
					$Linha = str_replace("<%TOTALINTERACOES%>", $rsInteracoes['total'], $Linha);
					$Linha = str_replace("<%VENDAS%>", $rsVendas['vendas'], $Linha);
					$Linha = str_replace("<%ORCAMENTOS%>", $rsOrcamentos['orcamentos'], $Linha);
					$retorno .= $Linha;
					$quantidade_clientes++;
					$total_interacoes += $rsInteracoes['total'];
					$total_vendas += $rsVendas['vendas'];
					$total_orcamentos += $rsOrcamentos['orcamentos'];
				}
				$LinhaTotal = parent::CarregaHtml('itens/relatorio-interacoes-ultima');
				$LinhaTotal = str_replace("<%QUANTIDADECLIENTES%>", $quantidade_clientes, $LinhaTotal);
				$LinhaTotal = str_replace("<%TOTALINTERACOES%>", $total_interacoes, $LinhaTotal);
				$LinhaTotal = str_replace("<%TOTALVENDAS%>", $total_vendas, $LinhaTotal);
				$LinhaTotal = str_replace("<%TOTALORCAMENTOS%>", $total_orcamentos, $LinhaTotal);
				$retorno .= utf8_encode($LinhaTotal);
			}else{
				$retorno = '';
			}
			return $retorno;
		}
		
		#Monta usu�rios select
		function MontaUsuarios($idresponsavel){
			$Sql = "SELECT * FROM t_usuarios WHERE 1 AND login <> 'admin' ORDER BY ativo DESC, nome_exibicao ASC";
			$select_usuarios = "<select id='busca_responsavel' style='float: left; width: 25%;' class='form-control' name='busca_responsavel'>";
			$select_usuarios .= "<option selected value=''>Respons�vel</option>";
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