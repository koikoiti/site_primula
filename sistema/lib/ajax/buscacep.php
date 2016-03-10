<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	$banco = new banco;
	$banco->Conecta();
	session_start('login');
	
	#Recuperando valores
	$cep = str_replace("-", "", $_POST["cep"]);
	
	$cURL = curl_init('http://m.correios.com.br/movel/buscaCepConfirma.do');
	$dados = array(
			'cepEntrada' => $cep,
			'tipoCep' => '',
			'cepTemp' => '',
			'metodo' => 'buscarCep'
			);
	#url-ify the data for the POST
	foreach($dados as $key => $value){
		$fields_string .= $key.'='.$value.'&';
	}
	rtrim($fields_string, '&');
	
	curl_setopt($cURL, CURLOPT_POST, true);
	curl_setopt($cURL, CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
	$resultado = curl_exec($cURL);
	curl_close($cURL);
	
	$newlines = array("\t","\n","\r","\0","\x0B");
	$resultado = str_replace($newlines, "", html_entity_decode($resultado));
	if(strpos($resultado, 'Dados nao encontrados') !== false) {
		echo 'NOTFOUND';die;
	}
	
	$startR = strpos($resultado,'<div class="caixacampobranco">');
	$endR = strpos($resultado,'<div style="text-align: right;" class="mopcoes orientacao">',$startR - 59);
	$tableR = substr($resultado,$startR,$endR-$startR);
	#Remove espaços duplicados ou +
	$tableR = preg_replace('/\s+/', ' ',$tableR);
	$tableR = str_replace('<span class="respostadestaque">', '', $tableR);
		
	$expl = explode("</span>", $tableR);
	
	$logradouro = trim($expl[1]);
	$bairro = trim($expl[3]);
	$cidade = str_replace(' /', '/', trim($expl[5]));
	
	$retorno = $logradouro . "|" . $bairro . "|" . $cidade;
	
	echo utf8_encode($retorno);
?>