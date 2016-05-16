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
				$Linha = str_replace('<%DATA%>', date("d/m/Y", strtotime($rs['data'])), $Linha);
				$Linha = str_replace('<%HORA%>', $rs['hora'], $Linha);
				$Linha = str_replace('<%DESCRICAO%>', $rs['descricao'], $Linha);
				$Linha = str_replace('<%VAGAS%>', $rs['vagas'], $Linha);
				$Cursos .= $Linha;
			}
			return utf8_encode($Cursos);
		}
	}
?>