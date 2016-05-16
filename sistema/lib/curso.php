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
		$data = $rs['data'];
		$hora = $rs['hora'];
		$nome = utf8_encode($rs['nome']);
		$descricao = utf8_encode($rs['descricao']);
		$vagas = utf8_encode($rs['vagas']);
		
		$titulo = 'Edita Curso';
	}elseif($this->PaginaAux[0] == 'excluir'){
		$idcurso = $this->PaginaAux[1];
		$banco->Excluir($idcurso);
		$banco->RedirecionaPara('lista-cursos');
	}
	
	if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
		$nome = utf8_decode(strip_tags(trim(addslashes($_POST["nome"]))));
		$data = $_POST['data'];
		$hora = $_POST['hora'];
		$descricao = utf8_decode(strip_tags(trim(addslashes($_POST["descricao"]))));
		$vagas = $_POST['vagas'];
	
		if($idcurso){
			$banco->AtualizaCurso($idcurso, $nome, $data, $hora, $descricao, $vagas);
		}else{
			$banco->InsereCurso($nome, $data, $hora, $descricao, $vagas);
		}
	}
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('curso'));
	$Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
	$Conteudo = str_replace("<%NOME%>", $nome, $Conteudo);
	$Conteudo = str_replace("<%DATA%>", $data, $Conteudo);
	$Conteudo = str_replace("<%HORA%>", $hora, $Conteudo);
	$Conteudo = str_replace("<%DESCRICAO%>", $descricao, $Conteudo);
	$Conteudo = str_replace("<%VAGAS%>", $vagas, $Conteudo);
	$Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
	$Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
	$Conteudo = str_replace("<%BOTAOSALVAR%>", $botao_salvar, $Conteudo);
	$Conteudo = str_replace("<%MSG%>", utf8_encode($msg), $Conteudo);
?>