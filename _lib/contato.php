<?php
	#include das funcoes da tela inico
	include('_functions/banco-contato.php');

	#Instancia o objeto
	$banco = new bancocontato();
    
	if($_POST){
		$nome = utf8_decode(strip_tags(trim(addslashes($_POST['nome']))));
		$email = strtolower(utf8_decode(strip_tags(trim(addslashes($_POST['email'])))));
		$mensagem = utf8_decode(strip_tags(trim(addslashes($_POST['mensagem']))));
		
		$texto_email = $banco->CarregaHtml('mail-contato');
		$texto_email = str_replace('<%DATA%>', date("d/m/Y H:i:s"), $texto_email);
		$texto_email = str_replace('<%NOME%>', $nome, $texto_email);
		$texto_email = str_replace('<%EMAIL%>', $email, $texto_email);
		$texto_email = str_replace('<%MENSAGEM%>', $mensagem, $texto_email);		
		
		if($banco->enviaEmail('Prímula', 'primulatkc@primulatkc.com.br', '[Site Prímula] - Contato', $texto_email, '')){
			echo utf8_encode('<script type="text/javascript">alert("Recebemos sua mensagem com sucesso! Aguarde que em breve entraremos em contato.")</script>');
		}
	}
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('contato'));
?>