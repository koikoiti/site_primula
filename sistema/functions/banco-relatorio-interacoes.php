<?php
	class bancorelatoriointeracoes extends banco{
		
		#Monta relatório interações
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
			}
			$Auxilio = utf8_encode(parent::CarregaHtml('itens/relatorio-interacoes-itens'));
			$Sql = "SELECT * FROM t_clientes_historico H
					INNER JOIN t_clientes C ON H.idcliente = C.idcliente 
					WHERE 1 $where 
					ORDER BY data ASC";
			$result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			if($num_rows){
				while($rs = parent::ArrayData($result)){
					$whereVendas = '';
					if($dataIni){
						#$newdataIni = implode("-", array_reverse(explode("/", $dataIni)));
						$whereVendas .= " AND data >= '$dataIni 00:00:00'";
					}
					if($dataFim){
						#$newdataFim = implode("-", array_reverse(explode("/", $dataFim)));
						$whereVendas .= " AND data <= '$dataFim 23:59:59'";
					}
					if($idresponsavel){
						$whereVendas .= " AND idusuario = '$idresponsavel'";
					}
					$SqlVendas = "SELECT COUNT(*) AS vendas FROM t_vendas WHERE idcliente = " . $rs['idcliente'] . $whereVendas . " AND orcamento = 0";
					$resultVendas = parent::Execute($SqlVendas);
					$rsVendas = parent::ArrayData($resultVendas);
					$SqlOrcamentos = "SELECT COUNT(*) AS orcamentos FROM t_vendas WHERE idcliente = " . $rs['idcliente'] . $whereVendas . " AND orcamento = 1";
					$resultOrcamentos = parent::Execute($SqlOrcamentos);
					$rsOrcamentos = parent::ArrayData($resultOrcamentos);
					$Linha = $Auxilio;
					$Linha = str_replace("<%IDCLIENTE%>", $rs['idcliente'], $Linha);
					$Linha = str_replace("<%CLIENTE%>", utf8_encode($rs['nome']), $Linha);
					$Linha = str_replace("<%RESPONSAVEL%>", utf8_encode($rs['usuario']), $Linha);
					$Linha = str_replace("<%DATAINTERACAO%>", date("d/m/Y - H:i", strtotime($rs['data'])), $Linha);
					$Linha = str_replace("<%VENDAS%>", $rsVendas['vendas'], $Linha);
					$Linha = str_replace("<%ORCAMENTOS%>", $rsOrcamentos['orcamentos'], $Linha);
					$retorno .= $Linha;
				}
			}else{
				$retorno = '';
			}
			return $retorno;
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