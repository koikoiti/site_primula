<?php
	#include das funcoes da tela lista-kit
	include('_functions/banco-kits.php');

	#Instancia o objeto
	$banco = new bancokit();
    
	$Categorias = $banco->ListaCategorias();
	$idkit = $this->PaginaAux[0];
	
	$rsKit = $banco->BuscaKitPorId($idkit); 
	$fotos = $banco->MontaFotosKit($idkit);
	$produtos = $banco->BuscaProdutosKit($idkit);
	
	if($_POST){
		$nome = utf8_decode(strip_tags(trim(addslashes($_POST['nome']))));
		$email = strtolower(utf8_decode(strip_tags(trim(addslashes($_POST['email'])))));
		$telefone = utf8_decode(strip_tags(trim(addslashes($_POST['telefone']))));
	
		$texto_email = $banco->CarregaHtml('mail-orcamento-kit');
		$texto_email = str_replace('<%DATA%>', date("d/m/Y H:i:s"), $texto_email);
		$texto_email = str_replace('<%NOME%>', $nome, $texto_email);
		$texto_email = str_replace('<%EMAIL%>', $email, $texto_email);
		$texto_email = str_replace('<%TELEFONE%>', $telefone, $texto_email);
		$texto_email = str_replace('<%KIT%>', $rsKit['nome'], $texto_email);
		$texto_email = str_replace('<%CODIGO%>', $rsKit['codigo'], $texto_email);
	
		if($banco->enviaEmail('Prímula', 'primulatkc@primulatkc.com.br', '[Site Prímula] - Orçamento de Kit', $texto_email, '')){
			echo utf8_encode('<script type="text/javascript">alert("Sua solicitação de orçamento foi enviada com sucesso! Em breve enviaremos o orçamento para o e-mail informado.");</script>');
		}
	}
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('kit'));
	$Conteudo = str_replace('<%NOMEKIT%>', utf8_encode($rsKit['nome']), $Conteudo);
	$Conteudo = str_replace('<%CODIGO%>', utf8_encode($rsKit['codigo']), $Conteudo);
	$Conteudo = str_replace('<%DESCRICAO%>', utf8_encode($rsKit['descricao']), $Conteudo);
	$Conteudo = str_replace('<%PRECO%>', number_format($rsKit['valor_consumidor'], 2, ',', '.'), $Conteudo);
	$Conteudo = str_replace('<%FOTOS%>', $fotos, $Conteudo);
	$Conteudo = str_replace('<%PRODUTOS%>', $produtos, $Conteudo);
?>