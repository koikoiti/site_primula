<?php
	class bancocursos extends banco{
		
		#
		function ListaCursos(){
			$Auxilio = parent::CarregaHtml('itens/lista-curso-itens');
			$Sql = "SELECT * FROM t_cursos";			
			$result = parent::Execute($Sql);
			$linha = parent::Linha($result);
			if($linha){
				while($rs = parent::ArrayData($result)){
					$Linha = $Auxilio;
					$Linha = str_replace('<%ID%>', $rs['idcurso'], $Linha);
					$Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
					$Linha = str_replace('<%DATAINI%>', date("d/m/Y", strtotime($rs['data_ini'])), $Linha);
					$Linha = str_replace('<%DATAFIM%>', date("d/m/Y", strtotime($rs['data_fim'])), $Linha);
					$Linha = str_replace('<%HORAINI%>', $rs['hora_ini'], $Linha);
					$Linha = str_replace('<%HORAFIM%>', $rs['hora_fim'], $Linha);
					$Linha = str_replace('<%CARGA%>', $rs['carga_horaria'], $Linha);
					$Linha = str_replace('<%INVESTIMENTO%>', number_format($rs['investimento'], 2, ',', '.'), $Linha);
					$Cursos .= $Linha;
				}
			}else{
				$Cursos = '<tr class="odd gradeX">
                                <td colspan="8">Não foram encontrados cursos cadastrados.</td>
                             <tr>';
			}
			
			return utf8_encode($Cursos);
		}
		
		#
		function InsereCurso($nome, $dataIni, $dataFim, $horaIni, $horaFim, $descricao, $carga, $investimento){
			$Sql = "INSERT INTO t_cursos (nome, data_ini, data_fim, hora_ini, hora_fim, descricao, carga_horaria, investimento) VALUES ('$nome', '$dataIni', '$dataFim', '$horaIni', '$horaFim', '$descricao', '$carga', '$investimento')";
			$result = parent::Execute($Sql);
			parent::RedirecionaPara('lista-cursos');
		}
		
		#
		function AtualizaCurso($idcurso, $nome, $dataIni, $dataFim, $horaIni, $horaFim, $descricao, $carga, $investimento){
			$Sql = "UPDATE t_cursos SET nome = '$nome', data_ini = '$dataIni', data_fim = '$dataFim', hora_ini = '$horaIni', hora_fim = '$horaFim', descricao = '$descricao', carga_horaria = '$carga', investimento = '$investimento' WHERE idcurso = $idcurso";
			$result = parent::Execute($Sql);
			parent::RedirecionaPara('lista-cursos');
		}
		
		#Busca curso por ID
		function BuscaCursoPorId($idcurso){
			$Sql = "SELECT * FROM t_cursos WHERE idcurso = $idcurso";
			$result = parent::Execute($Sql);
			return parent::ArrayData($result);
		}
		
		#Excluir curso
		function Excluir($idcurso){
			$Sql = "DELETE FROM t_cursos WHERE idcurso = $idcurso";
			parent::Execute($Sql);
		}
	}
?>