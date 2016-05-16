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
					$Linha = str_replace('<%DATA%>', date("d/m/Y", strtotime($rs['data'])), $Linha);
					$Linha = str_replace('<%HORA%>', $rs['hora'], $Linha);
					$Linha = str_replace('<%VAGAS%>', $rs['vagas'], $Linha);
					$Cursos .= $Linha;
				}
			}else{
				$Cursos = '<tr class="odd gradeX">
                                <td colspan="5">Não foram encontrados cursos cadastrados.</td>
                             <tr>';
			}
			
			return utf8_encode($Cursos);
		}
		
		#
		function InsereCurso($nome, $data, $hora, $descricao, $vagas){
			$Sql = "INSERT INTO t_cursos (nome, data, hora, descricao, vagas) VALUES ('$nome', '$data', '$hora', '$descricao', '$vagas')";
			$result = parent::Execute($Sql);
			parent::RedirecionaPara('lista-cursos');
		}
		
		#
		function AtualizaCurso($idcurso, $nome, $data, $hora, $descricao, $vagas){
			$Sql = "UPDATE t_cursos SET nome = '$nome', data = '$data', hora = '$hora', descricao = '$descricao', vagas = '$vagas' WHERE idcurso = $idcurso";
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