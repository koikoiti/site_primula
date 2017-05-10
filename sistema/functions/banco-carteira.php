<?php
	class bancocarteira extends banco{
		
		function TransfereClientes($funcDe, $funcPara, $arrClientes){
			foreach($arrClientes as $idcliente){
				$Sql = "UPDATE t_usuarios_carteira_clientes SET idusuario = $funcPara WHERE idusuario = $funcDe AND idcliente = $idcliente";
				parent::Execute($Sql);
			}
			return true;
		}
		
		function SelectFuncionariosDe(){
			$Sql = "SELECT * FROM t_usuarios";
			$result = parent::Execute($Sql);
			$select = '<select onchange="listaDe();" id="funcDe" name="funcDe" class="form-control"><option value="">Selecione um funcionário</option>';
			while($rs = parent::ArrayData($result)){
				if($rs['ativo'] == 0){
					$inativo = " (Inativo)";
				}else{
					$inativo = '';
				}
				$select .= "<option value='{$rs['idusuario']}'>{$rs['nome_exibicao']} $inativo</option>";
			}
			$select .= "</select>";
			return utf8_encode($select);
		}
		
		function SelectFuncionariosPara(){
			$Sql = "SELECT * FROM t_usuarios";
			$result = parent::Execute($Sql);
			$select = '<select onchange="listaPara();" id="funcPara" name="funcPara" class="form-control"><option value="">Selecione um funcionário</option>';
			while($rs = parent::ArrayData($result)){
				if($rs['ativo'] == 0){
					$inativo = " (Inativo)";
				}else{
					$inativo = '';
				}
				$select .= "<option value='{$rs['idusuario']}'>{$rs['nome_exibicao']} $inativo</option>";
			}
			$select .= "</select>";
			return utf8_encode($select);
		}
		
		#OLD - não usado - carteira toda
		function TransfereCarteira($func1, $func2){
			$Sql = "SELECT * FROM t_usuarios_carteira_clientes WHERE idusuario = $func2";
			$result = parent::Execute($Sql);
			$linha = parent::Linha($result);
			if($linha){
				return -1;
			}else{
				$SqlTransfere = "UPDATE t_usuarios_carteira_clientes SET idusuario = $func2 WHERE idusuario = $func1";
				parent::Execute($SqlTransfere);
				return mysql_affected_rows();
			}
		}
	}
?>