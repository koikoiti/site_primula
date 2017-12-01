<?php
	class bancocarteira extends banco{
		
		function TransfereClientes($funcDe, $funcPara, $arrClientes){
			if($funcDe == 'nao'){
				foreach($arrClientes as $idcliente){
					$Sql = "INSERT INTO t_usuarios_carteira_clientes (idusuario, idcliente) VALUES ($funcPara, $idcliente)";
					parent::Execute($Sql);
				}
			}else{
				foreach($arrClientes as $idcliente){
					$Sql = "UPDATE t_usuarios_carteira_clientes SET idusuario = $funcPara WHERE idusuario = $funcDe AND idcliente = $idcliente";
					parent::Execute($Sql);
				}
			}
			return true;
		}
		
		function SelectFuncionariosDe(){
			$Sql = "SELECT * FROM t_usuarios";
			$result = parent::Execute($Sql);
			$select = '<select onchange="listaDe();" id="funcDe" name="funcDe" class="form-control"><option value="">Selecione um funcion�rio</option>';
			#Sem carteira
			$SqlTotalSem = "SELECT COUNT(*) AS totalSem FROM t_usuarios_carteira_clientes X
						RIGHT JOIN t_clientes C ON X.idcliente = C.idcliente
						WHERE X.idcliente IS NULL";
			$resultTotalSem = parent::Execute($SqlTotalSem);
			$rsTotalSem = parent::ArrayData($resultTotalSem);
			$select .= "<option value='nao'>N�o Inclu�dos (Total: {$rsTotalSem['totalSem']})</option>";
			#Carteira com usu�rio
			while($rs = parent::ArrayData($result)){
				if($rs['ativo'] == 0){
					$inativo = " (Inativo)";
				}else{
					$inativo = '';
				}
				$SqlTotal = "SELECT COUNT(*) AS total FROM t_usuarios_carteira_clientes WHERE idusuario = " . $rs['idusuario'];
				$resultTotal = parent::Execute($SqlTotal);
				$rsTotal = parent::ArrayData($resultTotal);
				$select .= "<option value='{$rs['idusuario']}'>{$rs['nome_exibicao']} (Total: {$rsTotal['total']}) $inativo</option>";
			}
			$select .= "</select>";
			return utf8_encode($select);
		}
		
		function SelectFuncionariosPara(){
			$Sql = "SELECT * FROM t_usuarios";
			$result = parent::Execute($Sql);
			$select = '<select onchange="listaPara();" id="funcPara" name="funcPara" class="form-control"><option value="">Selecione um funcion�rio</option>';
			while($rs = parent::ArrayData($result)){
				if($rs['ativo'] == 0){
					$inativo = " (Inativo)";
				}else{
					$inativo = '';
				}
				$SqlTotal = "SELECT COUNT(*) AS total FROM t_usuarios_carteira_clientes WHERE idusuario = " . $rs['idusuario'];
				$resultTotal = parent::Execute($SqlTotal);
				$rsTotal = parent::ArrayData($resultTotal);
				$select .= "<option value='{$rs['idusuario']}'>{$rs['nome_exibicao']} (Total: {$rsTotal['total']}) $inativo</option>";
			}
			$select .= "</select>";
			return utf8_encode($select);
		}
		
		#OLD - n�o usado - carteira toda
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