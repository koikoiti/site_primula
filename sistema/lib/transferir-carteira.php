<?php
	$msg = '';

	#include das funcoes da tela
	include('functions/banco-carteira.php');
	
	#Instancia o objeto
	$banco = new bancocarteira();
		 
	if($_POST){
		$funcDe = $_POST['funcDe'];
		$funcPara = $_POST['funcPara'];
		$arrClientes = $_POST['arrClientes'];
		$transfere = $banco->TransfereClientes($funcDe, $funcPara, $arrClientes);
		
		if($transfere){
			$msg = utf8_encode('<div class="alert alert-success alert-dismissible" role="alert">
                            <strong>OK!</strong> Clientes transferidos com sucesso!</div>');
		}
	}
	
	$select_funcionarios_de = $banco->SelectFuncionariosDe();
	$select_funcionarios_para = $banco->SelectFuncionariosPara();
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('transferir-carteira'));
	$Conteudo = str_replace('<%SELECTFUNCIONARIOSDE%>', $select_funcionarios_de, $Conteudo);
	$Conteudo = str_replace('<%SELECTFUNCIONARIOSPARA%>', $select_funcionarios_para, $Conteudo);
	$Conteudo = str_replace('<%MSG%>', $msg, $Conteudo);
?>