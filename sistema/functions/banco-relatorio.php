<?php
	class bancorelatorio extends banco{
		
		#Monta o relatório
		function MontaRelatorio($dataIni, $dataFim, $idresponsavel){
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
			
			$Sql = "SELECT C.nome, V.idvenda, V.data, V.valor_frete, V.valor_venda, V.idusuario FROM t_vendas V 
					INNER JOIN t_clientes C ON V.idcliente = C.idcliente 
					WHERE 1 $where";
			
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
				$total_vendas += $rs['valor_venda'];
				$total_fretes += $rs['valor_frete'];
				$total_vendas_sem_frete += $rs['valor_venda'] - $rs['valor_frete'];
				
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
			$Sql = "SELECT * FROM t_usuarios WHERE ativo = 1 AND login <> 'admin' ORDER BY nome_exibicao";
			$select_usuarios = "<select id='busca_responsavel' style='float: left; width: 25%;' class='form-control' name='busca_responsavel'>";
			$select_usuarios .= "<option selected value=''>Responsável</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idusuario'] == $idresponsavel){
						$select_usuarios .= "<option selected value='".$rs['idusuario']."'>".$rs['nome_exibicao']."</option>";
					}else{
						$select_usuarios .= "<option value='".$rs['idusuario']."'>".$rs['nome_exibicao']."</option>";
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