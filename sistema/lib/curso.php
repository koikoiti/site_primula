<?php
	$titulo = "Novo Curso";
	$botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
	$botao_salvar = '<button type="submit" class="btn btn-success btn-flat"><i class="fa fa-check"></i> Salvar</button>';
	
	#include das funcoes da tela
	include('functions/banco-cursos.php');

	#Instancia o objeto
	$banco = new bancocursos();
    
	if($this->PaginaAux[0] == 'editar'){
		$idcurso = $this->PaginaAux[1];
		$rs = $banco->BuscaCursoPorId($idcurso);
		$dataIni = $rs['data_ini'];
		$dataFim = $rs['data_fim'];
		$horaIni = $rs['hora_ini'];
		$horaFim = $rs['hora_fim'];
		$nome = utf8_encode($rs['nome']);
		$descricao = utf8_encode($rs['descricao']);
		$carga = utf8_encode($rs['carga_horaria']);
		$investimento = $rs['investimento'];
		
		$titulo = 'Edita Curso';
	}elseif($this->PaginaAux[0] == 'excluir'){
		$idcurso = $this->PaginaAux[1];
		$banco->Excluir($idcurso);
		$banco->RedirecionaPara('lista-cursos');
	}
	
	if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
		$nome = utf8_decode(strip_tags(trim(addslashes($_POST["nome"]))));
		$dataIni = $_POST['dataIni'];
		$dataFim = $_POST['dataFim'];
		$horaIni = $_POST['horaIni'];
		$horaFim = $_POST['horaFim'];
		$descricao = utf8_decode(strip_tags(trim(addslashes($_POST["descricao"]))));
		$carga = $_POST['carga'];
		$investimento = $_POST['investimento'];
		$investimento = str_replace('.', '', $investimento);
		$investimento = str_replace(',', '.', $investimento);
	
		if($idcurso){
			$banco->AtualizaCurso($idcurso, $nome, $dataIni, $dataFim, $horaIni, $horaFim, $descricao, $carga, $investimento);
		}else{
			$banco->InsereCurso($nome, $dataIni, $dataFim, $horaIni, $horaFim, $descricao, $carga, $investimento);
		}
	}
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('curso'));
	$Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
	$Conteudo = str_replace("<%NOME%>", $nome, $Conteudo);
	$Conteudo = str_replace("<%DATAINI%>", $dataIni, $Conteudo);
	$Conteudo = str_replace("<%DATAFIM%>", $dataFim, $Conteudo);
	$Conteudo = str_replace("<%HORAINI%>", $horaIni, $Conteudo);
	$Conteudo = str_replace("<%HORAFIM%>", $horaFim, $Conteudo);
	$Conteudo = str_replace("<%DESCRICAO%>", $descricao, $Conteudo);
	$Conteudo = str_replace("<%CARGA%>", $carga, $Conteudo);
	$Conteudo = str_replace("<%INVESTIMENTO%>", $investimento, $Conteudo);
	$Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
	$Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
	$Conteudo = str_replace("<%BOTAOSALVAR%>", $botao_salvar, $Conteudo);
	$Conteudo = str_replace("<%MSG%>", utf8_encode($msg), $Conteudo);
?>