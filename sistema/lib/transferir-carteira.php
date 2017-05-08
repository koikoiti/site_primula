<?php
	$msg = '';

	#include das funcoes da tela
	include('functions/banco-carteira.php');
	
	#Instancia o objeto
	$banco = new bancocarteira();
		 
	if($_POST){
		$func1 = $_POST['func'][0];
		$func2 = $_POST['func'][1];
		$transfere = $banco->TransfereCarteira($func1, $func2);
		if($transfere < 0){
			$msg = utf8_encode('<div class="alert alert-warning alert-dismissible" role="alert">
                            <strong>Atenção!</strong> Para transferir a carteira, o usuário de destino não deve possuir nenhum cliente na carteira</div>');
		}else{
			$msg = utf8_encode('<div class="alert alert-success alert-dismissible" role="alert">
                            <strong>OK!</strong> '.$transfere.' clientes transferidos</div>');
		}
	}
	
	$select_funcionarios = $banco->SelectFuncionarios();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('transferir-carteira'));
	$Conteudo = str_replace('<%SELECTFUNCIONARIOS%>', $select_funcionarios, $Conteudo);
	$Conteudo = str_replace('<%MSG%>', $msg, $Conteudo);
?>