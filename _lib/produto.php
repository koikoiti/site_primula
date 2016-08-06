<?php
	#include das funcoes da tela lista-produto
	include('_functions/banco-produto.php');

	#Instancia o objeto
	$banco = new bancoproduto();
    
	$Categorias = $banco->ListaCategorias();
	$idproduto = $this->PaginaAux[0];
	
	$rsProduto = $banco->BuscaProdutoPorId($idproduto); 
	$fotos = $banco->MontaFotosProdutoUnico($idproduto);
	$semelhantes = $banco->MontaSemelhantes($rsProduto['idcategoria']);
	
	if($_POST){
		$nome = utf8_decode(strip_tags(trim(addslashes($_POST['nome']))));
		$email = strtolower(utf8_decode(strip_tags(trim(addslashes($_POST['email'])))));
		$telefone = utf8_decode(strip_tags(trim(addslashes($_POST['telefone']))));
		
		$texto_email = $banco->CarregaHtml('mail-orcamento-produto');
		$texto_email = str_replace('<%DATA%>', date("d/m/Y H:i:s"), $texto_email);
		$texto_email = str_replace('<%NOME%>', $nome, $texto_email);
		$texto_email = str_replace('<%EMAIL%>', $email, $texto_email);
		$texto_email = str_replace('<%TELEFONE%>', $telefone, $texto_email);
		$texto_email = str_replace('<%PRODUTO%>', $rsProduto['nome'], $texto_email);
		$texto_email = str_replace('<%CODIGO%>', $rsProduto['cod_barras'], $texto_email);
		
		if($banco->enviaEmail('Prímula', 'primulatkc@primulatkc.com.br', '[Site Prímula] - Orçamento de Produto', $texto_email, '')){
			echo utf8_encode('<script type="text/javascript">alert("Sua solicitação de orçamento foi enviada com sucesso! Em breve enviaremos o orçamento para o e-mail informado.");</script>');
		}
	}
	
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('produto'));
	$Conteudo = str_replace('<%NOMEPRODUTO%>', utf8_encode($rsProduto['nome']), $Conteudo);
	$Conteudo = str_replace('<%MARCA%>', utf8_encode($rsProduto['marca']), $Conteudo);
	$Conteudo = str_replace('<%CODIGO%>', utf8_encode($rsProduto['cod_barras']), $Conteudo);
	$Conteudo = str_replace('<%DESCRICAO%>', utf8_encode($rsProduto['descricao']), $Conteudo);
	$Conteudo = str_replace('<%NOMECATEGORIA%>', utf8_encode($rsProduto['categoria']), $Conteudo);
	$Conteudo = str_replace('<%PRECO%>', number_format($rsProduto['valor_consumidor'], 2, ',', '.'), $Conteudo);
	$Conteudo = str_replace('<%FOTOS%>', $fotos, $Conteudo);
	$Conteudo = str_replace('<%SEMELHANTES%>', $semelhantes, $Conteudo);
	$Conteudo = str_replace('<%CATEGORIAS%>', $Categorias, $Conteudo);
	$Conteudo = str_replace('<%IDCATEGORIA%>', $rsProduto['idcategoria'], $Conteudo);
?>