<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	#Recuperando valores
	$cep = str_replace("-", "", $_POST["cep"]);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$cep/json/");
	$result = curl_exec($ch);
	curl_close($ch);
	
	$obj = json_decode($result);
	
	$logradouro = $obj->logradouro;
	$bairro = $obj->bairro;
	$cidade = $obj->localidade . "/" . $obj->uf;
	
	$retorno = $logradouro . "|" . $bairro . "|" . $cidade;
	
	echo $retorno;
?>