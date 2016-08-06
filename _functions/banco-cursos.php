<?php
	class bancocursos extends banco{
		
		#
		function MontaCursos(){
			$Sql = "SELECT * FROM t_cursos";
			$result = parent::Execute($Sql);
			$Auxilio = parent::CarregaHtml('itens/lista-curso-itens');
			while($rs = parent::ArrayData($result)){
				$Linha = $Auxilio;
				$Linha = str_replace('<%NOMECURSO%>', $rs['nome'], $Linha);
				if($rs['data_ini'] == $rs['data_fim']){
					$data_format = date("d/m/Y", strtotime($rs['data_ini']));
				}else{
					$data_format = date("d/m/Y", strtotime($rs['data_ini'])) . " até " . date("d/m/Y", strtotime($rs['data_fim']));
				}
				if($rs['hora_ini'] == $rs['hora_fim']){
					$hora_format = date("H:i", strtotime($rs['hora_ini']));
				}else{
					$hora_format = date("H:i", strtotime($rs['hora_ini'])) . " às " . date("H:i", strtotime($rs['hora_fim'])); 
				}
				$Linha = str_replace('<%DATAFORMATADA%>', $data_format, $Linha);
				$Linha = str_replace('<%HORAFORMATADA%>', $hora_format, $Linha);
				$Linha = str_replace('<%DESCRICAO%>', $rs['descricao'], $Linha);
				if($rs['carga_horaria'] == 0){
					$carga_horaria = '';
				}else{
					$carga_horaria = "<p><span>Carga Horária: ". $rs['carga_horaria'] ." horas</span></p>";
				}
				$Linha = str_replace('<%CARGAHORARIA%>', $carga_horaria, $Linha);
				if($rs['investimento'] == '0.00'){
					$investimento = '';
				}else{
					$investimento = "<p><span>Investimento: R$ ". number_format($rs['investimento'], 2, ',', '.') ."</span></p>";
				}
				$Linha = str_replace('<%INVESTIMENTO%>', $investimento, $Linha);
				$Linha = str_replace('<%IDCURSO%>', $rs['idcurso'], $Linha);
				$Cursos .= $Linha;
			}
			return utf8_encode($Cursos);
		}
		
		#BuscaNomeCurso
		function BuscaNomeCurso($idcurso){
			$Sql = "SELECT nome FROM t_cursos WHERE idcurso = $idcurso";
			$result = parent::Execute($Sql);
			$rs = parent::ArrayData($result);
			return $rs['nome'];
		}
	}
?>