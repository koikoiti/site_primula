<?php
	#include das funcoes da tela lista-cursos
	include('_functions/banco-cursos.php');

	#Instancia o objeto
	$banco = new bancocursos();
    
	$Cursos = $banco->MontaCursos();
	
	if($_POST){
		$nome = utf8_decode(strip_tags(trim(addslashes($_POST['nome']))));
		$email = strtolower(utf8_decode(strip_tags(trim(addslashes($_POST['email'])))));
		$telefone = utf8_decode(strip_tags(trim(addslashes($_POST['telefone']))));
		$curso = $banco->BuscaNomeCurso($_POST['idcurso']);
		
		$texto_email = $banco->CarregaHtml('mail-interesse-curso');
		$texto_email = str_replace('<%DATA%>', date("d/m/Y H:i:s"), $texto_email);
		$texto_email = str_replace('<%NOME%>', $nome, $texto_email);
		$texto_email = str_replace('<%EMAIL%>', $email, $texto_email);
		$texto_email = str_replace('<%TELEFONE%>', $telefone, $texto_email);
		$texto_email = str_replace('<%CURSO%>', $curso, $texto_email);
	
		if($banco->enviaEmail('Prímula', 'primulatkc@primulatkc.com.br', '[Site Prímula] - Interesse registrado em ' . $curso, $texto_email, '')){
			echo utf8_encode('<script type="text/javascript">alert("Seu interesse em '. $curso .' foi registrado com sucesso! Entraremos em contato em breve pelo e-mail informado.");</script>');
		}
	}
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-cursos'));
	$Conteudo = str_replace('<%CURSOS%>', $Cursos, $Conteudo);
?>