<?php
	class bancoultimavenda extends banco{
		
		function ListaUltimasVendas($mes){
			if($mes){
				$data = date("Y-m-d 23:59:59", strtotime("-$mes months"));
				$where = "AND V.data >= '$data' AND V.data <> '0000-00-00 00:00:00'";
			}
			$Sql = "SELECT C.nome, V.idvenda, V.data, V.valor_frete, V.valor_venda, V.idusuario FROM t_vendas V
						INNER JOIN t_clientes C ON V.idcliente = C.idcliente
						WHERE 1 $where";
			$result = parent::Execute($Sql);
			$Auxilio = parent::CarregaHtml('itens/lista-ultima-venda-itens');
			while($rs = parent::ArrayData($result)){
				$Linha = $Auxilio;
				$Linha = str_replace("<%NUMERO%>", $rs['idvenda'], $Linha);
				$Linha = str_replace("<%DATA%>", date("d/m/Y", strtotime($rs['data'])), $Linha);
				$Linha = str_replace("<%CLIENTE%>", utf8_encode($rs['nome']), $Linha);
				$Linha = str_replace("<%VALORTOTAL%>", "R$ " . number_format($rs['valor_venda'], 2, ',', '.'), $Linha);
				$Linha = str_replace("<%RESPONSAVEL%>", parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
				$Vendas .= $Linha;
			}
			return $Vendas;
		}
		
		function MontaSelectPeriodo($mes){
			$select = '<select id="busca_mes" class="form-control" style="float: left; width: 20%">
               					<option value="">Selecione um período</option>';
			for($i = 1; $i < 13; $i++){
				if($i == 1){
					$texto = "1 Mês";
				}else{
					$texto = "$i Meses";
				}
				if($mes == $i){
					$select .= '<option selected value="'.$i.'">'.$texto.'</option>';
				}else{
					$select .= '<option value="'.$i.'">'.$texto.'</option>';
				}
			}
			$select .= '</select>';
			return utf8_encode($select);
		}
	}
?>