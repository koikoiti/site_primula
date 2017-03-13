<?php
class bancorelatoriocliente extends banco{

	#Monta o relatório
	function MontaRelatorio($dataIni, $dataFim, $idresponsavel, $marca){
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
			
		if($marca){
			$SqlMarcaProduto = "SELECT idproduto FROM t_produtos WHERE marca LIKE '%$marca%'";
			$resultMarcaProduto = parent::Execute($SqlMarcaProduto);
			$linhaMarcaProduto = parent::Linha($resultMarcaProduto);
			if($linhaMarcaProduto){
				$where .= " AND (";
				while($rsMarcaProduto = parent::ArrayData($resultMarcaProduto)){
					$where .= " X.produto_kit = 'prod_" . $rsMarcaProduto['idproduto'] . "' OR";
				}
				$where = rtrim($where, " OR");
				$where .= ")";
				$Sql = "SELECT DISTINCT SUM(valor_venda) AS soma_venda, SUM(valor_frete) AS soma_frete, C.nome, V.idvenda, V.data, V.valor_frete, V.valor_venda, V.idusuario
						FROM t_vendas V
						INNER JOIN t_clientes C ON V.idcliente = C.idcliente
						INNER JOIN t_vendas_produtos X ON V.idvenda = X.idvenda
						WHERE 1 $where GROUP BY nome ORDER BY nome ASC";
			}
		}else{
			$Sql = "SELECT SUM(valor_venda) AS soma_venda, SUM(valor_frete) AS soma_frete, C.nome, V.idvenda, V.data, V.valor_frete, V.valor_venda, V.idusuario FROM t_vendas V
					INNER JOIN t_clientes C ON V.idcliente = C.idcliente
					WHERE 1 $where GROUP BY nome ORDER BY nome ASC";
		}
		
		$quantidade_vendas = 0;
		$total_vendas_sem_frete = 0;
		$total_fretes = 0;
		$total_vendas = 0;
			
		$result = parent::Execute($Sql);
		while($rs = parent::ArrayData($result)){
			$Linha = $Auxilio;
			$Linha = str_replace("<%CLIENTE%>", utf8_encode($rs['nome']), $Linha);
			$venda_sem_frete = $rs['soma_venda'] - $rs['soma_frete'];
			$Linha = str_replace("<%VENDASEMFRETE%>", "R$ " . number_format($venda_sem_frete, 2, ',', '.'), $Linha);
			$Linha = str_replace("<%VALORFRETE%>", "R$ " . number_format($rs['soma_frete'], 2, ',', '.'), $Linha);
			$Linha = str_replace("<%VALORTOTAL%>", "R$ " . number_format($rs['soma_venda'], 2, ',', '.'), $Linha);
			$Linha = str_replace("<%RESPONSAVEL%>", parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);

			#Soma totais
			$quantidade_vendas++;
			$total_vendas += $rs['soma_venda'];
			$total_fretes += $rs['soma_frete'];
			$total_vendas_sem_frete += $rs['soma_venda'] - $rs['soma_frete'];

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